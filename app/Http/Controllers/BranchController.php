<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
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

                $tasks = Branch::select('*')->latest();

                return Datatables::of($tasks)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('address', function($row){
                        return $row->address ?? '';
                    })

                    ->addColumn('email', function($row){
                        return $row->email ?? '';
                    })

                    ->addColumn('phone', function($row){
                        return $row->phone ?? '';
                    })

                    ->addColumn('latitude', function($row){
                        return $row->latitude ?? '';
                    })

                    ->addColumn('longitude', function($row){
                        return $row->longitude ?? '';
                    })

                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status === 'Active' ? 'active-data' : 'decline-data') . '" id="status-update"  type="checkbox" ' . ($row->status === 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('branches.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-product" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

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
                                    ->orWhere('email', 'like', "%{$searchValue}%")
                                    ->orWhere('phone', 'like', "%{$searchValue}%")
                                    ->orWhere('status', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['name','address','email','phone','latitude','longitude', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.branches.index');
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
        return view('admin.branches.create');
    }
    public function store(BranchRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $branch = new Branch();
            $branch->name = $request->name ?? null;
            $branch->address = $request->address ?? null;
            $branch->email = $request->email ?? null;
            $branch->phone = $request->phone ?? null;
            $branch->latitude = $request->latitude ?? null;
            $branch->longitude = $request->longitude ?? null;
            $branch->status = $request->status ?? null;
            $branch->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );

            DB::commit();

            return redirect()->route('branches.index')->with($notification);

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
    public function show(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }
    public function edit(Branch $branch)
    {
        //
    }
    public function update(BranchRequest $request, Branch $branch)
    {
        try
        {
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->email = $request->email;
            $branch->phone = $request->phone;
            $branch->latitude = $request->latitude;
            $branch->longitude = $request->longitude;
            $branch->status = $request->status;
            $branch->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('branches.index')->with($notification);

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
    public function destroy(Branch $branch)
    {
        try
        {
            $branch->delete();
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
    public function branchStatusUpdate(BranchRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = Branch::findorfail($request->id);
            $data->status = $request->status;
            $data->update();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Branch status updated successfully."
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
