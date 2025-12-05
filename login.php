<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Generate Captcha Code jika belum ada
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = rand(1000, 9999);
}

// Kredensial admin
$valid_username = "admin";
$valid_password = "admin1234";

$error = "";
$success = "";

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $input_captcha = $_POST['captcha'] ?? '';
    
    // Ambil captcha dari session saat ini untuk validasi
    $valid_captcha = $_SESSION['captcha_code'] ?? '';
    
    // Regenerate captcha baru untuk attempt selanjutnya (keamanan anti-replay)
    // Kita simpan di variabel temp dulu jika login sukses, agar tidak hilang saat render
    $new_captcha = rand(1000, 9999);
    $_SESSION['captcha_code'] = $new_captcha;
    
    // 1. Validasi Captcha Terlebih Dahulu
    if ($input_captcha != $valid_captcha) {
        $error = "Kode keamanan (CAPTCHA) salah! Silakan coba lagi.";
    } 
    // 2. Jika Captcha Benar, Validasi Kredensial
    else if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        
        // PERBAIKAN: Baris unset dihapus agar tidak memicu error "Undefined array key" saat render HTML di bawah
        // unset($_SESSION['captcha_code']); 
        
        // Set session parameters untuk keamanan
        session_regenerate_id(true);
        
        $success = "Login berhasil! Redirecting...";
        
        // Redirect menggunakan JavaScript untuk menghindari header issues
        echo "<script>setTimeout(function() { window.location.href = 'admin-dashboard.php'; }, 1000);</script>";
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Login Admin - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <style>
        /* Additional Styles for Login Page */
        .login-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 0;
            background-image: url("https://upload.wikimedia.org/wikipedia/commons/c/c1/Pemandangan_Tanah_Grogot.jpg?20100504072853");
            background-color: rgba(255, 255, 255, 0.5);
            background-blend-mode: overlay;
            background-size: cover;
            background-position: center;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.8);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-logo {
            margin-bottom: 30px;
        }

        .login-logo img {
            height: 80px;
            margin-bottom: 15px;
        }

        .login-logo h2 {
            color: #003399;
            margin-bottom: 5px;
            font-size: 1.5rem;
        }

        .login-logo p {
            color: #666;
            font-size: 0.9rem;
        }

        .login-form {
            text-align: left;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #003399;
            box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
        }

        /* Styles untuk Captcha */
        .captcha-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .captcha-code {
            flex: 0 0 100px;
            background: #e9ecef;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 5px;
            color: #003399;
            border-radius: 6px;
            border: 1px solid #ced4da;
            text-decoration: line-through; /* Efek coret agar susah di OCR */
            user-select: none; /* Mencegah user melakukan select text */
            opacity: 0.8;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #003399;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .login-btn:hover {
            background: #002280;
            transform: translateY(-2px);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background: #e8f5e8;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #003399;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .login-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.8rem;
            color: #666;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .login-container {
                padding: 40px 20px;
            }

            .login-card {
                padding: 30px 25px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px 20px;
            }

            .login-logo img {
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <img src="assets/logo-kabupaten.png" alt="Logo Pemerintahan">
                    <div class="logo-text">
                        <h1>Dinas Pendidikan dan Kebudayaan</h1>
                        <p>Kabupaten Paser</p>
                    </div>
                </div>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
                <nav id="main-nav">
                    <ul>
                        <li><a href="index.php" onclick="closeMobileMenu()">Beranda</a></li>
                        <li><a href="profil.php" onclick="closeMobileMenu()">Profil</a></li>
                        <li><a href="layanan.php" onclick="closeMobileMenu(); scrollToLayanan();">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="kontak.php" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="login-container">
        <div class="login-card fade-in">
            <div class="login-logo">
                <img src="assets/logo-kabupaten.png" alt="Logo Pemerintahan">
                <h2>Admin Login</h2>
                <p>Dinas Pendidikan dan Kebudayaan Paser</p>
            </div>

            <?php
            // Tampilkan pesan error
            if (!empty($error)) {
                echo "<div class='alert alert-error'>$error</div>";
            }
            
            // Tampilkan pesan success
            if (!empty($success) && empty($error)) {
                echo "<div class='alert alert-success'>$success</div>";
            }
            ?>

            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <div class="form-group">
                    <label for="captcha">Kode Keamanan</label>
                    <div class="captcha-wrapper">
                        <div class="captcha-code">
                            <?php echo $_SESSION['captcha_code'] ?? '....'; ?>
                        </div>
                        <input type="text" id="captcha" name="captcha" class="form-control" placeholder="Salin kode di samping" required autocomplete="off">
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>

            <a href="index.html" class="back-link">← Kembali ke Beranda</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content" id="kontak">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid" width="250" height="200" style="border:0; border-radius: 0.3rem;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <ul>
                        <li>Jl. Jenderal Sudirman No. 27, Tanah Grogot, Kabupaten Paser, Kalimantan Timur 76251</li>
                        <li>Telp: (0543) 21023</li>
                        <li>Email: disdik@paserkab.go.id</li>
                        <li class="social-icons">
                            <a href="#" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Layanan Cepat</h3>
                    <ul>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Legalisir Ijazah/Dokumen Kelulusan</a></li>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Surat Keterangan Pindah Sekolah</a></li>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Tunjangan Profesi Guru</a></li>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Izin Pendirian Satuan Pendidikan</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Tautan Terkait</h3>
                    <ul>
                        <li><a href="#">Kementerian Dalam Negeri</a></li>
                        <li><a href="#">Badan Pusat Statistik</a></li>
                        <li><a href="#">Kementerian Keuangan</a></li>
                        <li><a href="#">Portal Nasional</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fade in effect untuk login card
        document.addEventListener('DOMContentLoaded', function() {
            const loginCard = document.querySelector('.login-card');
            setTimeout(() => {
                loginCard.classList.add('visible');
            }, 100);
        });

        // Validasi form sebelum submit
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const captcha = document.getElementById('captcha').value;
            
            if (!username || !password || !captcha) {
                e.preventDefault();
                alert('Harap isi semua field termasuk kode keamanan!');
            }
        });

        // Existing JavaScript functions
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.toggle('active');
        }

        function closeMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.remove('active');
            
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    </script>
</body>
</html>