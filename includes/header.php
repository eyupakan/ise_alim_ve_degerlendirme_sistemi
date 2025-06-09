<!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'İş İlanları'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #2D3748;
            --background-color: #f9fafb;
            --text-color: #4A5568;
            --border-color: #E2E8F0;
            --card-bg: #FFFFFF;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --header-bg: #FFFFFF;
            --header-border: #E5E7EB;
        }

        [data-bs-theme="dark"] {
            --primary-color: #3B82F6;
            --secondary-color: #E2E8F0;
            --background-color: #1A202C;
            --text-color: #E2E8F0;
            --border-color: #4A5568;
            --card-bg: #2D3748;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --header-bg: #1F2937;
            --header-border: #374151;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color) !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar {
            background-color: var(--header-bg) !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-bottom: 1px solid var(--header-border);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color) !important;
            font-size: 1.25rem;
            transition: color 0.2s ease;
        }

        .navbar-brand:hover {
            color: #1d4ed8 !important;
        }

        .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            position: relative;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            transition: color 0.2s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: var(--primary-color) !important;
        }

        .nav-link.active::after {
            width: 100%;
        }

        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background-color: var(--border-color);
            transform: rotate(30deg);
        }

        .position-card {
            height: 100%;
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            background-color: var(--card-bg);
        }

        .position-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary-color) !important;
            margin-bottom: 1rem;
        }

        .card-text {
            color: var(--text-color) !important;
            line-height: 1.6;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
            color: white !important;
        }

        .btn-primary:hover {
            background-color: #3182CE;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--border-color);
            color: var(--card-bg);
        }

        .main-content {
            min-height: calc(100vh - 160px);
            padding: 2rem 0;
        }

        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .alert-info {
            background-color: #EBF8FF;
            color: #2B6CB0;
        }

        .alert-danger {
            background-color: #FED7D7;
            color: #C53030;
        }

        [data-bs-theme="dark"] .alert-info {
            background-color: #2C5282;
            color: #EBF8FF;
        }

        [data-bs-theme="dark"] .alert-danger {
            background-color: #742A2A;
            color: #FED7D7;
        }

        [data-bs-theme="dark"] .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #A0AEC0 !important;
        }

        [data-bs-theme="dark"] .text-secondary {
             color: var(--secondary-color) !important;
        }

         [data-bs-theme="dark"] .text-dark {
             color: var(--text-color) !important;
        }

         [data-bs-theme="dark"] h1, [data-bs-theme="dark"] h2, [data-bs-theme="dark"] h3, [data-bs-theme="dark"] h4, [data-bs-theme="dark"] h5, [data-bs-theme="dark"] h6 {
             color: var(--secondary-color) !important;
        }

        /* Genel yazı elementleri için dark mode */
        [data-bs-theme="dark"] p,
        [data-bs-theme="dark"] span,
        [data-bs-theme="dark"] div,
        [data-bs-theme="dark"] label,
        [data-bs-theme="dark"] small,
        [data-bs-theme="dark"] strong,
        [data-bs-theme="dark"] li {
             color: var(--text-color) !important;
        }

        .form-control {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .form-control:focus {
            background-color: var(--card-bg);
            border-color: var(--primary-color);
            color: var(--text-color);
        }

        .form-label {
            color: var(--text-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-briefcase me-2"></i>
                Kariyer Portalı
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">İş İlanları</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">İletişim</a>
                    </li>
                </ul>
                <button class="theme-toggle" id="themeToggle" title="Tema Değiştir">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container"><?php if (isset($page_heading)): ?>
            <h1 class="mb-4"><?php echo $page_heading; ?></h1>
        <?php endif; ?>

    <script>
        // Tema değiştirme fonksiyonu
        document.getElementById('themeToggle').addEventListener('click', function() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-bs-theme', newTheme);
            
            // İkon değiştirme
            const icon = this.querySelector('i');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            
            // Tercihi localStorage'a kaydet
            localStorage.setItem('theme', newTheme);
        });

        // Sayfa yüklendiğinde tema tercihini kontrol et
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            
            const icon = document.querySelector('.theme-toggle i');
            icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script> 