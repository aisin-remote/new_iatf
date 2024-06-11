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
                            <div class="justify-content-end d-flex">
                                <div class="flex-md-grow-1 flex-xl-grow-0">
                                    <span class="btn btn-sm btn-light bg-white" id="currentDateText"></span>
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
                                <canvas id="pieChart1" width="406" height="202"
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
                                <h4 class="mb-4">WI</h4>
                                <p class="fs-30 mb-2">{{ $countByType->where('tipe_dokumen', 'WI')->first()->count ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">Prosedur</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'PROSEDUR')->first()->count ?? 0 }}</p>
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
                                <h4 class="mb-4">Standar</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'Standar')->first()->count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">WIS</h4>
                                <p class="fs-30 mb-2">{{ $countByType->where('tipe_dokumen', 'WIS')->first()->count ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
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
    <script>
        // Data untuk grafik pie
        var data = {
            labels: {!! json_encode($countByType->pluck('tipe_dokumen')) !!},
            datasets: [{
                data: {!! json_encode($countByType->pluck('count')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    // Anda dapat menambahkan warna tambahan jika diperlukan
                ],
                borderWidth: 1
            }]
        };

        // Konfigurasi untuk grafik pie
        var options = {
            responsive: true,
            maintainAspectRatio: false,
        };

        // Membuat grafik pie
        var ctx1 = document.getElementById('pieChart1').getContext('2d');
        var myPieChart1 = new Chart(ctx1, {
            type: 'pie',
            data: data,
            options: options
        });

        var ctx2 = document.getElementById('pieChart2').getContext('2d');
        var myPieChart2 = new Chart(ctx2, {
            type: 'pie',
            data: data,
            options: options
        });

        var ctx3 = document.getElementById('pieChart3').getContext('2d');
        var myPieChart3 = new Chart(ctx3, {
            type: 'pie',
            data: data,
            options: options
        });

        var ctx4 = document.getElementById('pieChart4').getContext('2d');
        var myPieChart4 = new Chart(ctx4, {
            type: 'pie',
            data: data,
            options: options
        });
    </script>
@endsection
