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

        <!-- Div untuk chart -->
        <div class="row">
            @foreach ($auditData as $index => $data)
                <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $data['auditName'] }}</h4>
                            <canvas id="chart-{{ $index }}" width="1668" height="834"
                                style="display: block; height: 417px; width: 834px;"
                                class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
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
        @foreach ($auditData as $index => $data)
            var ctx{{ $index }} = document.getElementById('chart-{{ $index }}').getContext('2d');
            var chart{{ $index }} = new Chart(ctx{{ $index }}, {
                type: 'pie',
                data: {
                    labels: ['Completed Tasks', 'Not Completed Tasks'],
                    datasets: [{
                        data: [{{ $data['completedTasks'] }}, {{ $data['notCompletedTasks'] }}],
                        backgroundColor: ['#4CAF50', '#FF5252'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    let dataLabel = tooltipItem.label || '';
                                    let dataValue = tooltipItem.raw || 0;
                                    return dataLabel + ': ' + dataValue + ' (' + Math.round((dataValue /
                                            {{ $data['completedTasks'] + $data['notCompletedTasks'] }}) *
                                        100) + '%)';
                                }
                            }
                        }
                    }
                },
                // Plugin untuk menampilkan pesan jika tidak ada data
                plugins: [{
                    afterDraw: function(chart) {
                        if (chart.data.datasets[0].data.every(v => v === 0)) {
                            var ctx = chart.ctx;
                            var chartArea = chart.chartArea;
                            var x = (chartArea.left + chartArea.right) / 2;
                            var y = (chartArea.top + chartArea.bottom) / 2;
                            ctx.save();
                            ctx.font = '16px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText('No data available', x, y);
                            ctx.restore();
                        }
                    }
                }]
            });
        @endforeach
    </script>
@endsection
