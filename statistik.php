<?php
session_start();

// 1. LOAD DATA DARI JSON
$json_file = 'data/statistik.json';
$stats = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];

// Data Default jika file belum ada/kosong
if (empty($stats)) {
    $stats = [ "tabel_rincian" => [] ];
}

// 2. AMBIL DATA RINCIAN (UNTUK DIHITUNG)
$rin = $stats['tabel_rincian'] ?? [];

// Helper function untuk ambil data aman (mencegah error jika key tidak ada)
function val($arr, $k1, $k2) { 
    // Hapus titik jika ada (misal "1.200" jadi "1200") lalu ubah ke integer
    $val = isset($arr[$k1][$k2]) ? $arr[$k1][$k2] : 0;
    return intval(str_replace('.', '', $val));
}

// 3. HITUNG TOTAL OTOMATIS DARI DATA RINCIAN
// Total Sekolah = (Semua Jenjang Negeri) + (Semua Jenjang Swasta)
$total_sekolah_real = 
    (val($rin, 'sekolah_negeri', 'paud') + val($rin, 'sekolah_negeri', 'sd') + val($rin, 'sekolah_negeri', 'smp')) +
    (val($rin, 'sekolah_swasta', 'paud') + val($rin, 'sekolah_swasta', 'sd') + val($rin, 'sekolah_swasta', 'smp'));

// Total Siswa = Penjumlahan semua jenjang
$total_siswa_real = 
    val($rin, 'siswa', 'paud') + val($rin, 'siswa', 'sd') + val($rin, 'siswa', 'smp');

// Total Guru = (Semua Guru ASN) + (Semua Guru Non-ASN)
$total_guru_real = 
    (val($rin, 'guru_asn', 'paud') + val($rin, 'guru_asn', 'sd') + val($rin, 'guru_asn', 'smp')) +
    (val($rin, 'guru_non_asn', 'paud') + val($rin, 'guru_non_asn', 'sd') + val($rin, 'guru_non_asn', 'smp'));


// 4. HITUNG DATA UNTUK GRAFIK (Agar sinkron dengan tabel)
// Grafik Sekolah (Total Negeri + Swasta per Jenjang)
$data_sekolah = [
    ['label' => 'PAUD', 'value' => val($rin, 'sekolah_negeri', 'paud') + val($rin, 'sekolah_swasta', 'paud')],
    ['label' => 'SD',   'value' => val($rin, 'sekolah_negeri', 'sd') + val($rin, 'sekolah_swasta', 'sd')],
    ['label' => 'SMP',  'value' => val($rin, 'sekolah_negeri', 'smp') + val($rin, 'sekolah_swasta', 'smp')]
];

// Grafik Guru (Total ASN + Non-ASN)
$total_asn = val($rin, 'guru_asn', 'paud') + val($rin, 'guru_asn', 'sd') + val($rin, 'guru_asn', 'smp');
$total_non = val($rin, 'guru_non_asn', 'paud') + val($rin, 'guru_non_asn', 'sd') + val($rin, 'guru_non_asn', 'smp');

$data_guru = [
    ['label' => 'ASN',     'value' => $total_asn],
    ['label' => 'Non-ASN', 'value' => $total_non]
];

// Kirim ke JS
$json_sekolah = json_encode($data_sekolah);
$json_guru    = json_encode($data_guru);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data & Statistik - Dinas Pendidikan dan Kebudayaan</title>
    
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* === STYLE KHUSUS HALAMAN STATISTIK === */
        
        body {
            background-color: #f8f9fc;
            color: #333;
        }

        /* Hero Section */
        .stats-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            text-align: center;
            padding: 100px 0 160px; /* Sedikit diperbesar untuk badge update */
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
        }

        /* Badge Pembaruan Data */
        .update-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeInDown 1s ease-out;
        }

        .update-badge i {
            margin-right: 8px;
            color: #93c5fd; /* Biru muda */
        }

        /* Summary Cards Grid - DIUBAH AGAR CARDS LEBIH LEBAR KARENA HANYA 3 */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Memaksa 3 kolom */
            gap: 30px;
            margin-top: -80px;
            position: relative;
            z-index: 10;
            margin-bottom: 60px;
        }

        .stat-card {
            background: white;
            padding: 35px 25px; /* Padding ditambah sedikit */
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: #e2e8f0;
            transition: background 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .stat-card:hover::before {
            background: #1e3a8a;
        }

        .stat-icon {
            font-size: 3rem; /* Ikon diperbesar sedikit */
            color: #1e3a8a;
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 2.5rem; /* Angka diperbesar */
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
            display: block;
            letter-spacing: -1px;
        }

        .stat-label {
            font-size: 1rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Container Grafik */
        .chart-wrapper {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 40px;
            height: 100%;
            border: 1px solid #f1f5f9;
            position: relative;
        }

        .chart-title {
            text-align: center;
            margin-bottom: 40px;
            color: #1e3a8a;
            font-size: 1.4rem;
            font-weight: 700;
            position: relative;
        }

        /* SVG Chart Styles */
        .svg-chart-container {
            width: 100%;
            height: 300px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .chart-bar {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .chart-bar:hover {
            filter: brightness(1.1);
        }

        .chart-slice {
            transition: transform 0.3s ease, filter 0.3s ease;
            cursor: pointer;
            stroke: white;
            stroke-width: 2px;
        }
        .chart-slice:hover {
            transform: scale(1.05);
            transform-origin: center;
            filter: brightness(1.1);
            z-index: 10;
        }

        /* Custom Tooltip */
        #tooltip {
            position: absolute;
            background: rgba(15, 23, 42, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 100;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translate(-50%, -120%);
        }

        /* Tabel Detail */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            font-size: 0.95rem;
        }

        .custom-table th {
            background-color: #f1f5f9;
            color: #334155;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
        }

        .custom-table tr:hover td {
            background-color: #f8fafc;
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        .table-footer td {
            background-color: #eff6ff;
            font-weight: bold;
            color: #1e3a8a;
            border-top: 2px solid #bfdbfe;
        }

        /* Utility */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive untuk Grid 3 Kolom */
        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .svg-chart-container { height: 250px; }
        }
    </style>
</head>
<body>

    <div id="tooltip"></div>

    <header id="main-header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <img src="assets/logo-kabupaten.png" alt="Logo">
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
                        <li><a href="Statistik.php" onclick="closeMobileMenu()" class="active">Data & Statistik</a></li>
                        <li><a href="layanan.php" onclick="closeMobileMenu()">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="kontak.php" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="stats-hero fade-in">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Data Statistik Pendidikan</h1>
            <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 0;">Transparansi Data Pendidikan Kabupaten Paser</p>
            
            <div class="update-badge">
                <i class="fas fa-clock"></i>
                <span>Pembaruan Terakhir: <strong>30 Mei 2025</strong></span>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="stats-grid fade-in">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-school"></i></div>
                <span class="stat-number count-up" data-target="<?php echo $total_sekolah_real; ?>">0</span>
                <span class="stat-label">Total Sekolah</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-graduate" style="color:#e36159"></i></div>
                <span class="stat-number count-up" data-target="<?php echo $total_siswa_real; ?>">0</span>
                <span class="stat-label">Total Siswa</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher" style="color:#28a745"></i></div>
                <span class="stat-number count-up" data-target="<?php echo $total_guru_real; ?>">0</span>
                <span class="stat-label">Total Guru</span>
            </div>
        </div>
    </div>

    <section style="padding-bottom: 80px;">
        <div class="container">
            
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 30px;">
                
                <div class="col" style="flex: 1; min-width: 300px;">
                    <div class="chart-wrapper fade-in">
                        <h3 class="chart-title">Jumlah Sekolah Per Jenjang</h3>
                        <div id="bar-chart-area" class="svg-chart-container"></div>
                        <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #64748b;">
                            Sebaran satuan pendidikan berdasarkan jenjang.
                        </p>
                    </div>
                </div>

                <div class="col" style="flex: 1; min-width: 300px;">
                    <div class="chart-wrapper fade-in">
                        <h3 class="chart-title">Status Kepegawaian Guru</h3>
                        <div id="pie-chart-area" class="svg-chart-container"></div>
                        
                        <div id="pie-legend" style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; flex-wrap: wrap;">
                            </div>
                    </div>
                </div>

            </div>

            <div class="chart-wrapper fade-in" style="margin-top: 30px;">
                <h3 class="chart-title" style="margin-bottom: 20px;">Tabel Rincian Data Pendidikan</h3>
                <div style="overflow-x: auto;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Indikator</th>
                                <th>PAUD</th>
                                <th>SD</th>
                                <th>SMP</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Sekolah (Negeri)</strong></td>
                                <td><?php echo number_format(val($rin, 'sekolah_negeri', 'paud'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'sekolah_negeri', 'sd'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'sekolah_negeri', 'smp'), 0, ',', '.'); ?></td>
                                <td><b><?php echo number_format(val($rin, 'sekolah_negeri', 'paud') + val($rin, 'sekolah_negeri', 'sd') + val($rin, 'sekolah_negeri', 'smp'), 0, ',', '.'); ?></b></td>
                            </tr>
                            
                            <tr>
                                <td><strong>Sekolah (Swasta)</strong></td>
                                <td><?php echo number_format(val($rin, 'sekolah_swasta', 'paud'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'sekolah_swasta', 'sd'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'sekolah_swasta', 'smp'), 0, ',', '.'); ?></td>
                                <td><b><?php echo number_format(val($rin, 'sekolah_swasta', 'paud') + val($rin, 'sekolah_swasta', 'sd') + val($rin, 'sekolah_swasta', 'smp'), 0, ',', '.'); ?></b></td>
                            </tr>

                            <tr style="background-color: #f0f9ff;">
                                <td><strong>Siswa</strong></td>
                                <td><?php echo number_format(val($rin, 'siswa', 'paud'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'siswa', 'sd'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'siswa', 'smp'), 0, ',', '.'); ?></td>
                                <td><b><?php echo number_format($total_siswa_real, 0, ',', '.'); ?></b></td>
                            </tr>

                            <tr>
                                <td><strong>Guru (ASN)</strong></td>
                                <td><?php echo number_format(val($rin, 'guru_asn', 'paud'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'guru_asn', 'sd'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'guru_asn', 'smp'), 0, ',', '.'); ?></td>
                                <td><b><?php echo number_format(val($rin, 'guru_asn', 'paud') + val($rin, 'guru_asn', 'sd') + val($rin, 'guru_asn', 'smp'), 0, ',', '.'); ?></b></td>
                            </tr>

                            <tr>
                                <td><strong>Guru (Non-ASN)</strong></td>
                                <td><?php echo number_format(val($rin, 'guru_non_asn', 'paud'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'guru_non_asn', 'sd'), 0, ',', '.'); ?></td>
                                <td><?php echo number_format(val($rin, 'guru_non_asn', 'smp'), 0, ',', '.'); ?></td>
                                <td><b><?php echo number_format(val($rin, 'guru_non_asn', 'paud') + val($rin, 'guru_non_asn', 'sd') + val($rin, 'guru_non_asn', 'smp'), 0, ',', '.'); ?></b></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-footer">
                                <td>TOTAL KESELURUHAN</td>
                                <td colspan="4" style="text-align: right; color: #666; font-weight: normal; font-size: 0.9em;">
                                    <i>Data dihitung otomatis dari sistem</i>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <ul>
                        <li>Jl. Jenderal Sudirman No. 27, Tanah Grogot</li>
                        <li>Telp: (0543) 21023</li>
                        <li>Email: disdik@paserkab.go.id</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Tautan Terkait</h3>
                    <ul>
                        <li><a href="#">Kemendikbud Ristek</a></li>
                        <li><a href="#">Dapodikdasmen</a></li>
                        <li><a href="#">Portal Kabupaten Paser</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // 1. Mobile Menu
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.toggle('active');
        }

        // 2. Data dari PHP ke JS
        const dataSekolah = <?php echo $json_sekolah; ?>;
        const dataGuru = <?php echo $json_guru; ?>;

        // 3. Warna Palet Monokromatik
        const colorsBlue = ['#1e3a8a', '#2563eb', '#60a5fa', '#93c5fd'];
        const colorsPie = ['#1e3a8a', '#0f766e', '#d97706'];

        // 4. Bar Chart SVG
        function createBarChart(containerId, data) {
            const container = document.getElementById(containerId);
            const width = container.clientWidth;
            const height = container.clientHeight;
            const padding = 40;
            
            const maxValue = Math.max(...data.map(d => d.value));
            const barWidth = (width - (padding * 2)) / data.length;
            const maxBarHeight = height - (padding * 2);

            let svgHTML = `<svg viewBox="0 0 ${width} ${height}">`;

            // Axis line
            svgHTML += `<line x1="${padding}" y1="${height - padding}" x2="${width - padding}" y2="${height - padding}" stroke="#cbd5e1" stroke-width="2"/>`;

            data.forEach((item, index) => {
                const barHeight = (item.value / maxValue) * maxBarHeight;
                const x = padding + (index * barWidth) + (barWidth * 0.2);
                const y = height - padding - barHeight;
                const w = barWidth * 0.6;
                const color = colorsBlue[index % colorsBlue.length];

                svgHTML += `
                    <rect x="${x}" y="${y}" width="${w}" height="${barHeight}" fill="${color}" rx="4" class="chart-bar"
                        onmousemove="showTooltip(evt, '${item.label}: ${item.value.toLocaleString('id-ID')}')"
                        onmouseout="hideTooltip()"
                    >
                        <animate attributeName="height" from="0" to="${barHeight}" dur="1s" fill="freeze" />
                        <animate attributeName="y" from="${height - padding}" to="${y}" dur="1s" fill="freeze" />
                    </rect>
                `;

                svgHTML += `
                    <text x="${x + w/2}" y="${height - 15}" text-anchor="middle" fill="#64748b" font-size="12" font-family="Segoe UI, sans-serif" font-weight="600">
                        ${item.label}
                    </text>
                `;
            });

            svgHTML += `</svg>`;
            container.innerHTML = svgHTML;
        }

        // 5. Pie Chart SVG
        function createPieChart(containerId, data) {
            const container = document.getElementById(containerId);
            const legendContainer = document.getElementById('pie-legend');
            const width = 300;
            const height = 300;
            const radius = 140;
            const centerX = width / 2;
            const centerY = height / 2;
            
            const total = data.reduce((acc, curr) => acc + curr.value, 0);
            let startAngle = 0;

            let svgHTML = `<svg viewBox="0 0 ${width} ${height}">`;

            data.forEach((item, index) => {
                const sliceAngle = (item.value / total) * 2 * Math.PI;
                const endAngle = startAngle + sliceAngle;
                const color = colorsPie[index % colorsPie.length];

                const x1 = centerX + radius * Math.cos(startAngle);
                const y1 = centerY + radius * Math.sin(startAngle);
                const x2 = centerX + radius * Math.cos(endAngle);
                const y2 = centerY + radius * Math.sin(endAngle);

                const largeArc = sliceAngle > Math.PI ? 1 : 0;
                const pathData = `M ${centerX} ${centerY} L ${x1} ${y1} A ${radius} ${radius} 0 ${largeArc} 1 ${x2} ${y2} Z`;

                svgHTML += `
                    <path d="${pathData}" fill="${color}" class="chart-slice"
                        onmousemove="showTooltip(evt, '${item.label}: ${item.value.toLocaleString('id-ID')} (${Math.round((item.value/total)*100)}%)')"
                        onmouseout="hideTooltip()"
                    />
                `;
                
                // Legend
                const legendHTML = `
                    <div style="display:flex; align-items:center; font-size:0.9rem; color:#475569;">
                        <div style="width:12px; height:12px; background:${color}; border-radius:50%; margin-right:8px;"></div>
                        <strong>${item.label}</strong>
                    </div>
                `;
                legendContainer.innerHTML += legendHTML;

                startAngle = endAngle;
            });

            // Donut hole
            svgHTML += `<circle cx="${centerX}" cy="${centerY}" r="80" fill="white" />`;
            
            // Text center
            svgHTML += `
                <text x="${centerX}" y="${centerY}" text-anchor="middle" dominant-baseline="middle" fill="#1e3a8a" font-weight="bold" font-size="24">
                    ${(total/1000).toFixed(1)}k
                </text>
                <text x="${centerX}" y="${centerY + 20}" text-anchor="middle" dominant-baseline="middle" fill="#64748b" font-size="12">
                    Total Guru
                </text>
            `;

            svgHTML += `</svg>`;
            container.innerHTML = svgHTML;
        }

        // 6. Tooltip Logic
        const tooltip = document.getElementById('tooltip');

        function showTooltip(evt, text) {
            tooltip.style.display = 'block';
            tooltip.innerHTML = text;
            tooltip.style.left = evt.pageX + 'px';
            tooltip.style.top = evt.pageY - 40 + 'px';
            setTimeout(() => { tooltip.style.opacity = 1; }, 10);
        }

        function hideTooltip() {
            tooltip.style.opacity = 0;
            setTimeout(() => { tooltip.style.display = 'none'; }, 200);
        }

        // 7. Counter Animation
        function animateCounters() {
            const counters = document.querySelectorAll('.count-up');
            const speed = 200;

            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText.replace(/\./g, '');
                    const inc = Math.ceil(target / speed);

                    if (count < target) {
                        counter.innerText = (count + inc).toLocaleString('id-ID');
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target.toLocaleString('id-ID');
                    }
                };
                updateCount();
            });
        }

        // Init
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

            animateCounters();
            createBarChart('bar-chart-area', dataSekolah);
            createPieChart('pie-chart-area', dataGuru);
        });
    </script>
</body>
</html>