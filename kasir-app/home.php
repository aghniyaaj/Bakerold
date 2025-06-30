<?php
session_start();
include("config/db.php"); // Pastikan path ke file db.php benar

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_kasir'])) {
    header("Location: login.php");
    exit;
}

// Mendapatkan tanggal hari ini
$today = date('Y-m-d');

// Query untuk mendapatkan total transaksi hari ini
$query_total_transaksi = $koneksi->query("
    SELECT COUNT(id_transaksi) AS total_transaksi_hari_ini
    FROM transaksi
    WHERE DATE(tanggal) = '$today'
");
$data_total_transaksi = $query_total_transaksi->fetch_assoc();
$total_transaksi_hari_ini = $data_total_transaksi['total_transaksi_hari_ini'];

// Query untuk mendapatkan total omset hari ini
$query_total_omset = $koneksi->query("
    SELECT SUM(total) AS total_omset_hari_ini
    FROM transaksi
    WHERE DATE(tanggal) = '$today'
");
$data_total_omset = $query_total_omset->fetch_assoc();
$total_omset_hari_ini = $data_total_omset['total_omset_hari_ini'];

// Format omset untuk tampilan
$formatted_omset = number_format($total_omset_hari_ini, 0, ',', '.');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        #sidebar {
            transition: all 0.3s ease;
        }
        
        #sidebar.collapsed {
            margin-left: -250px;
        }
        
        #main-content {
            transition: margin-left 0.3s ease;
        }
        
        #main-content.expanded {
            margin-left: 0;
        }
        
        .menu-item.active {
            background-color: #3b82f6;
            color: white;
        }
        
        .menu-item.active:hover {
            background-color: #2563eb;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                z-index: 1000;
                height: 100vh;
            }
            
            #sidebar.collapsed {
                margin-left: -250px;
            }
            
            #main-content {
                margin-left: 0 !important;
            }
            
            #overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 900;
            }
            
            #overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div id="overlay"></div>
    
    <!-- Top Navigation -->
    <nav class="bg-gray-800 text-white p-4 fixed top-0 left-0 w-full z-50 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button id="sidebar-toggle" class="text-white focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-bold">
                    <i class="fas fa-cash-register mr-2"></i>
                    Baker OLD
                </h1>
            </div>
            <div>
                <span class="mr-4">Halo, Admin</span>
                <a href="logout.php" onclick="return confirm('Yakin ingin logout?')" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="sidebar" class="bg-gray-800 text-white w-64 fixed top-16 left-0 h-[calc(100vh-4rem)] shadow-lg">
        <div class="p-4 border-b border-gray-700">
            <div class="flex items-center">
                <img src="https://placehold.co/40x40" alt="Gambar profil admin - Foto formal dengan latar belakang biru" 
                     class="rounded-full mr-3">
                <div>
                    <p class="font-medium">Admin</p>
                    <p class="text-xs text-gray-400">Kasir</p>
                </div>
            </div>
        </div>
        
        <nav class="p-2">
            <ul>
                <li>
                    <a href="home.php" class="menu-item active flex items-center p-3 rounded-lg transition hover:bg-gray-700">
                        <i class="fas fa-home w-6 text-center mr-3"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li>
                    <a href="transaksi_baru.php" class="menu-item flex items-center p-3 rounded-lg transition hover:bg-gray-700">
                        <i class="fas fa-shopping-cart w-6 text-center mr-3"></i>
                        <span>Transaksi Baru</span>
                    </a>
                </li>
                <li>
                    <a href="modules/produk.php" class="menu-item flex items-center p-3 rounded-lg transition hover:bg-gray-700">
                        <i class="fas fa-bread-slice w-6 text-center mr-3"></i>
                        <span>Data Produk</span>
                    </a>
                </li>
                <li>
                    <a href="modules/transaksi.php" class="menu-item flex items-center p-3 rounded-lg transition hover:bg-gray-700">
                        <i class="fas fa-history w-6 text-center mr-3"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
            <div class="text-center text-sm text-gray-400">
                Baker Old
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main id="main-content" class="ml-64 mt-16 p-8 transition-all duration-300">
        <div class="container mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                    Dashboard Kasir
                </h2>
                <p class="text-gray-600">Selamat datang kembali, Admin!</p>
                <div class="flex items-center mt-4 text-sm text-gray-500">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <span id="current-date" class="mr-4">Sabtu, 15 Juli 2023</span>
                    <i class="fas fa-clock mr-2"></i>
                    <span id="current-time">10:30:45</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-blue-500 p-4 text-white">
                        <div class="flex items-center">
                            <i class="fas fa-receipt text-xl mr-2"></i>
                            <h3 class="font-bold">Total Transaksi Hari Ini</h3>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-4xl font-bold text-gray-800 mb-1"><?= $total_transaksi_hari_ini ?></p>
                        <p class="text-gray-500">Transaksi</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-green-500 p-4 text-white">
                        <div class="flex items-center">
                            <i class="fas fa-dollar-sign text-xl mr-2"></i>
                            <h3 class="font-bold">Total Omset Hari Ini</h3>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-4xl font-bold text-gray-800 mb-1">Rp <?= $formatted_omset ?></p>
                        <p class="text-gray-500">Rupiah</p>
                    </div>
                </div>
                
            </div>
    </main>

    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const overlay = document.getElementById('overlay');
        
        let sidebarCollapsed = false;
        
        function toggleSidebar() {
            sidebarCollapsed = !sidebarCollapsed;
            
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                overlay.classList.add('show');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                overlay.classList.remove('show');
            }
        }
        
        sidebarToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
        
        // Set active menu item
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                // On mobile, close sidebar after clicking menu item
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
            });
        });
        
        // Update current date and time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
            
            // Update time every second
            setInterval(() => {
                const now = new Date();
                document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID');
            }, 1000);
        }
        
        updateDateTime();
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                overlay.classList.remove('show');
                sidebarCollapsed = false;
            }
        });
    </script>
</body>
</html>
