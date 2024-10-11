@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        {{-- Menampilkan nama departemen yang aktif --}}
                        <h3 class="font-weight-bold">
                            Welcome {{ Auth::user()->departemen->nama_departemen }}
                        </h3>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            @role('admin')
                                <div class="dropdown ml-2">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Select Department
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                        style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($departemens as $departemen)
                                            @if ($departemen->nama_departemen !== 'Aisin Indonesia')
                                                <button class="dropdown-item department-select"
                                                    data-department-id="{{ $departemen->id }}"
                                                    onclick="selectDepartment({{ $departemen->id }})">
                                                    {{ $departemen->nama_departemen }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endrole
                            <span class="btn btn-sm btn-light bg-white" id="currentDateText"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Div untuk chart -->
        <div class="row" id="chartsContainer">
            @if (Auth::user()->hasRole('admin'))
                @foreach ($auditData as $auditId => $departemenData)
                    @foreach ($departemenData as $departemenId => $data)
                        <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card department-chart"
                            data-department-id="{{ $departemenId }}">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $data['auditName'] }}</h4>
                                    <p class="text-muted">{{ $data['departemenName'] }}</p>
                                    <canvas id="chart-{{ $departemenId }}-{{ $auditId }}" width="1668" height="834"
                                        style="display: block; height: 417px; width: 834px;"
                                        class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                @foreach ($auditData as $auditId => $departemenData)
                    @foreach ($departemenData as $departemenId => $data)
                        @if ($departemenId == Auth::user()->departemen->id)
                            <div class="col-lg-4 grid-margin grid-margin-lg-0 stretch-card department-chart"
                                data-department-id="{{ Auth::user()->departemen->id }}">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">{{ $data['auditName'] }}</h4>
                                        <!-- Menggunakan $data['auditName'] -->
                                        <h4 class="card-title">{{ Auth::user()->departemen->nama_departemen }}</h4>
                                        <canvas id="chart-{{ Auth::user()->departemen->id }}" width="1668" height="834"
                                            style="display: block; height: 417px; width: 834px;"
                                            class="chartjs-render-monitor"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            @endif
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

        // Menyimpan semua chart dalam variabel
        var charts = {};

        // Menggambar chart untuk setiap departemen dan audit
        @foreach ($auditData as $auditId => $departemenData)
            @foreach ($departemenData as $departemenId => $data)
                (function(auditId, departemenId, dataValues) {
                    var ctx = document.getElementById('chart-' + departemenId + '-' + auditId).getContext('2d');

                    // Simpan chart ke dalam objek untuk digunakan nanti
                    charts[departemenId] = charts[departemenId] || {}; // Initialize department entry
                    charts[departemenId][auditId] = new Chart(ctx, {
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
                                                dataValue / (dataValues.reduce((a, b) => a + b,
                                                    0) || 1)) * 100) + '%)';
                                        }
                                    }
                                }
                            }
                        },
                        plugins: [{
                            afterDraw: function(chart) {
                                var dataValues = chart.data.datasets[0].data;
                                if (dataValues.every(v => v ===
                                        0)) { // Cek jika semua nilai adalah 0
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

        // Fungsi untuk menangani pemilihan departemen
        function selectDepartment(departmentId) {
            console.log('Selected department ID:', departmentId);

            // Menyembunyikan semua chart
            $('.department-chart').hide();

            // Menampilkan chart yang sesuai dengan departmentId yang dipilih
            $('.department-chart[data-department-id="' + departmentId + '"]').show();

            // Jika chart untuk departmentId sudah dibuat, cukup tampilkan
            if (charts[departmentId]) {
                Object.values(charts[departmentId]).forEach(chart => chart.update()); // Memperbarui semua chart yang ada
            } else {
                console.error('Chart not found for department ID:', departmentId);
            }
        }

        // Menampilkan chart default (jika bukan admin)
        @if (!Auth::user()->hasRole('admin'))
            $('.department-chart[data-department-id="{{ Auth::user()->departemen->id }}"]').show();
        @else
            // Jika admin, sembunyikan semua chart terlebih dahulu
            $('.department-chart').hide();
        @endif
    </script>
@endsection
