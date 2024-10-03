@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        {{-- Menampilkan nama departemen yang aktif --}}
                        {{-- <h3 class="font-weight-bold">
                        Welcome {{ Auth::user()->departemen->nama_departemen }}
                    </h3> --}}
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <span class="btn btn-sm btn-light bg-white" id="currentDateText"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Div untuk chart -->
        <div class="row">
            @foreach ($auditData as $auditId => $departemenData)
                @foreach ($departemenData as $departemenId => $data)
                    <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{ $data['auditName'] ?? 'Audit ' . $auditId }}</h4>
                                <h6 class="card-subtitle mb-2 text-muted">{{ $data['departemenName'] }}</h6>
                                <canvas id="chart-{{ $auditId }}-{{ $departemenId }}" width="1668" height="834"
                                    style="display: block; height: 417px; width: 834px;"
                                    class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fungsi untuk memperbarui tanggal dan waktu
        function updateDateTime() {
            var currentDate = new Date();
            var formattedDate = currentDate.toLocaleString();
            document.getElementById('currentDateText').textContent = formattedDate;
        }

        // Memperbarui tanggal dan waktu setiap detik
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Menggambar chart untuk setiap departemen
        @foreach ($auditData as $auditId => $departemenData)
            @foreach ($departemenData as $departemenId => $data)
                (function(auditId, departemenId, dataValues) {
                    var ctx = document.getElementById('chart-' + auditId + '-' + departemenId).getContext('2d');

                    var chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Completed Tasks', 'Not Completed Tasks', 'Submitted Tasks'],
                            datasets: [{
                                label: 'Tasks',
                                data: dataValues,
                                backgroundColor: ['#4CAF50', '#FF5252', '#FFA500'],
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Tasks'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            let dataLabel = tooltipItem.label || '';
                                            let dataValue = tooltipItem.raw || 0;
                                            return dataLabel + ': ' + dataValue + ' (' + Math.round((
                                                    dataValue / (
                                                        dataValues.reduce((a, b) => a + b, 0) || 1)) *
                                                100) + '%)';
                                        }
                                    }
                                }
                            }
                        },
                        plugins: [{
                            afterDraw: function(chart) {
                                if (dataValues.every(v => v === 0)) {
                                    var ctx = chart.ctx;
                                    var chartArea = chart.chartArea;
                                    var x = (chartArea.left + chartArea.right) / 2;
                                    var y = (chartArea.top + chartArea.bottom) / 2;
                                    ctx.save();
                                    ctx.font = '16px Arial';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    ctx.fillStyle = '#999'; // Set color for the text
                                    ctx.fillText('No data available', x, y);
                                    ctx.restore();
                                }
                            }
                        }]
                    });
                })({{ $auditId }}, {{ $departemenId }}, [{{ $data['completedTasks'] }},
                    {{ $data['notCompletedTasks'] }}, {{ $data['submittedTasks'] }}
                ]);
            @endforeach
        @endforeach
    </script>
@endsection
