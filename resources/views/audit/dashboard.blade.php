@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        {{-- <h3 class="font-weight-bold">
                            Welcome {{ Auth::user()->departemen->nama_departemen }}
                        </h3> --}}
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

        <!-- Div untuk count -->
        <div class="row">
            <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card">
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
                        <h4 class="card-title">ISO 90001</h4>
                        <canvas id="pieChart" width="1668" height="834"
                            style="display: block; height: 417px; width: 834px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card">
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
                        <canvas id="pieChart" width="1668" height="834"
                            style="display: block; height: 417px; width: 834px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card">
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
                        <canvas id="pieChart" width="1668" height="834"
                            style="display: block; height: 417px; width: 834px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateDateTime() {
            var currentDate = new Date();
            var formattedDate = currentDate.toLocaleString();
            document.getElementById('currentDateText').textContent = formattedDate;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
    <script>
        var ctx = document.getElementById('auditChart').getContext('2d');
        var auditChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Completed Tasks', 'Incomplete Tasks'],
                datasets: [{
                    data: [{{ $completedTasks }}, {{ $totalTasks - $completedTasks }}],
                    backgroundColor: ['#4CAF50', '#FF5252'], // Warna untuk pie chart
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    </script>
@endsection
