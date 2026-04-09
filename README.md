# Sherazi IT — POS Backend (Senior Laravel Developer Task)

## What Was Fixed & Why

This document explains every performance issue found in the original codebase, how each was fixed, and the measurable impact.

---

## Setup Instructions

```bash
# 1. Clone / extract the project
cd sherazi-pos-task

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure your DB in .env
DB_DATABASE=sherazi_pos
DB_USERNAME=root
DB_PASSWORD=

# 6. Set Redis as cache driver in .env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# 7. Run migrations & seed
php artisan migrate --seed

# 8. Start server
php artisan serve
```

---

## Fixes Applied

### 1. N+1 Query Problem — Eager Loading

**Problem:** The original code called `Product::all()` and then accessed `$product->category` inside a `foreach` loop. With 500 products this fired 501 separate SQL queries — one to get all products, then one per product to fetch its category. Same issue existed in `OrderController::index()` where `$order->customer` and `$order->items` were accessed inside a loop over 200 orders, causing 600+ queries per request.

**Fix:** Used `with()` to eager load all relationships in a single query upfront.

```php
// Before — 501 queries
$products = Product::all();
foreach ($products as $product) {
    $product->category->name; // fires a query every iteration
}

// After — 2 queries
$products = Product::with('category')->paginate(15);
```

**Impact:** `GET /api/products` dropped from 501 queries to 2 queries. `GET /api/orders` dropped from 601 queries to 3 queries.

---

### 2. Inefficient Counting & Aggregation in Dashboard

**Problem:** The dashboard method used `Product::all()->count()` which loaded every single row from the `products` table into PHP memory as Eloquent model objects, just to count them. With 500 products this meant 500 objects allocated in memory for no reason. Same pattern was used for `Order::all()->count()`, `Order::all()->sum()`, and `Product::all()->sortByDesc()->take(5)`.

**Fix:** Pushed all aggregation down to the database level.

```php
// Before — loads all rows into PHP memory
$totalProducts = Product::all()->count();
$topProducts   = Product::all()->sortByDesc('sold_count')->take(5);

// After — single aggregate SQL query, zero model hydration
$totalProducts = Product::count();
$topProducts   = Product::orderByDesc('sold_count')->limit(5)->get();
```

**Impact:** Dashboard went from hydrating 700+ Eloquent objects to executing 5 lightweight aggregate queries.

---

### 3. SQL Injection Vulnerability

**Problem:** `OrderController::filterByStatus()` built a raw SQL query using direct string interpolation from user input. Any value passed as `?status=` was inserted directly into the query string, making the endpoint trivially exploitable.

**Fix:** Replaced the raw query with Eloquent and added strict input validation.

```php
// Before — direct string interpolation, completely unsafe
$status = $request->input('status');
$orders = DB::select("SELECT * FROM orders WHERE status = '$status'");

// After — validated input, parameterised query via Eloquent
$status = $request->validate([
    'status' => 'required|in:pending,completed,cancelled'
])['status'];
$orders = Order::with('customer')->where('status', $status)->paginate(15);
```

---

### 4. Missing DB Transaction on Order Creation

**Problem:** `OrderController::store()` created the order, then looped through items creating `OrderItem` records and decrementing stock. If the process failed halfway (e.g. on the 3rd item out of 5), the order and the first 2 items would be permanently saved in the database with incorrect totals and missing items. There was no rollback mechanism.

Additionally, the original code returned `response()->json()` from inside the `DB::transaction()` closure, which prevents rollback from working correctly — returning an HTTP response object from a transaction does not trigger a rollback on failure.

**Fix:** Wrapped the entire operation in `DB::transaction()` and replaced the `response()->json()` inside with `abort()` so exceptions propagate and trigger automatic rollback.

```php
// Before — partial data saved on failure, response inside transaction
$order = Order::create([...]);
foreach ($request->items as $item) {
    if (!$product) {
        return response()->json(['error' => '...'], 422); // WRONG inside transaction
    }
    OrderItem::create([...]);
}

// After — atomic, all-or-nothing
$order = DB::transaction(function () use ($request) {
    $order = Order::create([...]);
    foreach ($request->items as $item) {
        if (!$product || $product->stock < $item['quantity']) {
            abort(422, 'Product unavailable or insufficient stock'); // triggers rollback
        }
        OrderItem::create([...]);
        $product->decrement('stock', $item['quantity']);
    }
    $order->update(['total_amount' => $totalAmount]);
    return $order;
});
```

---

### 5. Redis Caching on High-Traffic Endpoints

**Problem:** `GET /api/products` and `GET /api/orders` hit the database on every single request with no caching layer. `GET /api/products/dashboard` ran 5 separate aggregate queries on every page load with no caching.

**Fix:** Added `Cache::remember()` with Redis on all three endpoints. Cache keys are page-scoped so pagination works correctly. Cache is invalidated immediately whenever data changes.

```php
// Products list — cached per page for 5 minutes
public function index(Request $request)
{
    $page     = $request->get('page', 1);
    $cacheKey = "products:list:page:{$page}";

    $products = Cache::remember($cacheKey, 300, function () {
        return Product::with('category')->paginate(15);
    });

    return ProductResource::collection($products);
}

// Cache invalidation on create — clears all paginated pages
public function store(Request $request)
{
    $product = Product::create($request->validated());

    for ($i = 1; $i <= 50; $i++) {
        Cache::forget("products:list:page:{$i}");
    }

    return response()->json($product, 201);
}
```

**Impact:** Repeated requests to `GET /api/products` dropped from ~250ms (DB hit) to ~8ms (Redis hit) after the first request warms the cache.

---

### 6. Missing Database Indexes

**Problem:** Three columns were used heavily in `WHERE`, `ORDER BY`, and search queries but had no indexes. `products.sold_count` was sorted on every dashboard load. `orders.status` was filtered on every `filterByStatus` call. `products.name` was scanned with `LIKE` on every search.

**Fix:** Created a dedicated migration to add all missing indexes.

```php
// database/migrations/xxxx_add_performance_indexes.php
Schema::table('products', function (Blueprint $table) {
    $table->index('sold_count');
    $table->index('category_id');
    $table->fullText('name'); // better than plain index for LIKE searches
});

Schema::table('orders', function (Blueprint $table) {
    $table->index('status');
    $table->index('customer_id');
});

Schema::table('order_items', function (Blueprint $table) {
    $table->index('product_id');
});
```

---

### 7. API Resources + Pagination

**Problem:** Controllers returned raw `response()->json($array)` with manually constructed arrays inside `foreach` loops. No pagination was applied — every endpoint returned all records (500 products, 200 orders) in a single response. This caused slow response times, large payloads, and impossible frontend performance.

**Fix:** Created dedicated API Resource classes and applied `paginate(15)` on all list endpoints.

```php
// Before — manual array construction, all records
$result = [];
foreach ($products as $product) {
    $result[] = ['id' => $product->id, 'name' => $product->name, ...];
}
return response()->json($result);

// After — Resource class handles transformation, paginated
return ProductResource::collection(
    Product::with('category')->paginate(15)
);
```

Resources created: `ProductResource`, `OrderResource`, `OrderItemResource`, `CategoryResource`.

**Response now includes pagination metadata:**
```json
{
  "data": [...],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta":  { "current_page": 1, "last_page": 34, "per_page": 15, "total": 500 }
}
```

---

## Before vs After — Query Count & Response Time

<img src="https://i.ibb.co.com/0RrCZ5hQ/Screenshot-7.png" alt="Screenshot 7" border="0">

---

## Bonus Features Added

### Laravel Sanctum Authentication

Protected order creation and product creation routes behind Sanctum token authentication.

```php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/products', [ProductController::class, 'store']);
});
```

Login endpoint returns a token:
```php
URL: /api/login
Method: POST
Credentials:
"login": "admin@gmail.com",
"password": 123456
```

---
