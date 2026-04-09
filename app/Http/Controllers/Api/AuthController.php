<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends AppBaseController
{
    public function register(StaffRequest $request)
    {
        DB::beginTransaction();
        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('image')) {
                $filePath = storeFile($request->file('image'), 'profile_images', 'profileImage_');
            }

            $data = new User();
            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->user_type_id = 3;
            $data->role = 'service_provider';
            $data->image = $filePath;
            $data->password = $request->password;
            $data->status = 'Active';
            $data->save();

            // Generate API token
            $token = $data->createToken('flutter')->plainTextToken;

            DB::commit();

            return $this->sendResponse([
                'success' => true,
                'token' => $token,
                'user' => $data,
            ], 'User created successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in SP Register: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!!!',
            ], 500);
        }
    }
    public function SPLogin(StaffRequest $request)
    {
        try {
            // Rate limiting to prevent brute-force attacks
            $key = 'login_attempts:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return $this->sendError('Too many login attempts. Please try again later.', 429);
            }

            DB::beginTransaction();

            // Find user by phone or email
            $user = User::where('email', $request->login)
                ->orWhere('phone', $request->login)
                ->where('status', "Active")
                ->first();

            // Validate user and password
            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit($key, 60); // Increase failed login count (lockout for 1 minute)
                return $this->sendError('The provided credentials are incorrect.', 401);
            }

            // Reset login attempts after successful login
            RateLimiter::clear($key);

            // Generate API token immediately if OTP is not enabled
            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return $this->sendResponse([
                'token' => $token,
                'user' => $user,
            ], 'Login successful.');

        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in SP Login: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
    public function logout(Request $request)
    {
        try {
            // Ensure the user is authenticated
            if (!$request->user()) {
                return $this->sendError('Unauthorized', 401);
            }

            DB::beginTransaction();

            if ($request->user()) {
                // Delete all tokens for the authenticated user
                $request->user()->tokens()->delete();
            }

            DB::commit();

            return $this->sendSuccess('Logged out successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in Logout: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
}
