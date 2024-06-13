@extends('layouts.app')
@section('title', 'Dokumen-Iatf')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-6">
                <div class="card" style="height: 400px">
                    <div class="card-body">
                        <h4 class="card-title">Dashboard Dokumen Rule</h4>
                        <a href="{{ route('dashboard.rule') }}" class="btn btn-primary">Pilih</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" style="height: 400px">
                    <div class="card-body">
                        <h4 class="card-title">Dashboard Dokumen Proses</h4>
                        <a href="" class="btn btn-primary">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
