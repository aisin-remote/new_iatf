@extends('layouts.app')
@section('title', 'Document Rule')
@section('content')
    {{-- @php
        dd(session('active_departemen_name'));
    @endphp --}}
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Document {{ $jenis }} - {{ $tipe }}</h4>
                        <p class="card-description"></p>
                        <div class="d-flex justify-content-end mb-3">
                            <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                placeholder="Search..." style="width: 300px;">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploaddraftModal">
                                Upload Draft
                            </button>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger" id="error-alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Document Number</th>
                                        <th>Document Title</th>
                                        <th>Revision</th>
                                        <th>Upload Date</th>
                                        <th>status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($indukDokumenList as $doc)
                                        @if ($doc->statusdoc != 'active')
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $doc->nomor_dokumen }}</td>
                                                <td>{{ $doc->nama_dokumen }}</td>
                                                <td>{{ $doc->revisi_log }}</td>
                                                <td>{{ $doc->tgl_upload }}</td>
                                                <td>{{ $doc->status }}</td>
                                                <td>
                                                    <!-- Tombol Download -->
                                                    <a href="{{ route('download.rule', ['id' => $doc->id]) }}"
                                                        class="btn btn-success btn-sm" target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploaddraftModal" tabindex="-1" role="dialog" aria-labelledby="uploaddraftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('tambah.rule') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploaddraftModalLabel">Upload Draft</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form untuk Upload Draft -->
                        <div class="form-group">
                            <label for="nama_dokumen">Document Title</label>
                            <p>(Example: Penanganan Abnormality)</p>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor_list">Number Document</label>
                            <p>(Example: 012)</p>
                            <input type="text" class="form-control" id="nomor_list" name="nomor_list" required>
                        </div>
                        <div class="form-group">
                            <label for="status_dokumen">Document Status</label>
                            <select class="form-control" id="status_dokumen" name="status_dokumen" required>
                                <option value="">Select Document Status</option>
                                <option value="baru">New</option>
                                <option value="revisi">Revision</option>
                            </select>
                        </div>
                        <div class="form-group" id="revisi_ke_group" style="display: none;">
                            <label for="revisi_ke">Revision Number</label>
                            <input type="number" class="form-control" id="revisi_ke" name="revisi_ke">
                        </div>

                        <div class="form-group">
                            <label for="rule_id">Process Code</label>
                            <select class="form-control" id="rule_id" name="rule_id" required>
                                <option value="">Pilih Kode Proses</option>
                                @foreach ($kodeProses as $item)
                                    <option value="{{ $item['id'] }}">
                                        {{ $item['kode_proses'] }} - {{ $item['nama_proses'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Input untuk Line dan Model Product, disembunyikan secara default -->
                        <div id="prd_wis_group" style="display: none;">
                            <div class="form-group">
                                <label for="line">Line</label>
                                <p>(Example: PC6A)</p>
                                <input type="text" class="form-control" id="line" name="line">
                            </div>
                            <div class="form-group">
                                <label for="model_product">Model Product</label>
                                <p>(Example: 560B)</p>
                                <input type="text" class="form-control" id="model_product" name="model_product">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Share Document To:</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select_all">
                                            <label class="form-check-label" for="select_all">Select All</label>
                                        </div>
                                    </div>
                                    @foreach ($uniqueDepartemens as $dept)
                                        <div class="col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input kode_departemen_checkbox" type="checkbox"
                                                    id="dept_{{ $dept->code }}" name="kode_departemen[]"
                                                    value="{{ $dept->code }}">
                                                <label class="form-check-label"
                                                    for="dept_{{ $dept->code }}">{{ $dept->code }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="file">Choose File (Word, Excel)</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                            @if ($errors->has('file'))
                                <div class="alert alert-danger mt-2">
                                    {{ $errors->first('file') }}
                                </div>
                            @endif
                        </div>
                        <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}" id="jenis_dokumen">
                        <input type="hidden" name="tipe_dokumen" value="{{ $tipe }}" id="tipe_dokumen">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Event handler untuk pencarian
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#documentTableBody tr').each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    row.toggle(text.indexOf(value) > -1);
                });
            });

            // Menghandle pagination agar pencarian bekerja
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    $('#documentTableBody').html($(data).find('#documentTableBody').html());
                });
            });
        });
    </script>
    <script>
        document.getElementById('status_dokumen').addEventListener('change', function() {
            var revisiGroup = document.getElementById('revisi_ke_group');
            if (this.value === 'revisi') {
                revisiGroup.style.display = 'block';
                document.getElementById('revisi_ke').required = true;
            } else {
                revisiGroup.style.display = 'none';
                document.getElementById('revisi_ke').required = false;
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dapatkan elemen checkbox "Select All"
            const selectAllCheckbox = document.getElementById('select_all');

            // Dapatkan semua elemen checkbox departemen
            const checkboxes = document.querySelectorAll('input[name="kode_departemen[]"]');

            // Tambahkan event listener untuk checkbox "Select All"
            selectAllCheckbox.addEventListener('change', function() {
                // Set status semua checkbox departemen sesuai dengan status checkbox "Select All"
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Tambahkan event listener untuk setiap checkbox departemen
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Jika ada satu checkbox yang tidak dipilih, hapus centang dari "Select All"
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    }

                    // Jika semua checkbox departemen dipilih, beri centang pada "Select All"
                    if (document.querySelectorAll('input[name="kode_departemen[]"]:checked')
                        .length === checkboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tipeDokumen = document.getElementById('tipe_dokumen');
            var prdWisGroup = document.getElementById('prd_wis_group');

            // Dapatkan kode departemen dari user yang sedang login
            var kodeDepartemen = @json(Auth::user()->departemen->code);

            // Fungsi untuk menampilkan atau menyembunyikan field berdasarkan tipe dokumen dan departemen
            function togglePrdWisFields() {
                if (tipeDokumen.value === 'WIS' && kodeDepartemen === 'PRD') {
                    prdWisGroup.style.display = 'block';
                } else {
                    prdWisGroup.style.display = 'none';
                }
            }

            // Panggil fungsi saat halaman dimuat
            togglePrdWisFields();

            // Pastikan field diperbarui ketika tipe dokumen berubah
            tipeDokumen.addEventListener('change', togglePrdWisFields);
        });
        setTimeout(function() {
            document.getElementById('error-alert')?.remove();
        }, 3000);
    </script>
@endsection
