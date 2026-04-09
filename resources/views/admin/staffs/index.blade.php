@extends('admin_master')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Staffs</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Staffs</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Staffs</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <a href="{{route('staffs.create')}}" class="btn btn-primary add-new mb-2">Add New Staffs</a>
                <div class="fetch-data table-responsive">
                    <table id="table-data" class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="conts">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')

  <script>
  	$(document).ready(function(){
  		let data_id;
  		var table = $('#table-data').DataTable({
		        searching: true,
		        processing: true,
		        serverSide: true,
		        ordering: false,
		        responsive: true,
		        stateSave: true,
		        ajax: {
		          url: "{{ url('/staffs') }}",
		        },

		        columns: [
		            {data: 'name', name: 'name'},
		            {data: 'image', name: 'image'},
		            {data: 'email', name: 'email'},
		            {data: 'phone', name: 'phone'},
                    {data: 'status', name: 'status'},
		            {data: 'action', name: 'action', orderable: false, searchable: false},
		        ]
        });

        {{--$(document).on('click', '.delete-data', function(e){--}}

        {{--    e.preventDefault();--}}

        {{--    data_id = $(this).data('id');--}}

        {{--    if(confirm('Do you want to delete this?'))--}}
        {{--    {--}}
        {{--        $.ajax({--}}

        {{--            url: "{{url('/staffs')}}/"+data_id,--}}
        {{--            type:"DELETE",--}}
        {{--            dataType:"json",--}}
        {{--            success:function(data) {--}}
        {{--                if (data.status) {--}}
        {{--                    toastr.success(data.message);--}}

        {{--                    $('.data-table').DataTable().ajax.reload(null, false);--}}
        {{--                } else {--}}
        {{--                    toastr.error(data.message);--}}
        {{--                }--}}
        {{--            },--}}
        {{--        });--}}
        {{--    }--}}
        {{--});--}}

        $(document).on('click', '#status-update', function(){

            const id = $(this).data('id');
            var isDataChecked = $(this).prop('checked');
            var status_val = isDataChecked ? 'Active' : 'Inactive';
            $.ajax({

                url: "{{ url('/staff-status-update') }}",

                type: "POST",
                data:{ 'id': id, 'status': status_val },
                dataType: "json",
                success:function(data) {
                    if (data.status) {
                        toastr.success(data.message);

                        $('.data-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(data.message);
                    }
                },
            });
        });

  	});
  </script>

@endpush
