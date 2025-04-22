<script src="{{ asset('/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('/assets/libs/node-waves/waves.min.js') }}"></script>

<script src="{{ asset('/assets/libs/select2/js/select2.min.js') }}"></script>

<!-- Plugins js -->
<script src="{{ asset('/assets/libs/dropzone/min/dropzone.min.js') }}"></script>

<!-- owl.carousel js -->
<script src="{{ asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>

<!-- auth-2-carousel init -->
<script src="{{ asset('/assets/js/pages/auth-2-carousel.init.js') }}"></script>

<!-- apexcharts -->
<script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- crypto dash init js -->
<script src="{{ asset('/assets/js/pages/crypto-dashboard.init.js') }}"></script>

<script src="{{ asset('/assets/libs/parsleyjs/parsley.min.js') }}"></script>

<script src="{{ asset('/assets/js/pages/form-validation.init.js') }}"></script>

<script src="{{ asset('/assets/js/app.js') }}"></script>

<!-- form wizard init -->
<script src="{{ asset('/assets/js/pages/form-wizard.init.js') }}"></script>

<!-- jquery step -->
<script src="{{ asset('/assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>

<!-- Required datatable js -->
<script src="{{ asset('/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

<!-- Datatable init js -->
<script src="{{ asset('/assets/js/pages/datatables.init.js') }}"></script>

<!-- Buttons examples -->
<script src="{{ asset('/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/assets/libs/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- Sweet alert init js-->
<script src="{{ asset('/assets/js/pages/sweet-alerts.init.js') }}"></script>

<!-- Sweet Alerts js -->
<script src="{{ asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- Magnific Popup-->
<script src="{{ asset('/assets/libs/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

<!-- lightbox init js-->
<script src="{{ asset('/assets/js/pages/lightbox.init.js') }}"></script>

<!-- jquery step -->
<script src="{{ asset('/assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>

<!-- form wizard init -->
<script src="{{ asset('/assets/js/pages/form-wizard.init.js') }}"></script>

<script src="{{ asset('/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- TOASTER -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.js"></script>

<script>        
    @if(Session::has('success')) new Noty({ 
            type:'success', 
            layout:'bottomLeft', 
            text: '{{ Session::get('success') }}', 
            timeout: 5000
        }).show(); 
    @endif

    @if(Session::has('info')) new Noty({ 
            type:'info', 
            layout:'bottomLeft', 
            text: '{{ Session::get('info') }}', 
            timeout: 5000
        }).show(); 
    @endif

    @if(Session::has('error')) new Noty({ 
            type:'error', 
            layout:'bottomLeft', 
            text: '{{ Session::get('error') }}', 
            timeout: 5000
        }).show(); 
    @endif

    @if(Session::has('im-val-error')) new Noty({ 
            type:'error', 
            layout:'bottomLeft', 
            text: '{{ Session::get('im-val-error') }}', 
            timeout: 5000
        }).show(); 
    @endif

    @if(Session::has('warning')) new Noty({ 
            type:'warning', 
            layout:'bottomLeft', 
            text: '{{ Session::get('warning') }}', 
            timeout: 5000
        }).show(); 
    @endif
</script>

<script>

    $(document).ready(function() {
        $(".select2").select2({
            allowClear: true,
        });
    });

</script>

<script type="text/javascript">

    function handleFileSelect(event) 
    {
        var reader = new FileReader();
        reader.onload = function()
        {
            var output = document.getElementById('preview');
            output.src = reader.result;
            $('#preview').css({
               'width' : '220px',
               'height' : '150px'
            });
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    
</script>
