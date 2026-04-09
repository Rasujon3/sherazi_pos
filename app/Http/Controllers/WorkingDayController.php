<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkingDayRequest;
use App\Models\WorkingDay;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkingDayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }
    public function index(Request $request)
    {
        try
        {
            if($request->ajax()){

                $data = WorkingDay::orderBy('sort_order')->select('*')->latest();

                return Datatables::of($data)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('sort_order', function($row){
                        return $row->sort_order ?? '';
                    })

                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status === 'Active' ? 'active-data' : 'decline-data') . '" id="status-update"  type="checkbox" ' . ($row->status === 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('workingdays.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-data" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';

                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-data action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%")
                                    ->orWhere('slug', 'like', "%{$searchValue}%")
                                    ->orWhere('status', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['name', 'sort_order', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.workingdays.index');
        } catch(Exception $e) {
            // Log the error
            Log::error('Error in fetching data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!'
            ],500);
        }
    }
    public function create()
    {
        $availableSortOrders = $this->getAvailableSortOrders();

        return view('admin.workingdays.create', compact('availableSortOrders'));
    }
    public function store(WorkingDayRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = new WorkingDay();
            $data->name = $request->name;
            $data->sort_order = $request->sort_order;
            $data->status = $request->status;
            $data->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );
            DB::commit();

            return redirect()->route('workingdays.index')->with($notification);

        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in storing data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function show(WorkingDay $workingday)
    {
        $availableSortOrders = $this->getAvailableSortOrders($workingday->id);

        return view('admin.workingdays.edit', compact('workingday', 'availableSortOrders'));
    }
    public function edit(WorkingDayRequest $request)
    {
        //
    }
    public function update(WorkingDay $workingday, WorkingDayRequest $request)
    {
        try
        {
            $workingday->name = $request->name;
            $workingday->sort_order = $request->sort_order;
            $workingday->status = $request->status;
            $workingday->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('workingdays.index')->with($notification);

        } catch(Exception $e) {
            // Log the error
            Log::error('Error in updating data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function destroy(WorkingDay $workingday)
    {
        try
        {
            $workingday->delete();
            return response()->json([
                'status'=>true,
                'message'=>'Successfully the data has been deleted'
            ]);
        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in deleting data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!'
            ]);
        }
    }
    public function workingdaysStatusUpdate(WorkingDayRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = WorkingDay::findorfail($request->id);
            $data->status = $request->status;
            $data->update();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Experience status updated successfully."
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in updating status: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => "Something went wrong!!!"
            ]);
        }
    }
    private function getAvailableSortOrders(?int $excludeId = null): array
    {
        $allOrders = range(1, 7); // 1, 2, 3, 4, 5, 6, 7
        $usedOrders = WorkingDay::when($excludeId, function ($query, $id) {
            $query->where('id', '!=', $id); // Current record টা বাদ দিন
        })
            ->pluck('sort_order')
            ->toArray();

        // Available orders = All orders - Used orders
        $availableOrders = array_diff($allOrders, $usedOrders);

        return $availableOrders;
    }
}
