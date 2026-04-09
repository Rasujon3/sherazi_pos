@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">About Us</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/about-us')}}">About Us
                                </a></li>
                        <li class="breadcrumb-item active">About Us</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">About Us</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ url('about-us') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">

                       <div class="col-md-6">
                       	 <div class="card">
                       	   <div class="card-header bg-primary">
                       	   	 <h5 class="card-title">User Agreement</h5>
                       	   </div>
                       	   <div class="card-body">
                       	   	 <div class="form-group">
                       	   	  <label for="user_agreement">User Agreement</label>
                       	   	  <textarea
                                  name="user_agreement"
                                  class="form-control description"
                                  id="user_agreement"
                                  placeholder="Privacy & Policy"
                              >
                                  {!!old('user_agreement',$info ? $info->user_agreement : "")!!}
                              </textarea>
                       	   	 </div>
                       	   </div>
                       	 </div>
                       </div>

                       <div class="col-md-6">
                       	 <div class="card">
                       	   <div class="card-header bg-warning">
                       	   	 <h5 class="card-title text-light">About Us</h5>
                       	   </div>
                       	   <div class="card-body">
                       	   	 <div class="form-group">
                       	   	  <label for="about_us">About Us</label>
                       	   	  <textarea
                                  name="about_us"
                                  class="form-control description"
                                  id="about_us"
                                  placeholder="About Us"
                              >
                                  {!!old('about_us',$info?$info->about_us:"")!!}
                              </textarea>
                       	   	 </div>
                       	   </div>
                       	 </div>
                       </div>

                        <div class="form-group w-100 px-2">
                            <button type="submit" class="btn btn-success btn-block">Save Changes</button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
