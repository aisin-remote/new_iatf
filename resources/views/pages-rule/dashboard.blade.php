@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Welcome {{ Auth::user()->departemen->nama_departemen }}</h3>
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
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">WI</h4>
                                <canvas id="statusBarChartWI" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">WIS</h4>
                                <canvas id="statusBarChartWIS" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Standar</h4>
                                <canvas id="statusBarChartSTANDAR" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Prosedur</h4>
                                <canvas id="statusBarChartPROSEDUR" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Div untuk count -->
        <div class="row">
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <h4 class="mb-4">WI</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'WI')->first()->count ?? 0 }}
                                </p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">WIS</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'WIS')->first()->count ?? 0 }}</p>
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
                                    {{ $countByType->where('tipe_dokumen', 'STANDAR')->first()->count ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">Prosedur</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'PROSEDUR')->first()->count ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row untuk filter dan pencarian -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <!-- Tombol untuk membuka modal filter -->
                    <div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#filterModal">Filter
                            <i class="fa-solid fa-filter"></i></button>
                        <a href="{{ route('download.excel') }}" class="btn btn-primary" style="margin-right: 4px">
                            Download excel
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                    <!-- Search Input -->
                    <div>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Filter -->
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('filter.documents') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterModalLabel">Filter <i class="fa-solid fa-filter"></i></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Filter berdasarkan Tanggal Upload -->
                            <div class="row my-2">
                                <div class="col-4">
                                    <label class="col-form-label">Start Date / Upload Date</label>
                                </div>
                                <div class="col">
                                    <input type="text" name="date_from" class="form-control input"
                                        placeholder="From">
                                </div>
                                <label class="col-form-label px-3">to</label>
                                <div class="col">
                                    <input type="text" name="date_to" class="form-control input" placeholder="To">
                                </div>
                            </div>

                            <!-- Filter berdasarkan Tipe Dokumen -->
                            <div class="row my-2">
                                <div class="col-4">
                                    <label class="col-form-label">Tipe Dokumen</label>
                                </div>
                                <div class="col">
                                    <select name="tipe_dokumen_id" id="tipe_dokumen_id" class="form-control select2"
                                        style="width: 100%;">
                                        <option value="" selected>Select Tipe Dokumen</option>
                                        @foreach ($tipeDokumen as $dokumen)
                                            <option value="{{ $dokumen->id }}"
                                                {{ request('tipe_dokumen_id') == $dokumen->id ? 'selected' : '' }}>
                                                {{ $dokumen->tipe_dokumen }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Filter berdasarkan Departemen (Hanya untuk admin) -->
                            @role('admin')
                                <div class="row my-2">
                                    <div class="col-4">
                                        <label class="col-form-label">Departemen</label>
                                    </div>
                                    <div class="col">
                                        <select name="departemen_id" id="departemen_id" class="form-control select2"
                                            style="width: 100%;">
                                            <option value="" selected>Select Departemen</option>
                                            @foreach ($allDepartemen as $departemen)
                                                <option value="{{ $departemen->nama_departemen }}"
                                                    {{ request('departemen_id') == $departemen->nama_departemen ? 'selected' : '' }}>
                                                    {{ $departemen->nama_departemen }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Filter berdasarkan Status Dokumen -->
                                <div class="row my-2">
                                    <div class="col-4">
                                        <label class="col-form-label">Status Doc</label>
                                    </div>
                                    <div class="col">
                                        <select name="statusdoc" id="statusdoc" class="form-control select2"
                                            style="width: 100%;">
                                            <option value="" selected>Pilih Status Doc</option>
                                            <option value="active" {{ request('statusdoc') == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <!-- Tambahkan opsi status lain jika diperlukan -->
                                        </select>
                                    </div>
                                </div>
                            @endrole
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bagian tabel -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Header dan Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="card-title">Dokumen Final</p>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <!-- Form untuk memilih jumlah entri per halaman -->
                                <form method="GET" action="{{ route('dashboard.rule') }}">
                                    <label class="mb-0">Show
                                        <select name="per_page" aria-controls="data-table-x"
                                            class="custom-select custom-select-sm form-control form-control-sm"
                                            onchange="this.form.submit()">
                                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10
                                            </option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25
                                            </option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                            </option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                            </option>
                                        </select> entries
                                    </label>
                                </form>
                            </div>
                        </div>
                        <!-- Tabel Dokumen -->
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor Dokumen</th>
                                                <th>Nama Dokumen</th>
                                                <th>Revisi</th>
                                                <th>Tanggal Upload</th>
                                                <th>Departemen</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentTableBody">
                                            @forelse ($dokumenall as $doc)
                                                <tr>
                                                    <td>{{ ($dokumenall->currentPage() - 1) * $dokumenall->perPage() + $loop->iteration }}
                                                    </td>
                                                    <td>{{ $doc->nomor_dokumen }}</td>
                                                    <td>{{ $doc->nama_dokumen }}</td>
                                                    <td>{{ $doc->revisi_log }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($doc->tgl_upload)->format('d-m-Y') }}</td>
                                                    <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                                    <td>{{ $doc->statusdoc }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No documents found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <!-- Paginasi -->
                                    <div class="d-flex justify-content-center">
                                        {{ $dokumenall->links('pagination::bootstrap-4', ['paginationClass' => 'pagination-sm']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateDateTime() {
            var currentDate = new Date();
            var formattedDate = currentDate.toLocaleString();
            document.getElementById('currentDateText').textContent = formattedDate;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        document.addEventListener("DOMContentLoaded", function() {
            @foreach (['WI', 'WIS', 'STANDAR', 'PROSEDUR'] as $type)
                @php
                    $typeData = $countByStatusAndType->where('tipe_dokumen', $type);
                    $waitingCheck = $typeData->where('status', 'Waiting check by MS')->first()->count ?? 0;
                    $finishCheck = $typeData->where('status', 'Finish check by MS')->first()->count ?? 0;
                    $approve = $typeData->where('status', 'Approve by MS')->first()->count ?? 0;
                    $totalDocuments = $typeData->sum('count');
                @endphp

                console.log('Data for {{ $type }}:', {
                    waitingCheck: {{ $waitingCheck }},
                    finishCheck: {{ $finishCheck }},
                    approve: {{ $approve }},
                    totalDocuments: {{ $totalDocuments }}
                });

                var ctx{{ $type }} = document.getElementById('statusBarChart{{ $type }}')
                    .getContext('2d');
                var data{{ $type }} = [
                    {{ $waitingCheck }},
                    {{ $finishCheck }},
                    {{ $approve }},
                    {{ $totalDocuments }}
                ];
                var labels{{ $type }} = ['Waiting check by MS', 'Finish check by MS', 'Approve by MS',
                    'Total'
                ];

                if (data{{ $type }}.every(value => value === 0)) {
                    labels{{ $type }} = ['No data available'];
                    data{{ $type }} = [0];
                }

                var chartData{{ $type }} = {
                    labels: labels{{ $type }},
                    datasets: [{
                        label: 'Number of Documents',
                        data: data{{ $type }},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)', // Waiting check by MS
                            'rgba(54, 162, 235, 0.2)', // Finish check by MS
                            'rgba(255, 140, 0, 0.2)', // Approve by MS
                            'rgba(0, 255, 0, 0.2)' // Total
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)', // Waiting check by MS
                            'rgba(54, 162, 235, 1)', // Finish check by MS
                            'rgba(255, 140, 0, 1)', // Approve by MS
                            'rgba(0, 255, 0, 1)' // Total
                        ],
                        borderWidth: 1
                    }]
                };

                new Chart(ctx{{ $type }}, {
                    type: 'bar',
                    data: chartData{{ $type }},
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Documents Status for {{ $type }}'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.label + ': ' + tooltipItem.raw
                                            .toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            @endforeach

            document.getElementById('applyFilter').addEventListener('click', function() {
                filterAndSearch();
                $('#filterModal').modal('hide');
            });

            document.getElementById('searchInput').addEventListener('input', function() {
                filterAndSearch();
            });

            function filterAndSearch() {
                var filterStatus = document.getElementById('filterStatus').value.toLowerCase();
                var searchInput = document.getElementById('searchInput').value.toLowerCase();
                var tableRows = document.querySelectorAll('#documentTableBody tr');

                tableRows.forEach(function(row) {
                    var status = row.children[6].textContent.toLowerCase();
                    var text = row.textContent.toLowerCase();

                    if ((filterStatus === '' || status.includes(filterStatus)) && text.includes(
                            searchInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // Tangkap event saat tombol Apply Filter diklik
            $('#applyFilter').click(function() {
                // Ambil nilai input dari modal
                var date_from = $('input[name="date_from"]').val();
                var date_to = $('input[name="date_to"]').val();
                var tipe_dokumen_id = $('#tipe_dokumen_id').val();
                var departemen_id = $('#departemen_id').val();
                var statusdoc = $('#statusdoc').val();

                // Kirim data filter ke controller menggunakan Ajax
                $.ajax({
                    url: '{{ route('filter.documents') }}', // Ganti dengan route yang sesuai
                    type: 'GET',
                    data: {
                        date_from: date_from,
                        date_to: date_to,
                        tipe_dokumen_id: tipe_dokumen_id,
                        departemen_id: departemen_id,
                        statusdoc: statusdoc
                    },
                    success: function(response) {
                        // Handle response dari server (misalnya, refresh halaman atau update konten)
                        console.log(response); // Cetak response ke console untuk debugging
                        // Anda bisa menambahkan logika untuk meng-update halaman atau bagian tertentu
                    },
                    error: function(xhr, status, error) {
                        // Handle jika terjadi error saat mengirim data
                        console.error(error); // Cetak error ke console untuk debugging
                    }
                });

                // Tutup modal setelah Apply Filter diklik
                $('#filterModal').modal('hide');
            });
        });
    </script>

@endsection
