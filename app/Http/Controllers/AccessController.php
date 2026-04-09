<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccessController extends Controller
{
    public function adminLogin(Request $request)
    {
    	try
        {
            // check user type admin or not
            $user = User::where('user_type_id', 1)
                ->where('role', 'admin')
                ->where('email', $request->email)
                ->first();

            if (!$user) {
                $notification = array(
                    'message' => 'Only Admin can access this page',
                    'alert-type' => 'error'
                );

                return Redirect()->back()->with($notification);
            }
        	$data = $request->all();
		    	if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){

		    		$notification = array(
		                     'message' => 'Successfully Logged In',
		                     'alert-type' => 'success'
		                    );

		           return redirect()->route('dashboard')->with($notification);
		    	} else {
		    		$notification = array(
		                     'message' => 'Username or Password Invalid',
		                     'alert-type' => 'error'
		                    );

		          return Redirect()->back()->with($notification);
	    	}
	   } catch(Exception $e){
            // Log the error
            Log::error('Error in Login: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return Redirect()->back()->with($notification);
        }
    }

    public function Logout()
    {
    	try
    	{
            $redirectUrl = '/';
    		Auth::logout();
    		return redirect($redirectUrl);
    	} catch(Exception $e) {
            // Log the error
            Log::error('Error in Logout: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return Redirect()->back()->with($notification);
        }
    }
    public function passwordChange()
    {
        return view('admin.settings.change_password');
    }
    public function changePassword(Request $request)
    {
        try
        {
            $user = User::findorfail(Auth::user()->id);

            if (!Hash::check($request->current_password, $user->password)) {

                $notification=array(
                    'message' => 'The current password is not matched',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification);
            }

            if ($request->new_password !== $request->confirm_password) {

                $notification=array(
                    'message' => 'The new & confirm password are not matched',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification);
            }

            $user->password = $request->new_password;
            $user->update();


            $notification=array(
                'message' => 'Successfully your has been changed',
                'alert-type' => 'success'
            );

            return redirect()->route('dashboard')->with($notification);

        } catch(Exception $e) {
            // Log the error
            Log::error('Error in changePassword: ', [
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
}
