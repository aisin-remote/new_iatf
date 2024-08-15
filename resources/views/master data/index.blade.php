@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Master Data</h3>
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

        <div class="row">
            <div class="col-md-4 mb-4 stretch-card transparent">
                <div class="card card-tale">
                    <div class="card-body">
                        <a href="{{ route('masterdata.departemen') }}"
                            style="display: block; text-decoration: none; color: inherit;">
                            <p class="mb-4">Master Data</p>
                            <p class="fs-30 mb-2">Departemen</p>
                            <p>{{ $departemenCount }}</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                    <div class="card-body">
                        <a href="{{ route('masterdata.kodeproses') }}"
                            style="display: block; text-decoration: none; color: inherit;">
                            <p class="mb-4">Master Data</p>
                            <p class="fs-30 mb-2">Process Code</p>
                            <p>{{ $rulecodeCount }}</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                    <div class="card-body">
                        <a href="{{ route('masterdata.role') }}" style="display: block; text-decoration: none; color: inherit;">
                            <p class="mb-4">Master Data</p>
                            <p class="fs-30 mb-2">Role</p>
                            <p>{{ $roleCount }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
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
