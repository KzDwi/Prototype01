<?php
session_start();
require_once 'functions.php'; // Asumsi file ini ada

// Cek login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// FUNGSI LOAD & SAVE DATA
$json_file = 'data/pesan_layanan.json';

function loadMessages() {
    global $json_file;
    if (file_exists($json_file)) {
        $data = json_decode(file_get_contents($json_file), true);
        return $data ?: [];
    }
    return [];
}

function saveMessages($data) {
    global $json_file;
    $dir = dirname($json_file);
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    return file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$pesan_sukses = '';
$pesan_error = '';

// HANDLE HAPUS PESAN
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    $messages = loadMessages();
    
    $new_messages = array_filter($messages, function($msg) use ($id_to_delete) {
        return ($msg['id'] ?? '') !== $id_to_delete;
    });
    
    $new_messages = array_values($new_messages);
    
    if (saveMessages($new_messages)) {
        header("Location: layanan_pesan.php?success=deleted");
        exit;
    } else {
        $pesan_error = "Gagal menghapus pesan.";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 'deleted') {
    $pesan_sukses = "Pesan berhasil dihapus.";
}

// === LOGIC FILTER & PAGINATION ===
$all_messages = loadMessages();

// Ambil parameter filter dari URL
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_layanan = isset($_GET['layanan']) ? $_GET['layanan'] : '';
$start_date     = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date       = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Proses Filtering
$filtered_messages = array_filter($all_messages, function($item) use ($search_keyword, $filter_layanan, $start_date, $end_date) {
    $match = true;

    // 1. Filter Keyword
    if ($search_keyword) {
        $keyword = strtolower($search_keyword);
        $nama = strtolower($item['nama'] ?? '');
        $email = strtolower($item['email'] ?? '');
        if (strpos($nama, $keyword) === false && strpos($email, $keyword) === false) {
            $match = false;
        }
    }

    // 2. Filter Jenis Layanan
    if ($match && $filter_layanan) {
        if (($item['layanan'] ?? '') !== $filter_layanan) {
            $match = false;
        }
    }

    // 3. Filter Tanggal
    if ($match && ($start_date || $end_date)) {
        $msg_date = date('Y-m-d', strtotime($item['tanggal'] ?? 'now'));
        if ($start_date && $msg_date < $start_date) $match = false;
        if ($end_date && $msg_date > $end_date) $match = false;
    }

    return $match;
});

// Reset index array setelah filter agar pagination akurat
$filtered_messages = array_values($filtered_messages);

// --- LOGIC PAGINATION ---
$limit = 10; // Jumlah pesan per halaman
$total_data = count($filtered_messages);
$total_pages = ceil($total_data / $limit);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;
$paginated_messages = array_slice($filtered_messages, $offset, $limit);

// Build query string untuk link pagination (agar filter tetap ikut)
$query_params = $_GET;
unset($query_params['page']); // Hapus page lama
$query_string = http_build_query($query_params);
$link_base = "layanan_pesan.php?" . ($query_string ? $query_string . "&" : "");

// Ambil daftar unik jenis layanan untuk dropdown
$daftar_layanan = array_unique(array_column($all_messages, 'layanan'));
sort($daftar_layanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Masuk Layanan - Admin</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === STYLE PAGINATION === */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px;
        }
        
        .page-item .page-link {
            display: block;
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            color: #003399;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .page-item.active .page-link {
            background: #003399;
            color: white;
            border-color: #003399;
        }
        
        .page-item.disabled .page-link {
            background: #f8f9fa;
            color: #ccc;
            pointer-events: none;
            cursor: default;
        }
        
        .page-item .page-link:hover:not(.active) {
            background: #f1f1f1;
        }

        .data-summary {
            font-size: 14px;
            color: #666;
        }

        /* Filter Styles (Sama seperti sebelumnya) */
        .filter-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .filter-grid { display: grid; grid-template-columns: 2fr 1.5fr 1fr 1fr auto; gap: 15px; align-items: end; }
        .filter-item label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 13px; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; height: 42px; box-sizing: border-box; }
        .btn-group-filter { display: flex; gap: 10px; }
        .btn-filter { background: #003399; color: white; border: none; padding: 0 20px; border-radius: 4px; cursor: pointer; font-weight: 500; height: 42px; display: inline-flex; align-items: center; justify-content: center; transition: background 0.2s; }
        .btn-filter:hover { background: #002280; }
        .btn-reset { background: #6c757d; color: white; text-decoration: none; padding: 0 15px; border-radius: 4px; font-size: 14px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border: none; }
        .btn-reset:hover { background: #5a6268; }

        @media (max-width: 992px) { .filter-grid { grid-template-columns: 1fr 1fr; } .btn-group-filter { grid-column: span 2; justify-content: flex-end; } }
        @media (max-width: 576px) { .filter-grid { grid-template-columns: 1fr; } .btn-group-filter { grid-column: span 1; width: 100%; } .btn-filter, .btn-reset { flex: 1; } .pagination-container { flex-direction: column; gap: 15px; } }

        /* Table Styles */
        .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow-x: auto; }
        .custom-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .custom-table th { background: #003399; color: white; padding: 15px; text-align: left; font-size: 14px; }
        .custom-table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: top; color: #333; font-size: 14px; }
        .custom-table tr:hover { background-color: #f8f9fa; }
        .badge-layanan { background: #e3f2fd; color: #003399; border: 1px solid #bbdefb; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .meta-info { font-size: 12px; color: #888; margin-top: 5px; }
        .pesan-text { line-height: 1.5; color: #555; max-height: 100px; overflow-y: auto; }
        .btn-delete { background: #ffebee; color: #dc3545; border: 1px solid #ffcdd2; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-block; }
        .btn-delete:hover { background: #dc3545; color: white; }
        .empty-state { padding: 40px; text-align: center; color: #888; }
        .empty-state i { font-size: 48px; margin-bottom: 15px; color: #ddd; }
    </style>
</head>
<body>
    <nav class="navbar default-layout">
        <div class="navbar-brand-wrapper">
            <a class="navbar-brand" href="#"><span class="brand-logo">Menu Admin</span></a>
        </div>
        <div class="navbar-menu-wrapper">
            <div class="navbar-brand">
                <img src="assets/logo-kabupaten.png" alt="Logo">
                <span class="brand-text">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</span>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="admin-dashboard.php" class="sidebar-menu-link">
                        <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link">
                        <span class="icon icon-news"></span>
                        <span class="sidebar-menu-text">Kelola Berita</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="layanan_pesan.php" class="sidebar-menu-link active"> <span class="icon icon-envelope"></span>
                        <span class="sidebar-menu-text">Pesan Pengaduan</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-pengaturan.php" class="sidebar-menu-link">
                        <span class="icon icon-settings"></span>
                        <span class="sidebar-menu-text">Pengaturan Konten</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="?logout=true" class="btn-sidebar-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                    <span>Keluar Aplikasi</span>
                </a>
            </div>
        </aside>

        <main class="content-wrapper">
            <div class="card mb-3" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div class="card-header" style="background: none; border: none; padding: 0;">
                    <h3 style="margin: 0; color: #333;">Kotak Masuk Layanan</h3>
                    <p class="text-muted mb-0" style="color: #666; margin-top: 5px;">Kelola pertanyaan dan permohonan dari formulir layanan masyarakat</p>
                </div>
            </div>

            <?php if ($pesan_sukses): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $pesan_sukses; ?>
                </div>
            <?php endif; ?>

            <?php if ($pesan_error): ?>
                <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $pesan_error; ?>
                </div>
            <?php endif; ?>

            <div class="filter-container">
                <form method="GET" action="layanan_pesan.php" class="filter-grid">
                    <div class="filter-item">
                        <label>Cari Nama / Email</label>
                        <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </div>
                    <div class="filter-item">
                        <label>Jenis Layanan</label>
                        <select name="layanan" class="form-control">
                            <option value="">Semua Layanan</option>
                            <?php foreach ($daftar_layanan as $jenis): ?>
                                <option value="<?php echo htmlspecialchars($jenis); ?>" <?php echo ($filter_layanan === $jenis) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($jenis); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="filter-item">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="btn-group-filter">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-filter" style="margin-right: 5px;"></i> Terapkan
                        </button>
                        <?php if ($search_keyword || $filter_layanan || $start_date || $end_date): ?>
                            <a href="layanan_pesan.php" class="btn-reset">
                                <i class="fas fa-undo" style="margin-right: 5px;"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <?php if (count($paginated_messages) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%;">Pengirim</th>
                                <th style="width: 15%;">Layanan</th>
                                <th style="width: 45%;">Isi Pesan</th>
                                <th style="width: 10%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Nomor urut berdasarkan halaman
                            $no = $offset + 1;
                            foreach ($paginated_messages as $msg): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($msg['nama'] ?? '-'); ?></strong><br>
                                    <span style="color: #666; font-size: 13px;">
                                        <i class="far fa-envelope"></i> <?php echo htmlspecialchars($msg['email'] ?? '-'); ?>
                                    </span>
                                    <div class="meta-info">
                                        <i class="far fa-clock"></i> 
                                        <?php 
                                            $tgl = $msg['tanggal'] ?? '';
                                            echo $tgl ? date('d M Y, H:i', strtotime($tgl)) : '-'; 
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-layanan">
                                        <?php echo htmlspecialchars($msg['layanan'] ?? 'Umum'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="pesan-text">
                                        <?php echo nl2br(htmlspecialchars($msg['pesan'] ?? '')); ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <a href="?delete_id=<?php echo $msg['id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Yakin ingin menghapus pesan ini?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-folder-open"></i>
                        <h4>Tidak ada pesan ditemukan</h4>
                        <p>Cobalah ubah kata kunci pencarian atau rentang tanggal filter Anda.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="pagination-container">
                <div class="data-summary">
                    Menampilkan <?php echo count($paginated_messages); ?> dari total <?php echo $total_data; ?> pesan.
                </div>
                
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $link_base . "page=" . ($page - 1); ?>" tabindex="-1">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo $link_base . "page=" . $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $link_base . "page=" . ($page + 1); ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
            <div class="dashboard-footer" style="margin-top: 25px; text-align: center; color: #999; font-size: 0.8rem;">
                <p>© <?php echo date('Y'); ?> Admin Panel - Disdikbud Paser</p>
            </div>
            </main>
    </div>

    <script>
        // Dropdown script
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            if(userDropdown && userDropdownMenu) {
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    userDropdownMenu.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>