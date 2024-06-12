@extends('layouts.app')
@section('title', 'Dashboard-admin')
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
                    @foreach ($countByStatusAndType->groupBy('tipe_dokumen') as $type => $typeData)
                        <div class="col-lg-3 grid-margin grid-margin-lg-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $type }}</h4>
                                    <canvas id="statusPieChart{{ $type }}" width="400" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <h4 class="mb-4">Prosedur</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'PROSEDUR')->first()->count ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">Standar</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'STANDAR')->first()->count ?? 0 }}</p>
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
                                <h4 class="mb-4">WI</h4>
                                <p class="fs-30 mb-2">
                                    {{ $countByType->where('tipe_dokumen', 'WI')->first()->count ?? 0 }}</p>
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
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Advanced Table</p>
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
                                                <th>status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dokumenall as $doc)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $doc->nomor_dokumen }}</td>
                                                    <td>{{ $doc->nama_dokumen }}</td>
                                                    <td>{{ $doc->revisi_log }}</td>
                                                    <td>{{ $doc->tgl_upload }}</td>
                                                    <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                                    <td>{{ $doc->status }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
        // Function to update the date and time
        function updateDateTime() {
            // Get the current date and time
            var currentDate = new Date();

            // Format the date as desired (e.g., "YYYY-MM-DD HH:MM:SS")
            var formattedDate = currentDate.toLocaleString();

            // Update the text of the element with the current date and time
            document.getElementById('currentDateText').textContent = formattedDate;
        }

        // Update the date and time initially when the page loads
        updateDateTime();

        // Update the date and time every second (1000 milliseconds)
        setInterval(updateDateTime, 1000);
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($countByStatusAndType->groupBy('tipe_dokumen') as $type => $typeData)
                var ctx{{ $type }} = document.getElementById('statusPieChart{{ $type }}')
                    .getContext('2d');
                var chartData{{ $type }} = {
                    labels: ['Waiting', 'Approve'],
                    datasets: [{
                        data: [
                            {{ $typeData->where('status', 'waiting')->first()->count ?? 0 }},
                            {{ $typeData->where('status', 'approve')->first()->count ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                        ],
                        borderWidth: 1
                    }]
                };

                var statusPieChart{{ $type }} = new Chart(ctx{{ $type }}, {
                    type: 'pie',
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
                            }
                        }
                    }
                });
            @endforeach
        });
    </script>
@endsection
