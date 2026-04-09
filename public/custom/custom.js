$(document).ready(function(){
	$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });

	$('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    }) 
  

   $('.dropify').dropify();


   $('#description').summernote(
      {
        height: 300,
        focus: false
      }
    );
});

