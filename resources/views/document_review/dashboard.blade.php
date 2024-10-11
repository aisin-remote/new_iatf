@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">
                            Welcome
                            {{ Auth::user()->departemen ? Auth::user()->departemen->nama_departemen : 'No Department Assigned' }}
                        </h3>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="d-flex justify-content-end">
                            <form id="filterForm" method="GET" action="{{ route('document_review.dashboard') }}"
                                class="me-2 d-flex align-items-center">
                                <div class="input-group me-2">
                                    <select name="month" class="form-select me-2" required>
                                        <option value="" disabled {{ old('month') ? '' : 'selected' }}>Bulan</option>
                                        <option value="1" {{ old('month') == 1 ? 'selected' : '' }}>Januari</option>
                                        <option value="2" {{ old('month') == 2 ? 'selected' : '' }}>Februari</option>
                                        <option value="3" {{ old('month') == 3 ? 'selected' : '' }}>Maret</option>
                                        <option value="4" {{ old('month') == 4 ? 'selected' : '' }}>April</option>
                                        <option value="5" {{ old('month') == 5 ? 'selected' : '' }}>Mei</option>
                                        <option value="6" {{ old('month') == 6 ? 'selected' : '' }}>Juni</option>
                                        <option value="7" {{ old('month') == 7 ? 'selected' : '' }}>Juli</option>
                                        <option value="8" {{ old('month') == 8 ? 'selected' : '' }}>Agustus</option>
                                        <option value="9" {{ old('month') == 9 ? 'selected' : '' }}>September</option>
                                        <option value="10" {{ old('month') == 10 ? 'selected' : '' }}>Oktober</option>
                                        <option value="11" {{ old('month') == 11 ? 'selected' : '' }}>November</option>
                                        <option value="12" {{ old('month') == 12 ? 'selected' : '' }}>Desember</option>
                                    </select>
                                </div>
                                <div class="input-group me-2">
                                    <select name="year" class="form-select me-2" required>
                                        <option value="" disabled {{ old('year') ? '' : 'selected' }}>Tahun</option>
                                        @for ($i = date('Y'); $i >= 2000; $i--)
                                            <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button type="submit" class="btn btn-primary"
                                        style="height: 28px; line-height: 28px; text-align: center; padding: 0 10px;">
                                        Filter
                                    </button>
                                </div>
                            </form>
                            <span class="btn btn-sm btn-light bg-white" id="currentDateText"
                                style="margin-left: 24px"></span>
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
                        <h4 class="card-title">Uncomplete Document Review</h4>
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
                    text: 'Department Document Review Counts'
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
