<?php
session_start();
require_once 'functions.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle upload gambar
$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_image'])) {
    $target_dir = "assets/uploads/";
    
    // Buat direktori jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES["upload_image"]["name"]);
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validasi file gambar
    $check = getimagesize($_FILES["upload_image"]["tmp_name"]);
    if ($check !== false) {
        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
            if (move_uploaded_file($_FILES["upload_image"]["tmp_name"], $target_file)) {
                $pesan_sukses = "Gambar berhasil diupload!";
            } else {
                $pesan_error = "Terjadi kesalahan saat mengupload gambar.";
            }
        } else {
            $pesan_error = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        }
    } else {
        $pesan_error = "File yang diupload bukan gambar.";
    }
}

// Handle delete gambar
if (isset($_GET['delete'])) {
    $file_to_delete = urldecode($_GET['delete']);
    
    // Validasi path untuk mencegah directory traversal
    if (strpos($file_to_delete, 'assets/uploads/') === 0 && file_exists($file_to_delete)) {
        if (unlink($file_to_delete)) {
            $pesan_sukses = "Gambar berhasil dihapus!";
        } else {
            $pesan_error = "Gagal menghapus gambar.";
        }
    } else {
        $pesan_error = "File tidak valid.";
    }
    
    header("Location: admin-uploads.php");
    exit;
}

// Ambil semua gambar yang sudah diupload
$upload_dir = 'assets/uploads/';
$uploaded_images = [];
if (file_exists($upload_dir)) {
    $images = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $uploaded_images = array_reverse($images); // Urutkan dari terbaru
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload - Admin</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <style>
        .uploads-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        @media (max-width: 992px) {
            .uploads-container {
                grid-template-columns: 1fr;
            }
        }
        
        .upload-area {
            border: 3px dashed #ddd;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .upload-area:hover {
            border-color: #003399;
            background: #e3f2fd;
        }
        
        .upload-icon {
            font-size: 4rem;
            color: #003399;
            margin-bottom: 15px;
            opacity: 0.7;
        }
        
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .image-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
            background: white;
        }
        
        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        
        .image-info {
            padding: 10px;
            background: white;
        }
        
        .image-name {
            font-size: 0.9rem;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 5px;
        }
        
        .image-size {
            font-size: 0.8rem;
            color: #666;
        }
        
        .image-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .image-item:hover .image-actions {
            opacity: 1;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: rgba(0,0,0,0.7);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .action-btn:hover {
            background: #003399;
        }
        
        .url-copy {
            margin-top: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            padding: 8px;
            display: none;
        }
        
        .url-copy.show {
            display: block;
        }
        
        .copy-btn {
            background: #003399;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar (sama seperti admin-dashboard.php) -->
    <nav class="navbar default-layout">
        <div class="navbar-brand-wrapper">
            <a class="navbar-brand" href="#">
                <span class="brand-logo">File Upload</span>
                <span class="brand-logo-mini">FU</span>
            </a>
        </div>
        <div class="navbar-menu-wrapper">
            <div class="navbar-brand">
                <img src="assets/logo-kabupaten.png" alt="Logo">
                <span class="brand-text">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</span>
            </div>
            
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <a class="nav-link" href="admin-dashboard.php">
                        <span class="icon icon-dashboard"></span> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-uploads.php">
                        <span class="icon icon-upload"></span> Upload
                    </a>
                </li>
                <li class="nav-item user-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button">
                        <span class="icon icon-user"></span>
                        <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </a>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <div class="dropdown-header">
                            <h6><?php echo htmlspecialchars($_SESSION['admin_username']); ?></h6>
                            <span class="text-muted">Administrator</span>
                        </div>
                        <a class="dropdown-item" href="admin-dashboard.php">
                            <span class="icon icon-dashboard"></span> Dashboard
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?logout=true">
                            <span class="icon icon-logout"></span> Logout
                        </a>
                    </div>
                </li>
            </ul>
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
                    <a href="admin-pengaturan.php" class="sidebar-menu-link">
                        <span class="icon icon-settings"></span>
                        <span class="sidebar-menu-text">Pengaturan</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-uploads.php" class="sidebar-menu-link active">
                        <span class="icon icon-upload"></span>
                        <span class="sidebar-menu-text">File Upload</span>
                    </a>
                </li>
            </ul>
        </aside>

        <main class="content-wrapper">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>File Upload Manager</h3>
                    <p class="text-muted mb-0">Upload dan kelola gambar untuk website</p>
                </div>
            </div>

            <?php if ($pesan_sukses): ?>
                <div class="alert alert-success"><?php echo $pesan_sukses; ?></div>
            <?php endif; ?>

            <?php if ($pesan_error): ?>
                <div class="alert alert-error"><?php echo $pesan_error; ?></div>
            <?php endif; ?>

            <div class="uploads-container">
                <!-- Upload Form -->
                <div>
                    <h4>Upload Gambar Baru</h4>
                    <form method="POST" enctype="multipart/form-data" id="uploadForm">
                        <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                            <div class="upload-icon">📤</div>
                            <h4>Klik atau seret file ke sini</h4>
                            <p>Format: JPG, PNG, GIF • Maksimal: 5MB</p>
                            <input type="file" id="fileInput" name="upload_image" accept="image/*" 
                                   onchange="previewImage(event)" style="display: none;">
                        </div>
                        
                        <div id="imagePreview" style="display: none; margin-bottom: 20px;">
                            <h5>Preview:</h5>
                            <img id="previewImg" src="#" alt="Preview" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; padding: 12px;">
                            <span class="icon icon-upload"></span> Upload Gambar
                        </button>
                    </form>
                </div>
                
                <!-- Uploaded Images -->
                <div>
                    <h4>Gambar yang Telah Diupload (<?php echo count($uploaded_images); ?>)</h4>
                    
                    <?php if (empty($uploaded_images)): ?>
                        <div class="empty-state">
                            <div class="icon">📭</div>
                            <p>Belum ada gambar yang diupload</p>
                        </div>
                    <?php else: ?>
                        <div class="image-grid">
                            <?php foreach ($uploaded_images as $image): ?>
                            <div class="image-item">
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     alt="<?php echo basename($image); ?>"
                                     onclick="selectImage('<?php echo htmlspecialchars($image); ?>')">
                                
                                <div class="image-actions">
                                    <button class="action-btn" title="Copy URL" 
                                            onclick="copyImageURL('<?php echo htmlspecialchars($image); ?>')">
                                        📋
                                    </button>
                                    <button class="action-btn" title="Hapus" 
                                            onclick="deleteImage('<?php echo htmlspecialchars($image); ?>')">
                                        🗑️
                                    </button>
                                </div>
                                
                                <div class="image-info">
                                    <div class="image-name"><?php echo basename($image); ?></div>
                                    <div class="image-size">
                                        <?php echo round(filesize($image) / 1024, 1); ?> KB
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        }
        
        function copyImageURL(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('URL gambar telah disalin ke clipboard!');
            }).catch(err => {
                alert('Gagal menyalin URL');
            });
        }
        
        function deleteImage(url) {
            if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                window.location.href = 'admin-uploads.php?delete=' + encodeURIComponent(url);
            }
        }
        
        function selectImage(url) {
            if (window.opener) {
                window.opener.selectImageForField(url);
                window.close();
            }
        }
        
        // Drag and drop
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = document.getElementById('fileInput');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#003399';
            uploadArea.style.backgroundColor = '#e3f2fd';
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#ddd';
            uploadArea.style.backgroundColor = '#f8f9fa';
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#ddd';
            uploadArea.style.backgroundColor = '#f8f9fa';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                previewImage({target: fileInput});
            }
        });
    </script>
</body>
</html>