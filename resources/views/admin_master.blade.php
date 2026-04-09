<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('back/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('back/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('back/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('back/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('back/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('back/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('back/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('back/plugins/summernote/summernote-bs4.min.css')}}">

  <link rel="stylesheet" href="{{asset('custom/style.css')}}">

  <link rel="stylesheet" href="{{asset('custom/toastr.css')}}">

    <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('back/plugins/select2/css/select2.min.css')}}">

  <link rel="stylesheet" href="{{asset('back/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

     <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="{{asset('back/datatable/css/dataTables.bootstrap4.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('back/datatable/css/buttons.dataTables.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('back/datatable/css/responsive.bootstrap4.min.css')}}">



    <link rel="stylesheet" href="{{asset('dropify/dist/css/dropify.min.css')}}">


</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

   @include('navbar')
   @include('sidebar')

   @yield('content')

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('custom/custom_js.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('back/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('back/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>


<script src="{{asset('custom/custom.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('back/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('back/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('back/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('back/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('back/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('back/dist/js/adminlte.js')}}"></script>

<!-- Select2 -->
<script src="{{asset('back/plugins/select2/js/select2.full.min.js')}}"></script>

<!-- data-table js -->
<script src="{{asset('back/datatable/js/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('back/datatable/js/dataTables.buttons.min.js')}}"></script>



<script src="{{asset('back/datatable/js/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{asset('back/datatable/js/dataTables.responsive.min.js')}}"></script>

<script src="{{asset('back/datatable/js/responsive.bootstrap4.min.js')}}"></script>

<script src="{{asset('back/datatable/js/data-table-custom.js')}}"></script>

<script src="{{asset('dropify/dist/js/dropify.min.js')}}"></script>



<script>
  $(function () {
    $('#summernote').summernote();

    var base_url = "{{url('/')}}";
    localStorage.setItem('base_url', base_url);

  })
</script>

<script src="{{asset('custom/toastr.js')}}"></script>

  @if(Session::has('message'))
    @toastr("{{ Session::get('message') }}")
  @endif

@stack('scripts')
</body>
</html>
