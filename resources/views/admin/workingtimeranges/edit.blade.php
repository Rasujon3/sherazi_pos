@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Working Time Range</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/workingtimeranges')}}">All Working Time Range</a></li>
                        <li class="breadcrumb-item active">Edit Working Time Range</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Edit Working Time Range</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('workingtimeranges.update',$workingtimerange->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Name <span class="required">*</span></label>
                                <input type="text" name="title" class="form-control" id="title"
                                       required value="{{ old('title', $workingtimerange->title) }}">
                                @error('title')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="status" id="status" required="">
                                    <option value="" selected="" disabled="">Select One</option>
                                    <option value="Active" @if($workingtimerange->status === 'Active') selected @endif>Active</option>
                                    <option value="Inactive" @if($workingtimerange->status === 'Inactive') selected @endif>Inactive</option>
                                </select>
                                @error('status')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- From Time --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_time">From Time <span class="required">*</span></label>
                                <input type="time" name="from_time" class="form-control"
                                       id="from_time" required
                                       value="{{ old('from_time', $workingtimerange->from_time) }}">
                                @error('from_time')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- To Time --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_time">To Time <span class="required">*</span></label>
                                <input type="time" name="to_time" class="form-control"
                                       id="to_time" required
                                       value="{{ old('to_time', $workingtimerange->to_time) }}">
                                @error('to_time')
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
