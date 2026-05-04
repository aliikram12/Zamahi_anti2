<?php
/**
 * ZAMAHI Admin - Header / Layout Top
 */
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --gold: #D4AF37; --gold-light: #E8D48B; --gold-dark: #B8960F; --black: #000; --white: #FFF;
            --charcoal: #1C1C1C; --dark-grey: #232323; --mid-grey: #888; --light-grey: #F5F5F5;
            --success: #2ecc71; --danger: #e74c3c; --info: #3498db; --warning: #f39c12;
            --bg-body: #0d0d0d; --bg-card: #1a1a1a; --border-subtle: rgba(255,255,255,0.06);
            --radius: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-pill: 100px;
            --transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-body); color: var(--white); min-height: 100vh; display: flex; }

        /* ═══ Sidebar ═══ */
        .admin-sidebar {
            width: 260px; background: var(--charcoal); border-right: 1px solid rgba(212,175,55,0.08);
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; display: flex; flex-direction: column;
            transition: width var(--transition);
        }
        .sidebar-logo {
            padding: 28px 20px; border-bottom: 1px solid rgba(212,175,55,0.1); text-align: center;
        }
        .sidebar-logo .logo-text {
            font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700;
            color: var(--gold); letter-spacing: 5px; display: block;
        }
        .sidebar-logo .logo-sub {
            font-size: 0.52rem; letter-spacing: 3px; color: var(--gold-light);
            text-transform: uppercase; opacity: 0.6; margin-top: 2px;
        }
        .sidebar-nav { flex: 1; padding: 20px 0; overflow-y: auto; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 14px; padding: 13px 24px;
            color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.88rem; font-weight: 400;
            transition: all var(--transition); border-left: 3px solid transparent;
            position: relative;
        }
        .sidebar-nav a:hover {
            color: var(--gold-light); background: rgba(212,175,55,0.04); border-left-color: rgba(212,175,55,0.3);
        }
        .sidebar-nav a.active {
            color: var(--gold); background: rgba(212,175,55,0.06); border-left-color: var(--gold);
            font-weight: 500;
        }
        .sidebar-nav a i { width: 20px; text-align: center; font-size: 0.95rem; transition: color var(--transition); }
        .sidebar-nav a:hover i, .sidebar-nav a.active i { color: var(--gold); }
        .sidebar-nav .nav-divider { height: 1px; background: rgba(255,255,255,0.04); margin: 14px 24px; }

        /* ═══ Main Content ═══ */
        .admin-main { margin-left: 260px; flex: 1; min-height: 100vh; }
        .admin-topbar {
            background: rgba(28,28,28,0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            padding: 16px 32px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(212,175,55,0.08); position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 1.15rem; font-weight: 600; letter-spacing: 0.5px; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-right a { color: var(--mid-grey); font-size: 0.85rem; text-decoration: none; transition: color 0.3s; }
        .topbar-right a:hover { color: var(--gold); }
        .admin-content { padding: 32px; }

        /* ═══ Stat Cards ═══ */
        .stat-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);
            padding: 24px; transition: all var(--transition); position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity: 0; transition: opacity var(--transition);
        }
        .stat-card:hover {
            border-color: rgba(212,175,55,0.2); transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .stat-card:hover::before { opacity: 1; }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; margin-bottom: 16px;
        }
        .stat-card .stat-value { font-size: 1.7rem; font-weight: 700; color: var(--white); letter-spacing: -0.5px; }
        .stat-card .stat-label { font-size: 0.75rem; color: var(--mid-grey); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 6px; font-weight: 500; }

        /* ═══ Tables ═══ */
        .admin-table-wrap {
            background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);
            overflow: hidden; margin-bottom: 24px;
        }
        .admin-table-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 24px; border-bottom: 1px solid var(--border-subtle);
        }
        .admin-table-header h3 { font-size: 0.95rem; font-weight: 600; }
        table.admin-table { width: 100%; border-collapse: collapse; }
        table.admin-table th {
            text-align: left; padding: 14px 20px; font-size: 0.72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1.5px; color: var(--gold);
            background: rgba(0,0,0,0.25); border-bottom: 1px solid var(--border-subtle);
        }
        table.admin-table td {
            padding: 14px 20px; font-size: 0.88rem; color: rgba(255,255,255,0.75);
            border-bottom: 1px solid var(--border-subtle); transition: background var(--transition);
        }
        table.admin-table tr:hover td { background: rgba(212,175,55,0.02); }

        /* ═══ Buttons ═══ */
        .btn-admin {
            padding: 9px 20px; border-radius: var(--radius-pill); font-size: 0.82rem; font-weight: 500;
            border: none; cursor: pointer; transition: all var(--transition); display: inline-flex;
            align-items: center; gap: 7px; text-decoration: none; font-family: 'Poppins', sans-serif;
        }
        .btn-gold { background: var(--gold); color: var(--black); }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: 0 4px 16px rgba(212,175,55,0.25); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-success:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(46,204,113,0.25); }
        .btn-danger { background: var(--danger); color: var(--white); }
        .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(231,76,60,0.25); }
        .btn-outline-gold { background: transparent; border: 1px solid rgba(212,175,55,0.4); color: var(--gold); }
        .btn-outline-gold:hover { background: rgba(212,175,55,0.1); border-color: var(--gold); transform: translateY(-2px); }
        .btn-sm { padding: 7px 14px; font-size: 0.78rem; }

        /* ═══ Forms ═══ */
        .admin-form .form-group { margin-bottom: 20px; }
        .admin-form label { display: block; font-size: 0.82rem; font-weight: 500; margin-bottom: 8px; color: rgba(255,255,255,0.7); }
        .admin-form .form-control {
            width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius);
            color: var(--white); font-family: 'Poppins', sans-serif; font-size: 0.9rem;
            outline: none; transition: all var(--transition);
        }
        .admin-form .form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,175,55,0.1); }
        .admin-form select.form-control { appearance: none; cursor: pointer; }
        .admin-form select.form-control option { background: var(--charcoal); }

        /* ═══ Alerts ═══ */
        .admin-alert {
            padding: 14px 20px; border-radius: var(--radius-md); margin-bottom: 20px;
            font-size: 0.88rem; display: flex; align-items: center; gap: 10px;
        }
        .admin-alert.success { background: rgba(46,204,113,0.08); border: 1px solid rgba(46,204,113,0.2); color: var(--success); }
        .admin-alert.error { background: rgba(231,76,60,0.08); border: 1px solid rgba(231,76,60,0.2); color: var(--danger); }

        /* ═══ Badges ═══ */
        .badge {
            padding: 4px 14px; border-radius: var(--radius-pill); font-size: 0.72rem;
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge-pending { background: rgba(212,175,55,0.12); color: var(--gold); }
        .badge-confirmed { background: rgba(46,204,113,0.12); color: var(--success); }
        .badge-preparing { background: rgba(52,152,219,0.12); color: var(--info); }
        .badge-completed { background: rgba(39,174,96,0.12); color: #27ae60; }
        .badge-cancelled { background: rgba(231,76,60,0.12); color: var(--danger); }
        .badge-refunded { background: rgba(149,165,166,0.12); color: #95a5a6; }

        /* ═══ Modals ═══ */
        .admin-modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        }
        .admin-modal-overlay.active { display: flex; }
        .admin-modal {
            background: var(--dark-grey); border: 1px solid rgba(212,175,55,0.15); border-radius: var(--radius-lg);
            padding: 36px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .admin-modal h3 { color: var(--gold); margin-bottom: 24px; font-size: 1.05rem; font-family: 'Playfair Display', serif; }

        /* ═══ Mobile Toggle ═══ */
        .admin-mobile-toggle {
            display: none; background: none; border: none; color: var(--gold);
            font-size: 1.2rem; cursor: pointer; padding: 8px;
        }

        /* ═══ Responsive ═══ */
        @media (max-width: 1024px) {
            .stat-cards { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); position: fixed; z-index: 200; width: 260px; }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .admin-content { padding: 20px 16px; }
            .stat-cards { grid-template-columns: 1fr; }
            .admin-mobile-toggle { display: block; }
            table.admin-table { font-size: 0.82rem; }
            table.admin-table th, table.admin-table td { padding: 10px 12px; }
        }
    </style>
</head>
<body>
    <!-- Mobile Sidebar Toggle -->
    <button class="admin-mobile-toggle" id="adminMobileToggle" style="position:fixed;top:14px;left:12px;z-index:201;">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-logo">
            <span class="logo-text">ZAMAHI</span>
            <span class="logo-sub">ADMIN PANEL</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
            <a href="bookings.php" class="<?= $currentPage === 'bookings' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> <span>Bookings</span></a>
            <a href="payments.php" class="<?= $currentPage === 'payments' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> <span>Payments</span></a>
            <div class="nav-divider"></div>
            <a href="menu.php" class="<?= $currentPage === 'menu' ? 'active' : '' ?>"><i class="fas fa-utensils"></i> <span>Menu Items</span></a>
            <a href="testimonials.php" class="<?= $currentPage === 'testimonials' ? 'active' : '' ?>"><i class="fas fa-star"></i> <span>Testimonials</span></a>
            <a href="gallery.php" class="<?= $currentPage === 'gallery' ? 'active' : '' ?>"><i class="fas fa-images"></i> <span>Gallery</span></a>
            <a href="offers.php" class="<?= $currentPage === 'offers' ? 'active' : '' ?>"><i class="fas fa-tags"></i> <span>Offers</span></a>
            <div class="nav-divider"></div>
            <a href="<?= SITE_URL ?>" target="_blank"><i class="fas fa-external-link-alt"></i> <span>View Site</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </aside>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="topbar-title"><?= ucfirst($currentPage) ?></div>
            <div class="topbar-right">
                <span style="color:var(--mid-grey);font-size:0.85rem;"><i class="fas fa-user" style="color:var(--gold);margin-right:6px;"></i><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
        <div class="admin-content">
            <?php
            $flash = getFlash();
            if ($flash): ?>
            <div class="admin-alert <?= $flash['type'] ?>">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
            <?php endif; ?>

    <script>
    /* Mobile sidebar toggle */
    document.getElementById('adminMobileToggle')?.addEventListener('click', function() {
        document.getElementById('adminSidebar').classList.toggle('open');
    });
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('adminSidebar');
        const toggle = document.getElementById('adminMobileToggle');
        if (sidebar && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
    </script>
