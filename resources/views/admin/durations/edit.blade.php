@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Duration</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/durations')}}">All Duration</a></li>
                        <li class="breadcrumb-item active">Edit Duration</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Edit Duration</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('durations.update',$duration->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="time_duration">Time Duration <span class="required">*</span></label>
                                <input type="text" name="time_duration" class="form-control" id="time_duration"
                                       placeholder="Time Duration (Ex. 30 Minutes, 45 Minutes)" required="" value="{{old('time_duration', $duration->time_duration)}}">
                                @error('time_duration')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Time Unit <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="time_unit" id="time_unit" required="">
                                    <option value="" selected="" disabled="">Select One</option>
                                    <option value="Minutes" @if($duration->time_unit === 'Minutes') selected @endif>Minutes</option>
                                    <option value="Hours" @if($duration->time_unit === 'Hours') selected @endif>Hours</option>
                                </select>
                                @error('time_unit')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="status" id="status" required="">
                                    <option value="" selected="" disabled="">Select One</option>
                                    <option value="Active" @if($duration->status === 'Active') selected @endif>Active</option>
                                    <option value="Inactive" @if($duration->status === 'Inactive') selected @endif>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group w-100 px-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </form>
        </div>
    </section>
</div>

@endsection

@push('scripts')


  <script>

  </script>

@endpush
