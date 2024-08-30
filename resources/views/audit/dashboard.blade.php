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
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <h4 class="mb-4">WI</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">WIS</h4>
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
                                <h4 class="mb-4">Standard</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <h4 class="mb-4">Procedure</h4>
                            </div>
                        </div>
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
@endsection
