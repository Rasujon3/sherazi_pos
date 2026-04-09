<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkingTimeRangeRequest;
use App\Models\WorkingTimeRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkingTimeRangeController extends Controller
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

                $data = WorkingTimeRange::select('*')->latest();

                return Datatables::of($data)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('from_time', function($row){
                        return $row->from_time ? Carbon::parse($row->from_time)->format('h:i a') : '';
                    })

                    ->addColumn('to_time', function($row){
                        return $row->to_time ? Carbon::parse($row->to_time)->format('h:i a') : '';
                    })

                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status === 'Active' ? 'active-data' : 'decline-data') . '" id="status-update"  type="checkbox" ' . ($row->status === 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('workingtimeranges.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-data" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';

                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-data action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('title', 'like', "%{$searchValue}%")
                                    ->orWhere('status', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['title', 'from_time', 'to_time', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.workingtimeranges.index');
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
        return view('admin.workingtimeranges.create');
    }
    public function store(WorkingTimeRangeRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = new WorkingTimeRange();
            $data->title = $request->title;
            $data->from_time = $request->from_time;
            $data->to_time = $request->to_time;
            $data->status = $request->status;
            $data->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );
            DB::commit();

            return redirect()->route('workingtimeranges.index')->with($notification);

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
    public function show(WorkingTimeRange $workingtimerange)
    {
        return view('admin.workingtimeranges.edit', compact('workingtimerange'));
    }
    public function edit(WorkingTimeRangeRequest $request)
    {
        //
    }
    public function update(WorkingTimeRange $workingtimerange, WorkingTimeRangeRequest $request)
    {
        try
        {
            $workingtimerange->title = $request->title;
            $workingtimerange->from_time = $request->from_time;
            $workingtimerange->to_time = $request->to_time;
            $workingtimerange->status = $request->status;
            $workingtimerange->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('workingtimeranges.index')->with($notification);

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
    public function destroy(WorkingTimeRange $workingtimerange)
    {
        try
        {
            $workingtimerange->delete();
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
    public function workingtimerangesStatusUpdate(WorkingTimeRangeRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = WorkingTimeRange::findorfail($request->id);
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
}
