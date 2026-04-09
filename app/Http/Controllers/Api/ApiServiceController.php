<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\Branch;
use App\Models\Duration;
use App\Models\Experience;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Staff;
use App\Models\StaffService;
use App\Models\StaffWorkingDay;
use App\Models\WorkingDay;
use App\Models\WorkingTimeRange;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiServiceController extends AppBaseController
{
    public function addServices()
    {
        try {
            $branches = Branch::where('status','Active')->get();
            $specialities = Speciality::where('status','Active')->get();
            $experiences = Experience::where('status','Active')->get();

            $workingDays = WorkingDay::where('status','Active')
                ->orderBy('sort_order')
                ->get();
            $workingTimeRanges = WorkingTimeRange::where('status','Active')->get();

            $services = Service::where('status','Active')->get();
            $durations = Duration::where('status','Active')->get();

            return $this->sendResponse([
                'branches' => $branches,
                'specialities' => $specialities,
                'experiences' => $experiences,
                'working_days' => $workingDays,
                'working_time_ranges' => $workingTimeRanges,
                'services' => $services,
                'durations' => $durations,
            ], 'Data retrieved successfully.');
        } catch (Exception $e) {

            // Log the error
            Log::error('Error in SP Login: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
    public function storeService(StaffRequest $request)
    {
        try {
            $user = auth()->user();
            $staffExist = Staff::where('user_id', $user->id)->first();
            if ($staffExist) {
                return $this->sendError('Service already added for this user', 409);
            }

            DB::beginTransaction();

            $staff = Staff::create([
                'user_id' => $user->id,
                'branch_id' => $request->branch_id,
                'specialty_id' => $request->specialty_id,
                'experience_id' => $request->experience_id,
                'working_time_range_id' => $request->working_time_range_id,
                'slot_duration_minutes' => 15,
                'created_by' => $user->id,
            ]);

            $staff->workingDays()->sync($request->working_day_ids);

            foreach($request->services as $service){
                StaffService::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'service_id' => $service['service_id'],
                    'duration_id' => $service['duration_id'],
                    'price' => $service['price'],
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Services added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in store Services: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
    public function updateService(StaffRequest $request)
    {
        try {
            $user = auth()->user();
            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                return $this->sendError('Stuff not added yet.', 404);
            }

            DB::beginTransaction();

            $staff->update([
                'branch_id' => $request->branch_id,
                'specialty_id' => $request->specialty_id,
                'experience_id' => $request->experience_id,
                'working_time_range_id' => $request->working_time_range_id,
                'updated_by' => $user->id,
            ]);

            // Delete working days
            StaffWorkingDay::where('staff_id',$staff->id)->delete();

            // update working days
            $staff->workingDays()->sync($request->working_day_ids);

            // remove old services
            StaffService::where('staff_id',$staff->id)->delete();

            // insert new services
            foreach($request->services as $service) {
                StaffService::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'service_id' => $service['service_id'],
                    'duration_id' => $service['duration_id'],
                    'price' => $service['price'],
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Services updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in update Services: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
    public function showServices()
    {
        try {
            $user = auth()->user();
            $staffExist = Staff::where('user_id', $user->id)->first();
            if (!$staffExist) {
                return $this->sendError('Stuff not added yet.', 409);
            }

            $data = Staff::with([
                'branch',
                'specialty',
                'experience',
                'workingTimeRange',
                'services' => function($query) {
                    $query->with(['service', 'duration']);
                },
                'workingDays'
            ])
                ->where('user_id', $user->id)
                ->get();

            return $this->sendResponse($data,'Services added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in store Services: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
}
