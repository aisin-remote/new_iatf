@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">
                            Welcome
                            {{ Auth::user()->departemen ? Auth::user()->departemen->nama_departemen : 'No Department Assigned' }}
                        </h3>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <form id="filterForm" method="GET" action="{{ route('document_control.dashboard') }}"
                                class="me-2">
                                <select name="month" class="form-select me-2" required>
                                    <option value="" disabled selected>Bulan</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <select name="year" class="form-select me-2" required>
                                    <option value="" disabled selected>Tahun</option>
                                    @for ($i = date('Y'); $i >= 2000; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                            <span class="btn btn-sm btn-light bg-white" id="currentDateText"></span>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Div untuk chart -->
        <div class="row" id="chartsContainer">
            <div class="col-lg-12 grid-margin grid-margin-lg-0 stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Uncomplete Document Obsolate</h4>
                        <div id="departmentChart" style="height: 417px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
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

        // Fungsi untuk menggambar chart menggunakan Highcharts
        function drawHighchart(containerId, departmentNames, departmentCounts) {
            Highcharts.chart(containerId, {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Department Document Obsolate Counts'
                },
                xAxis: {
                    categories: departmentNames // Mengambil nama departemen dari model
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Count'
                    }
                },
                series: [{
                    name: 'Departments',
                    data: departmentCounts, // Mengisi data dengan total dokumen per departemen
                    colorByPoint: true
                }],
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b>',
                    shared: true
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                }
            });
        }

        // Ambil data departemen dari PHP ke JavaScript
        const departmentNames = @json($departments->pluck('nama_departemen'));

        // Ambil data total dokumen per departemen dari PHP ke JavaScript
        const departmentCounts = @json(array_values($departmentTotals)); // Ambil total dokumen

        // Menggambar chart dengan nama departemen dan total dokumen
        drawHighchart('departmentChart', departmentNames, departmentCounts);
    </script>

@endsection
