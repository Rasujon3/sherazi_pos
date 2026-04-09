@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Settings</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/settings')}}">Settings
                                </a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Settings</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{url('settings-app')}}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trial_amount">Trial amount <span class="required">*</span></label>
                                <input type="text" name="trial_amount" class="form-control numericInput" id="trial_amount"
                                    placeholder="Trial amount"  value="{{old('trial_amount', ($setting && $setting->trial_amount) ? $setting->trial_amount : "")}}">
                                @error('trial_amount')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="frozen_amount">Frozen amount <span class="required">*</span></label>
                                <input type="text" name="frozen_amount" class="form-control numericInput" id="frozen_amount"
                                       placeholder="Frozen amount"  value="{{old('frozen_amount', $setting ? $setting->frozen_amount : "")}}">
                                @error('frozen_amount')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_of_trial_task">No of trial task <span class="required">*</span></label>
                                <input type="text" name="no_of_trial_task" class="form-control numericInput" id="no_of_trial_task"
                                       placeholder="No of trial task"  value="{{old('no_of_trial_task', $setting ? $setting->no_of_trial_task : "")}}">
                                @error('no_of_trial_task')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_timing">Task Timing <span class="required">*</span></label>
                                <input type="datetime-local" name="task_timing" class="form-control" id="task_timing"
                                       value="{{ old('task_timing', $setting ? \Carbon\Carbon::parse($setting->task_timing)->format('Y-m-d\TH:i') : '') }}">
                                @error('task_timing')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telegram_group_link">Telegram group Link <span class="required">*</span></label>
                                <input type="text" name="telegram_group_link" class="form-control numericInput" id="telegram_group_link"
                                       placeholder="Telegram group Link"  value="{{old('telegram_group_link', $setting ? $setting->telegram_group_link : "")}}">
                                @error('telegram_group_link')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </div>
                    <!-- /.card-body -->
            </form>
        </div>
    </section>
</div>
@endsection
