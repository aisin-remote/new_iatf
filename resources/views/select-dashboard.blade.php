<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Select Dashboard</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="index.html"><img src="images/logo-iatf.png" class="mr-2"
                        alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="index.html"><img src="images/logo-iatf-mini.png"
                        alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            {{ Auth::user()->selectedDepartmen ? Auth::user()->selectedDepartmen->nama_departemen : 'No Department Assigned' }}

                            <i class="fa-solid fa-sort-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            @foreach (Auth::user()->departmens ?? [] as $departmen)
                                <a class="dropdown-item" href="{{ route('switch.departemen', $departmen->id) }}">
                                    {{ $departmen->nama_departemen }}
                                </a>
                            @endforeach
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="ti-power-off text-primary"></i>
                                Logout
                            </a>
                        </div>

                    </li>
                </ul>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- main panel without sidebar -->
            <div class="main-panel" style="width: 100%;">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="row">
                                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                                    <h3 class="font-weight-bold">Welcome To Control Document AIIA</h3>
                                    <h6 class="font-weight-normal mb-0">Select your dashboard</h6>
                                </div>
                                <div class="col-12 col-xl-4">
                                    <div class="justify-content-end d-flex">
                                        <div class="justify-content-end d-flex">
                                            <div class="flex-md-grow-1 flex-xl-grow-0">
                                                <span class="btn btn-sm btn-light bg-white" id="currentDateText"></span>
                                                <!-- Tombol unduh ditambahkan di bawah elemen span -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('dashboard.rule') }}">
                                <div class="card tale-bg">
                                    <div class="card-people mt-auto"
                                        style="position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; height: 100%;">
                                        <img src="{{ asset('images/select-dashboard-3.jpeg') }}" alt="process"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                        <div class="weather-info" style="position: absolute; bottom: 10px; left: 10px;">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <h2 class="mb-0 font-weight-bold text-white"><i
                                                        class="icon-sun mr-2"></i>Dashboard Rule</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <div class="card tale-bg">
                                <a href="{{ route('comingsoon') }}" style="text-decoration: none;">
                                    <div class="card-people mt-auto"
                                        style="position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; height: 100%;">
                                        <img src="{{ asset('images/select-dashboard-2.jpg') }}" alt="rule"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                        <div class="weather-info" style="position: absolute; bottom: 10px; left: 10px;">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <h2 class="mb-0 font-weight-bold text-white"><i
                                                        class="icon-sun mr-2"></i>Dashboard Process</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">2024 © <a
                                href="https://aiia.co.id/" target="_blank">PT. Aisin Indonesia Automotive</a></span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/chart.js/Chart.min.js"></script>
    <script src="vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="js/dataTables.select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <script src="js/Chart.roundedBarCharts.js"></script>
    <script>
        function updateDateTime() {
            var currentDate = new Date();
            var formattedDate = currentDate.toLocaleString();
            document.getElementById('currentDateText').textContent = formattedDate;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
    <!-- End custom js for this page-->
</body>

</html>
