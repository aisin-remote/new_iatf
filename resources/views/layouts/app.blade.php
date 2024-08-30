<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- <link rel="stylesheet" href="{{ asset('vendors/feather/feather.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('js/select.dataTables.min.css') }}">

    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
</head>
<style>
    /* Dropdown notifications style */
    .nav-item.dropdown .dropdown-menu {
        width: 300px;
        padding: 10px;
    }

    .nav-item.dropdown .dropdown-header {
        font-size: 1.2em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .notification-item {
        padding: 10px;
        border-bottom: 1px solid #f1f1f1;
        transition: background-color 0.3s;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f9f9f9;
    }

    .notification-icon {
        width: 40px;
        /* Adjust the width according to your icon size */
        height: 40px;
        /* Adjust the height according to your icon size */
        background-color: #007bff;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        margin-right: 10px;
    }

    .notification-icon i {
        color: #fff;
        font-size: 1.5em;
    }

    .notification-content {
        flex-grow: 1;
    }

    .notification-title {
        font-weight: bold;
        margin: 0;
    }

    .notification-text {
        margin: 0;
        color: #6c757d;
    }

    .notification-status {
        font-size: 0.9em;
        color: #28a745;
    }

    /* Badge for unread notifications */
    .count-indicator .badge {
        position: absolute;
        top: 5px;
        right: 5px;
        font-size: 0.75em;
    }
</style>


<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        @include('partials.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.html -->
            {{-- @include('partials.settings-panel') --}}

            @if (request()->is('rule') || request()->is('rule/*'))
                @include('partials.sidebar-rule')
            @else
                @include('partials.sidebar-audit')
            @endif

            <!-- partial -->
            <div class="main-panel">
                @yield('content')
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                @include('partials.footer')
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/dataTables.select.min.js') }}"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/Chart.roundedBarCharts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @include('sweetalert::alert')
    <!-- End custom js for this page-->
</body>

</html>
