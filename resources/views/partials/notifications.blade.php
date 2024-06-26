@foreach ($notifications as $notification)
    <a class="dropdown-item preview-item">
        <div class="preview-thumbnail">
            <div class="preview-icon bg-info">
                <i class="ti-bell mx-0"></i>
            </div>
        </div>
        <div class="preview-item-content">
            <h6 class="preview-subject font-weight-normal">
                {{ $notification->data['nama_dokumen'] ?? 'Dokumen Tidak Tersedia' }}
            </h6>
            <p class="font-weight-light small-text mb-0 text-muted">
                Status: {{ $notification->data['status'] ?? 'Status Tidak Tersedia' }}
            </p>
            <p class="font-weight-light small-text mb-0 text-muted">
                Komentar: {{ $notification->data['comment'] ?? 'Komentar Tidak Tersedia' }}
            </p>
            <p class="font-weight-light small-text mb-0 text-muted">
                <a href="{{ url('/dokumen/' . ($notification->data['dokumen_id'] ?? '#')) }}">Lihat Dokumen</a>
            </p>
        </div>
    </a>
@endforeach
