<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Admin BluePro</title>
    <!-- Bootstrap 5 CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue-primary: #1e40af;
            /* Biru Gelap Utama */
            --blue-secondary: #3b82f6;
            /* Biru Terang */
            --blue-light: #eff6ff;
            /* Latar belakang biru sangat muda */
            --blue-accent: #60a5fa;
            /* Aksentuasi */
            --dark-text: #1e293b;
            /* Warna teks gelap */
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--dark-text);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: var(--blue-primary);
            color: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        }

        #sidebar .brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.8rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            border-radius: 8px;
            margin: 0.2rem 0.75rem;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.15);
        }

        #sidebar .nav-link.active {
            background-color: var(--blue-secondary);
            font-weight: 600;
        }

        /* Main Content Styling */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
            padding-bottom: 3rem;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        /* Card Customization */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--blue-primary);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Customization */
        .form-label {
            font-weight: 500;
            color: #475569;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--blue-accent);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        .btn-primary {
            background-color: var(--blue-primary);
            border-color: var(--blue-primary);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        /* Status Badge */
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            #sidebar {
                left: -260px;
            }

            #sidebar.active {
                left: 0;
            }

            #main-content {
                margin-left: 0;
            }

            #main-content.active {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.4);
                z-index: 98;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- Overlay untuk Mobile Sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <div id="sidebar">
        <div class="brand">
            <i class="bi bi-shield-fill-check fs-4 text-info"></i>
            <span>Apresiasi {{ date('Y') }}</span>
        </div>
        <div class="mt-4">
            <a href="{{ route('apresiasi-dashboard', ['halaman' => 'main']) }}" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
            <a href="#" class="nav-link @if (isset($main_menu) && $main_menu == 'dashboard') active @endif">
                <i class="bi bi-journal-text"></i>
                Form Entri Data
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-people"></i>
                Kelola Pengguna
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                Laporan Analitik
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i>
                Pengaturan Sistem
            </a>
            <hr class="mx-3 my-4 text-white-50">
            <a href="#" class="nav-link text-danger-hover" onclick="showToast('Logout Berhasil')">
                <i class="bi bi-box-arrow-left text-danger"></i>
                <span class="text-danger">Logout</span>
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div id="main-content">
        <!-- TOP NAVBAR -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="d-none d-lg-block">
                <h5 class="mb-0 fw-semibold text-muted">Selamat Datang, {{ Auth::user()->nama }}</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                {{-- <!-- Notifikasi -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.6rem;">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="width: 280px;">
                        <li>
                            <h6 class="dropdown-header">Notifikasi Terbaru</h6>
                        </li>
                        <li><a class="dropdown-item py-2" href="#"><i
                                    class="bi bi-info-circle text-primary me-2"></i> Pendaftaran pengguna baru</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i
                                    class="bi bi-exclamation-triangle text-warning me-2"></i> Server mencapai batas
                                85%</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center text-primary py-1" style="font-size: 0.85rem;"
                                href="#">Lihat Semua</a></li>
                    </ul>
                </div> --}}
                <!-- Profil -->
                <div class="d-flex align-items-center gap-2">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 fw-semibold" style="font-size: 0.9rem;">{{ Auth::user()->getNamaProfil() }}
                        </p>
                        <small class="text-muted"
                            style="font-size: 0.75rem;">{{ Auth::user()->jabatanTingkatBunda() }}</small>
                    </div>
                    <img src="{{ Auth::user()->getFotoProfil() }}" alt="Avatar Admin"
                        class="rounded-circle border border-2 border-primary"
                        style="width: 40px; height: 40px; object-fit: cover;">
                </div>
            </div>
        </nav>

        <!-- CONTAINER CONTENT -->
        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>

    <!-- TOAST NOTIFIKASI kustom -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="actionToast" class="toast align-items-center text-white bg-dark border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success" id="toastIcon"></i>
                    <span id="toastMessage">Pemberitahuan sistem</span>
                </div>
                <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (termasuk Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Set Default Date hari ini di Input Tanggal
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('inputTanggal').value = today;
        });

        // Kontrol Responsif Sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
            sidebarOverlay.classList.toggle('show');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Menutup sidebar jika mengklik menu di layar mobile
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    toggleSidebar();
                }
                // Ubah kelas aktif navigasi
                navLinks.forEach(item => item.classList.remove('active'));
                link.classList.add('active');
            });
        });

        // Manajemen Toast Notifikasi
        function showToast(message, isSuccess = true) {
            const toastEl = document.getElementById('actionToast');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            toastMessage.textContent = message;

            if (isSuccess) {
                toastIcon.className = "bi bi-check-circle-fill text-success";
            } else {
                toastIcon.className = "bi bi-exclamation-triangle-fill text-danger";
            }

            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Handle Submit Form
        function handleFormSubmit(event) {
            event.preventDefault();

            // Ambil data form
            const nama = document.getElementById('inputNama').value;
            const email = document.getElementById('inputEmail').value;
            const kategori = document.getElementById('selectKategori').value;
            const statusAktif = document.getElementById('switchStatus').checked;

            // Tambahkan baris baru ke tabel secara dinamis
            const tableBody = document.querySelector('#dataTabel tbody');
            const newRow = document.createElement('tr');

            // Atur badge kategori
            let badgeClass = "bg-secondary";
            if (kategori === "Gold") badgeClass = "bg-warning text-dark";
            else if (kategori === "Platinum") badgeClass = "bg-info text-dark";
            else if (kategori === "Bronze") badgeClass = "bg-dark text-white";

            const statusText = statusAktif ? 'Aktif' : 'Nonaktif';
            const statusClass = statusAktif ? 'badge-active' : 'bg-danger bg-opacity-10 text-danger';

            newRow.innerHTML = `
                <td class="ps-3">
                    <div class="fw-semibold">${nama}</div>
                    <small class="text-muted">${email}</small>
                </td>
                <td><span class="badge ${badgeClass}">${kategori}</span></td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td class="text-end pe-3">
                    <button class="btn btn-sm btn-icon btn-light border" onclick="showToast('Edit ${nama}')"><i class="bi bi-pencil"></i></button>
                </td>
            `;

            // Animasi baris baru ditambahkan ke awal tabel
            tableBody.insertBefore(newRow, tableBody.firstChild);

            // Munculkan notifikasi sukses
            showToast(`Berhasil menyimpan data ${nama}!`);

            // Reset form
            document.getElementById('adminForm').reset();
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('inputTanggal').value = today;
        }
    </script>
</body>

</html>
