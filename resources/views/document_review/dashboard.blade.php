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
                            @if (Auth::user()->hasRole('admin'))
                                {{ Auth::user()->getRoleNames()->first() }} <!-- Menampilkan nama role jika admin -->
                            @else
                                {{ Auth::user()->departemen ? Auth::user()->departemen->nama_departemen : 'No Department Assigned' }}
                                <!-- Menampilkan nama departemen jika bukan admin -->
                            @endif
                        </h3>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="d-flex justify-content-end">
                            <span class="btn btn-sm btn-light bg-white" id="currentDateText" style="margin-left: 24px"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Div untuk chart -->
        @role('admin')
            <div class="row" id="chartsContainer">
                <div class="col-lg-12 grid-margin grid-margin-lg-0 stretch-card">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Uncomplete Document Review</h4>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Show Details</label>
                            </div>
                        </div>
                        <div id="departmentChart" style="height: 417px;"></div>
                    </div>
                </div>
            </div>
        @else
            <div class="row mt-4" id="guestChartsContainer">
                <div class="col-lg-12 grid-margin grid-margin-lg-0 stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Document Status Department</h4>
                            <div id="guestRoleChart" style="height: 417px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endrole
        <div id="documentControlTable" class="mt-3"></div>
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
        function drawColumnChart(containerId, departmentNames, departmentCounts) {
            // Filter untuk mengecualikan "Aisin Indonesia"
            let filteredNames = [];
            let filteredCounts = [];

            for (let i = 0; i < departmentNames.length; i++) {
                if (departmentNames[i] !== 'Aisin Indonesia') {
                    filteredNames.push(departmentNames[i]); // Menambahkan nama departemen kecuali Aisin Indonesia
                    filteredCounts.push(departmentCounts[
                        i]); // Menambahkan count dokumen kecuali yang terkait dengan Aisin Indonesia
                }
            }

            Highcharts.chart(containerId, {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Department Document Review Monitoring'
                },
                xAxis: {
                    categories: filteredNames // Mengambil nama departemen yang sudah difilter
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Number of documents'
                    }
                },
                series: [{
                    name: 'Review Document Count',
                    data: filteredCounts, // Mengisi data dengan total dokumen per departemen yang sudah difilter
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

        function drawPieChart(containerId, statusData) {
            Highcharts.chart(containerId, {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: '{{ Auth::user()->departemen->nama_departemen }}'
                },
                series: [{
                    name: 'Total Documents',
                    colorByPoint: true,
                    data: statusData
                }],
                tooltip: {
                    pointFormat: '<b>{point.name}</b>: {point.y}'
                }
            });
        }

        const departmentNames = @json($departments->pluck('nama_departemen'));

        // Ambil data total dokumen per departemen dari PHP ke JavaScript
        const departmentCounts = @json(array_values($departmentTotals)); // Ambil total dokumen

        // Ambil data status dokumen untuk pengguna guest
        const statusCounts = @json($statusCounts); // Data untuk pie chart status dokumen

        // Menggambar chart dengan nama departemen dan total dokumen
        @role('admin')
            drawColumnChart('departmentChart', departmentNames, departmentCounts);
        @else
            // Format data untuk pie chart dari statusCounts
            const statusData = [{
                    name: 'Uncomplete',
                    y: statusCounts.Uncomplete || 0
                },
                {
                    name: 'Submitted',
                    y: statusCounts.Submitted || 0
                },
                {
                    name: 'Completed',
                    y: statusCounts.Completed || 0
                },
                {
                    name: 'Rejected',
                    y: statusCounts.Rejected || 0
                }
            ];
            drawPieChart('guestRoleChart', statusData);
        @endrole
        // Event listener untuk toggle switch
        document.getElementById('flexSwitchCheckDefault').addEventListener('change', function() {
            if (this.checked) {
                // Kirim AJAX request ketika switch dinyalakan
                $.ajax({
                    url: '/document_review/details', // URL untuk mengambil data
                    type: 'GET', // Tipe request
                    dataType: 'json', // Format data yang diharapkan dari server
                    success: function(data) {
                        displayDocumentControlTable(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching data:', textStatus, errorThrown);
                    }
                });
            } else {
                // Kosongkan tabel ketika switch dimatikan
                document.getElementById('documentControlTable').innerHTML = '';
            }
        });

        // Fungsi untuk menampilkan tabel data dokumen
        function displayDocumentControlTable(data) {
            // Buat struktur tabel
            let tableHtml = `
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Document Name</th>
                    <th>Department</th>
                    <th>Review Date</th>
                </tr>
            </thead>
            <tbody>
    `;

            // Mendapatkan tanggal saat ini
            const currentDate = new Date();

            // Loop melalui data dan buat baris untuk setiap dokumen
            data.forEach((doc, index) => {
                // Memeriksa apakah status "Uncomplete" dan tanggal review sudah terlewat
                let warningIcon = '';
                if (doc.status === 'Uncomplete' && new Date(doc.review) < currentDate) {
                    warningIcon =
                        '<span class="text-danger" title="Document is overdue!">⚠️</span>'; // Ikon peringatan
                }

                tableHtml += `
            <tr>
                <td>${index + 1}</td>
                <td>${doc.name} ${warningIcon}</td>
                <td>${doc.department}</td>
                <td>${doc.review}</td>
            </tr>
        `;
            });

            tableHtml += `
            </tbody>
        </table>
    `;

            // Sisipkan tabel ke dalam placeholder HTML
            document.getElementById('documentControlTable').innerHTML = tableHtml;
        }
    </script>

@endsection
@push('styles')
    <style>
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush
