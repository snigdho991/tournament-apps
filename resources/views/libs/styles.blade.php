<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('/assets/images/favicon.ico') }}">

<link href="{{ asset('/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Plugins css -->
<link href="{{ asset('/assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />

@if(Auth::check())
    <?php 
        $user_theme = Auth::user()->theme; 
    ?>

    @if ($user_theme == 'default')
        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

        <style type="text/css">
            .notification-msg {
                color: #495057; 
                font-weight: 450;
            }
        </style>
    @else
        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap-dark.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" />

        <style type="text/css">
            .notification-msg {
                color: rgb(195, 203, 228); 
                font-weight: 450;
            }
            .notification-time{
                color: #fff;
            }
        </style>
    @endif
@else
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
@endif

<!-- Icons Css -->
<link href="{{ asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- owl.carousel css -->
<link rel="stylesheet" href="{{ asset('/assets/libs/owl.carousel/assets/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/libs/owl.carousel/assets/owl.theme.default.min.css') }}">

<link rel="stylesheet" href="{{ asset('/assets/css/toastr.min.css') }}"/>

<!-- DataTables -->
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Sweet Alert-->
<link href="{{ asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Lightbox css -->
<link href="{{ asset('/assets/libs/magnific-popup/magnific-popup.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.css" />

<style>
    .breadcrumb-item+.breadcrumb-item::before {
        float: left;
        padding-right: .5rem;
        color: #74788d;
        content: var(--bs-breadcrumb-divider, "/") !important;
    }

    .image_upload_zone {
        display: block;
        width: 100%;
        border: 2px dashed #ddd;
        position: relative;
        padding: 30px 10px;
        margin: 25px 0;
    }

    .image_upload_zone input[type=file] {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
    }

    .image_upload_zone img {
        vertical-align: middle;
        border-style: none;
        width: 100px;
        margin: auto;
        display: block;
    }

    .image_upload_zone .text {
        text-align: center;
        font-size: 15px;
        font-weight: normal;
    }

    .image_upload_zone .text button {
        background: #fff;
        padding: 10px 25px;
        margin: 15px 0;
        font-weight: bold;
        box-shadow: 0 1px 10px #0002;
        border: 1px solid #bcbcbc;
    }

    .remove_img_preview
    {
        position: relative;
        top: -105px;
        right: 48px;
        background: red;
        color: white;
        border-radius: 50%;
        font-size: 1.3em;
        padding: 0 0.4em 0;
        text-align: center;
        cursor: pointer;
    }

    .remove_img_preview:before
    {
        content: "×";
    }

    .name_ext
    {
        display: block;
        width: 100%;
        border: 2px dashed #ddd;
        position: relative;
        padding: 40px 10px;
        margin: 25px 0;
    }

    #chartdiv {
        width: 100%;
        height: 370px;
    }

    @media screen and (max-width: 575px) and (min-width: 260px) {
        .cusGap{
            margin-top: 10px !important;
        }
    }

</style>

