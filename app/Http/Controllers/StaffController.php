<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Branch;
use App\Models\Duration;
use App\Models\Experience;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Staff;
use App\Models\StaffService;
use App\Models\StaffWorkingDay;
use App\Models\User;
use App\Models\WorkingDay;
use App\Models\WorkingTimeRange;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
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

                $data = User::with('staff')
                    ->where('user_type_id', 3)
                    ->where('role', 'service_provider')
                    ->select('*')->latest();

                return Datatables::of($data)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('image', function($row){
                        $url = asset($row->image);
                        return '<img src="' . $url . '" alt="Profile Image" loading="lazy" style="height:60px;">';
                    })

                    ->addColumn('email', function($row){
                        return $row->email ?? '';
                    })

                    ->addColumn('phone', function($row){
                        return $row->phone ?? '';
                    })

                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status === 'Active' ? 'active-data' : 'decline-data') . '" id="status-update"  type="checkbox" ' . ($row->status === 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('staffs.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-data" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';

                        if($row->staff){
                            // already staff exists → Edit Services
                            $btn .= ' <a href="'.route('staffs.edit.services',$row->id).'"
                    class="btn btn-warning btn-sm">
                    Edit Services
                </a>';
                        }else{
                            // no staff yet → Add Services
                            $btn .= ' <a href="'.route('staffs.add.services',$row->id).'"
                    class="btn btn-info btn-sm">
                    Add Services
                </a>';
                        }

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%")
                                    ->orWhere('email', 'like', "%{$searchValue}%")
                                    ->orWhere('phone', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['name','image', 'email', 'phone', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.staffs.index');
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
        return view('admin.staffs.create');
    }
    public function store(StaffRequest $request)
    {
        DB::beginTransaction();
        try
        {
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
            $data->status = $request->status;
            $data->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );
            DB::commit();

            return redirect()->route('staffs.index')->with($notification);

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
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.staffs.edit', compact('user'));
    }
    public function edit(Staff $staff)
    {
        //
    }
    public function update(StaffRequest $request, $id)
    {
        try
        {
            $user = User::findOrFail($id);
            if (!$user) {
                $notification = array(
                    'message' => 'User not found',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            // Check if email or phone is being updated to a value that already exists
            $existingEmailUser = User::where('email', $request->email)
                ->where('id', '!=', $id)
                ->first();
            if ($existingEmailUser) {
                $notification = array(
                    'message' => 'The email has already been taken.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }

            $existingPhoneUser = User::where('phone', $request->phone)
                ->where('id', '!=', $id)
                ->first();
            if ($existingPhoneUser) {
                $notification = array(
                    'message' => 'The phone has already been taken.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }

            // Handle file upload
            $filePath = $user->image;
            if ($request->hasFile('image')) {
                $filePath = updateFile($request->file('image'), 'profile_images', 'profileImage_', $user->image);
            }

            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->phone = $request->phone ?? $user->phone;
            $user->image = $filePath;
            $user->status = $request->status ?? $user->status;
            $user->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('staffs.index')->with($notification);

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
    public function destroy(Staff $staff)
    {
        try
        {
            $speciality->delete();
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
    public function staffStatusUpdate(StaffRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = User::findorfail($request->id);
            $data->status = $request->status;
            $data->update();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "User status updated successfully."
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
    public function addServices($id)
    {
        $staff = User::findorfail($id);
        if (!$staff) {
            $notification=array(
                'message' => 'Staff not found.',
                'alert-type' => 'info',
            );

            return redirect()->route('staffs.index')->with($notification);
        }

        $branches = Branch::where('status','Active')->get();
        $specialities = Speciality::where('status','Active')->get();
        $experiences = Experience::where('status','Active')->get();

        $workingDays = WorkingDay::where('status','Active')
            ->orderBy('sort_order')
            ->get();
        $workingTimeRanges = WorkingTimeRange::where('status','Active')->get();

        $services = Service::where('status','Active')->get();
        $durations = Duration::where('status','Active')->get();

        return view('admin.staffs.add_services', compact(
            'staff',
            'services',
            'branches',
            'durations',
            'specialities',
            'experiences',
            'workingDays',
            'workingTimeRanges',
        ));
    }

    public function storeServices(StaffRequest $request, $id)
    {
        try {
            $user = User::findorfail($id);
            if (!$user) {
                $notification=array(
                    'message' => 'Staff not found.',
                    'alert-type' => 'error',
                );
                return redirect()->route('staffs.index')->with($notification);
            }

            $staffExist = Staff::where('user_id', $user->id)->first();
            if ($staffExist) {
                $notification=array(
                    'message' => 'Service already added for this user',
                    'alert-type' => 'error',
                );
                return redirect()->route('staffs.index')->with($notification);
            }

            DB::beginTransaction();

            $staff = Staff::create([
                'user_id' => $user->id,
                'branch_id' => $request->branch_id,
                'specialty_id' => $request->specialty_id,
                'experience_id' => $request->experience_id,
                'working_time_range_id' => $request->working_time_range_id,
                'slot_duration_minutes' => 15,
                'created_by' => Auth::id(),
            ]);

            $staff->workingDays()->sync($request->working_day_ids);

            foreach($request->service_id as $i => $serviceId){
                StaffService::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'service_id' => $serviceId,
                    'duration_id' => $request->duration_id[$i],
                    'price' => $request->price[$i],
                ]);
            }

            DB::commit();

            $notification = array(
                'message' => 'Services added successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('staffs.index')->with($notification);

        } catch(Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in store services: ', [
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

    public function editServices($id)
    {
        $user = User::with([
            'staff.services',
            'staff.workingDays'
        ])->findOrFail($id);

        if(!$user?->staff){
            return redirect()
                ->route('staffs.add.services',$id);
        }

        $staff = $user?->staff;

        $services = Service::where('status','Active')->get();
        $durations = Duration::where('status','Active')->get();
        $workingDays = WorkingDay::where('status','Active')->get();
        $workingTimeRanges = WorkingTimeRange::where('status','Active')->get();
        $branches = Branch::where('status','Active')->get();
        $specialities = Speciality::where('status','Active')->get();
        $experiences = Experience::where('status','Active')->get();

        return view('admin.staffs.edit_services', compact(
            'user','staff','services','durations',
            'workingDays','workingTimeRanges',
            'branches','specialities','experiences'
        ));
    }

    public function updateServices(StaffRequest $request, Staff $staff)
    {
        try {
            DB::beginTransaction();

            if(!$staff){
                $notification=array(
                    'message' => 'Staff record missing.',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }

            // update staff main info
            $staff->update([
                'branch_id' => $request->branch_id,
                'specialty_id' => $request->specialty_id,
                'experience_id' => $request->experience_id,
                'working_time_range_id' => $request->working_time_range_id,
                'updated_by' => Auth::id(),
            ]);

            // Delete working days
            StaffWorkingDay::where('staff_id',$staff->id)->delete();

            // update working days
            $staff->workingDays()->sync($request->working_day_ids);

            // remove old services
            StaffService::where('staff_id',$staff->id)->delete();

            // insert new services
            foreach($request->service_id as $i => $serviceId){
                StaffService::create([
                    'user_id' => $staff->user_id,
                    'staff_id' => $staff->id,
                    'service_id' => $serviceId,
                    'duration_id' => $request->duration_id[$i],
                    'price' => $request->price[$i],
                ]);
            }

            DB::commit();

            $notification=array(
                'message' => 'Service updated successfully.',
                'alert-type' => 'success'
            );

            return redirect()
                ->route('staffs.index')
                ->with($notification);

        } catch(Exception $e) {

            DB::rollBack();
            // Log the error
            Log::error('Error in update services: ', [
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
