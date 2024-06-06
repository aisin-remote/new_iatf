@extends('layouts.app')
@section('title', 'Dashboard-admin')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Welcome Aamir</h3>
                        <h6 class="font-weight-normal mb-0">All systems are running smoothly! You have
                            <span class="text-primary">3 unread alerts!</span>
                        </h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                    id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                    <a class="dropdown-item" href="#">January - March</a>
                                    <a class="dropdown-item" href="#">March - June</a>
                                    <a class="dropdown-item" href="#">June - August</a>
                                    <a class="dropdown-item" href="#">August - November</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="chartjs-size-monitor">
                                    <div class="chartjs-size-monitor-expand">
                                        <div class=""></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink">
                                        <div class=""></div>
                                    </div>
                                </div>
                                <h4 class="card-title">Pie chart</h4>
                                <canvas id="pieChart" width="406" height="202"
                                    style="display: block; height: 162px; width: 325px;"
                                    class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="chartjs-size-monitor">
                                    <div class="chartjs-size-monitor-expand">
                                        <div class=""></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink">
                                        <div class=""></div>
                                    </div>
                                </div>
                                <h4 class="card-title">Pie chart</h4>
                                <canvas id="pieChart" width="406" height="202"
                                    style="display: block; height: 162px; width: 325px;"
                                    class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="chartjs-size-monitor">
                                    <div class="chartjs-size-monitor-expand">
                                        <div class=""></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink">
                                        <div class=""></div>
                                    </div>
                                </div>
                                <h4 class="card-title">Pie chart</h4>
                                <canvas id="pieChart" width="406" height="202"
                                    style="display: block; height: 162px; width: 325px;"
                                    class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="chartjs-size-monitor">
                                    <div class="chartjs-size-monitor-expand">
                                        <div class=""></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink">
                                        <div class=""></div>
                                    </div>
                                </div>
                                <h4 class="card-title">Pie chart</h4>
                                <canvas id="pieChart" width="406" height="202"
                                    style="display: block; height: 162px; width: 325px;"
                                    class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Today’s Bookings</p>
                                <p class="fs-30 mb-2">4006</p>
                                <p>10.00% (30 days)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Total Bookings</p>
                                <p class="fs-30 mb-2">61344</p>
                                <p>22.00% (30 days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Today’s Bookings</p>
                                <p class="fs-30 mb-2">4006</p>
                                <p>10.00% (30 days)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Total Bookings</p>
                                <p class="fs-30 mb-2">61344</p>
                                <p>22.00% (30 days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Mengatur interval untuk menggeser carousel setiap 10 detik
            setInterval(function() {
                $('#detailedReports.carousel').carousel('next');
            }, 10000); // 10 detik

            // Aktifkan carousel manual
            $('#detailedReports.carousel').carousel({
                interval: false // Nonaktifkan pergeseran otomatis
            });
        });
    </script>
    <script>
        function updateCurrentDate() {
            // Mendapatkan tanggal hari ini
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            // Format tanggal sesuai kebutuhan (misalnya: 10 Jan 2021)
            var formattedDate = dd + ' ' + monthNames[parseInt(mm) - 1] + ' ' + yyyy;

            // Mengubah teks dengan tanggal hari ini
            document.getElementById("currentDateText").innerText = "Today (" + formattedDate + ")";
        }

        // Array untuk nama-nama bulan
        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        // Memperbarui tanggal setiap detik
        updateCurrentDate(); // Memperbarui tanggal saat ini saat halaman dimuat
        var intervalId = setInterval(updateCurrentDate, 1000); // Memperbarui tanggal setiap detik

        // Fungsi untuk menghentikan pembaruan tanggal secara real-time
        function stopRealTimeUpdate() {
            clearInterval(intervalId); // Menghentikan pemanggilan setInterval
        }
    </script>
@endsection
