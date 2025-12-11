<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints') ?>">Daftar Laporan</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints/' . $complaint->id) ?>">Detail #<?= $complaint->id ?></a></li>
        <li class="breadcrumb-item active">Chat</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <!-- Chat Card -->
        <div class="card" style="height: calc(100vh - 250px);">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-comments"></i> Chat Internal
                        </h5>
                        <small>Laporan #<?= $complaint->id ?> - <?= esc($complaint->title) ?></small>
                    </div>
                    <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" 
                       class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <!-- Chat Messages Area -->
            <div class="card-body" id="chatMessages" style="overflow-y: auto; height: calc(100% - 140px);">
                <div class="text-center py-3">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Memuat pesan...</p>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="card-footer bg-light">
                <form id="chatForm" onsubmit="sendMessage(event)">
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control" 
                               placeholder="Ketik pesan..." required>
                        <button type="submit" class="btn btn-success" id="sendButton">
                            <i class="fas fa-paper-plane"></i> Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Complaint Info -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Info Laporan
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status:</small><br>
                    <?= $complaint->getStatusBadge() ?>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Prioritas:</small><br>
                    <?= $complaint->getPriorityBadge() ?>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Aplikasi:</small><br>
                    <strong><?= esc($complaint->application_name) ?></strong>
                </div>
                <?php if ($admin): ?>
                    <div class="mb-3">
                        <small class="text-muted">Ditangani Oleh:</small><br>
                        <div class="d-flex align-items-center mt-2">
                            <div class="user-avatar bg-info text-white me-2" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <?= strtoupper(substr($admin->full_name, 0, 2)) ?>
                            </div>
                            <strong><?= esc($admin->full_name) ?></strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-clock"></i>
                        <small>Menunggu admin untuk menangani laporan ini</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Info -->
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="fas fa-lightbulb text-warning"></i> Tips Chat
                </h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Jelaskan masalah dengan jelas
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Berikan informasi tambahan jika diminta
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Sopan dalam berkomunikasi
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Tunggu respon dari admin
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const complaintId = <?= $complaint->id ?>;
const currentUserId = <?= session()->get('user_id') ?>;
let isLoadingMessages = false;
let autoRefreshInterval = null;

// Load messages on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    
    // Auto refresh every 5 seconds
    autoRefreshInterval = setInterval(loadMessages, 45000);
});

// Load chat messages
function loadMessages() {
    if (isLoadingMessages) return;
    isLoadingMessages = true;

    fetch(`<?= base_url('user/complaints/') ?>${complaintId}/chat/fetch`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.chats);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        })
        .finally(() => {
            isLoadingMessages = false;
        });
}

// Display messages
function displayMessages(chats) {
    const container = document.getElementById('chatMessages');
    
    if (chats.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada pesan</p>
                <small class="text-muted">Mulai percakapan dengan mengirim pesan pertama</small>
            </div>
        `;
        return;
    }

    let html = '';
    chats.forEach(chat => {
        const isOwn = chat.is_own;
        const alignClass = isOwn ? 'text-end' : 'text-start';
        const bgClass = isOwn ? 'bg-success text-white' : 'bg-light';
        const roleLabel = chat.user_role === 'admin' || chat.user_role === 'superadmin' ? 
            '<span class="badge bg-info">Admin</span>' : 
            '<span class="badge bg-primary">User</span>';

        html += `
            <div class="mb-3 ${alignClass}">
                <div class="d-inline-block" style="max-width: 70%;">
                    <div class="small mb-1">
                        <strong>${chat.user_name}</strong> ${roleLabel}
                    </div>
                    <div class="p-3 rounded ${bgClass}" style="word-wrap: break-word;">
                        ${escapeHtml(chat.message)}
                    </div>
                    <div class="small text-muted mt-1">
                        ${chat.time_diff}
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

// Send message
function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('messageInput');
    const button = document.getElementById('sendButton');
    const message = input.value.trim();

    if (!message) return;

    // Disable input
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

    // Send via AJAX
    fetch(`<?= base_url('user/complaints/') ?>${complaintId}/chat/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `message=${encodeURIComponent(message)}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages();
        } else {
            alert(data.message || 'Gagal mengirim pesan');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Terjadi kesalahan saat mengirim pesan');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim';
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Clear interval when leaving page
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
<?= $this->endSection() ?>
