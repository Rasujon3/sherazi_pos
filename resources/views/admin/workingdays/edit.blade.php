@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Working Days</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/workingdays')}}">All Working Days</a></li>
                        <li class="breadcrumb-item active">Edit Working Days</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Edit Working Days</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('workingdays.update',$workingday->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Working Days <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" id="name"
                                       placeholder="Working Days" required="" value="{{old('name', $workingday->name)}}">
                                @error('name')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_order">Select Order <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="sort_order" id="sort_order" required="">
                                    <option value="" selected="" disabled="">Select Order</option>
                                    @if(count($availableSortOrders) > 0)
                                        @foreach ($availableSortOrders as $availableSortOrder)
                                            <option value="{{ $availableSortOrder }}" @if($workingday->sort_order === $availableSortOrder) selected @endif>{{ $availableSortOrder }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('sort_order')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="status" id="status" required="">
                                    <option value="" selected="" disabled="">Select One</option>
                                    <option value="Active" @if($workingday->status === 'Active') selected @endif>Active</option>
                                    <option value="Inactive" @if($workingday->status === 'Inactive') selected @endif>Inactive</option>
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
