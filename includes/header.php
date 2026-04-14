<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? '';
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kasir – <?= ucfirst($page) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --brand: #1a56db; --brand-dark: #1340a8; --brand-light: #eff6ff;
    --accent: #f59e0b; --bg: #f8fafc; --white: #fff;
    --text: #1e293b; --muted: #64748b; --border: #e2e8f0;
    --danger: #ef4444; --success: #10b981; --warning: #f59e0b;
    --sidebar-w: 240px; --radius: 12px;
  }
  body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg);
         color: var(--text); display: flex; min-height: 100vh; }

  .sidebar {
    width: var(--sidebar-w); background: var(--brand-dark); color: #fff;
    display: flex; flex-direction: column; position: fixed; top: 0; left: 0;
    height: 100vh; z-index: 100;
  }
  .sidebar-logo {
    padding: 24px 20px; display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
  }
  .sidebar-logo span { font-size: 22px; }
  .sidebar-logo h2 { font-size: 16px; font-weight: 800; letter-spacing: -.3px; }
  .sidebar-logo small { font-size: 11px; opacity: .6; }
  nav { flex: 1; padding: 16px 12px; }
  .nav-label { font-size: 10px; font-weight: 700; letter-spacing: 1px;
                text-transform: uppercase; opacity: .5; padding: 10px 8px 4px; }
  nav a {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px;
    border-radius: 8px; color: rgba(255,255,255,.75); text-decoration: none;
    font-size: 14px; font-weight: 500; transition: all .15s; margin-bottom: 2px;
  }
  nav a:hover, nav a.active { background: rgba(255,255,255,.15); color: #fff; }
  nav a .icon { font-size: 20px; width: 24px; text-align: center; }
  .sidebar-footer {
    padding: 16px 12px; border-top: 1px solid rgba(255,255,255,.12);
  }
  .sidebar-user { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
  .avatar {
    width: 36px; height: 36px; background: var(--accent); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 14px; color: #fff; flex-shrink: 0;
  }
  .sidebar-user-info small { display: block; font-size: 11px; opacity:.55; }
  .sidebar-user-info strong { font-size: 13px; }
  .btn-logout {
    display: flex; align-items: center; gap: 8px; width: 100%; padding: 9px 12px;
    background: rgba(239,68,68,.2); color: #fca5a5; border: none; border-radius: 8px;
    font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
    transition: background .15s;
  }
  .btn-logout:hover { background: rgba(239,68,68,.35); color: #fff; }

  .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }
  .topbar {
    background: var(--white); border-bottom: 1px solid var(--border);
    padding: 16px 28px; display: flex; align-items: center; justify-content: space-between;
  }
  .topbar h1 { font-size: 20px; font-weight: 800; }
  .content { padding: 28px; flex: 1; }

  .card { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; }
  .card-header { padding: 18px 24px; border-bottom: 1px solid var(--border);
                  display: flex; align-items: center; justify-content: space-between; }
  .card-header h2 { font-size: 15px; font-weight: 700; }
  .card-body { padding: 24px; }
  .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
  .stat-card {
    background: var(--white); border-radius: var(--radius); padding: 20px;
    border: 1px solid var(--border); display: flex; align-items: center; gap: 16px;
  }
  .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex;
                align-items: center; justify-content: center; font-size: 22px; flex-shrink:0; }
  .stat-icon.blue   { background: #eff6ff; }
  .stat-icon.green  { background: #f0fdf4; }
  .stat-icon.amber  { background: #fffbeb; }
  .stat-icon.red    { background: #fef2f2; }
  .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; }
  .stat-value { font-size: 22px; font-weight: 800; }

  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  th { background: var(--bg); padding: 10px 16px; text-align: left; font-size: 12px;
       font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
  td { padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #fafbff; }

  .badge {
    display: inline-block; padding: 3px 10px; border-radius: 99px;
    font-size: 11px; font-weight: 700;
  }
  .badge-blue   { background: var(--brand-light); color: var(--brand); }
  .badge-green  { background: #f0fdf4; color: #059669; }
  .badge-amber  { background: #fffbeb; color: #d97706; }
  .badge-red    { background: #fef2f2; color: var(--danger); }

  .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px;
          border-radius: 8px; font-family: inherit; font-size: 13px; font-weight: 600;
          cursor: pointer; border: none; text-decoration: none; transition: all .15s; }
  .btn-primary   { background: var(--brand); color: #fff; }
  .btn-primary:hover { background: var(--brand-dark); }
  .btn-success   { background: var(--success); color: #fff; }
  .btn-danger    { background: var(--danger); color: #fff; }
  .btn-secondary { background: var(--bg); color: var(--text); border: 1px solid var(--border); }
  .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 6px; }

  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
  .form-control {
    width: 100%; padding: 10px 14px; border: 2px solid var(--border); border-radius: 8px;
    font-family: inherit; font-size: 14px; outline: none; transition: border .2s;
  }
  .form-control:focus { border-color: var(--brand); }
  .form-row { display: grid; gap: 16px; }
  .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
  .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }

  .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
  .alert-success { background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0; }
  .alert-danger  { background: #fef2f2; color: var(--danger); border: 1px solid #fecaca; }
</style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-logo">
    <span style="font-size: 28px; color: var(--brand-light);"><i class="ph-fill ph-shopping-cart-simple"></i></span>
    <div><h2>Aplikasi Kasir</h2></div>
  </div>
  <nav>
    <div class="nav-label">Menu Utama</div>
    <a href="dashboard.php" class="<?= $page==='dashboard'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-squares-four"></i></span> Dashboard
    </a>
    <a href="kasir.php" class="<?= $page==='kasir'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-receipt"></i></span> Transaksi Kasir
    </a>
    <a href="stok.php" class="<?= $page==='stok'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-package"></i></span> Stok Barang
    </a>
    <a href="laporan.php" class="<?= $page==='laporan'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-chart-line-up"></i></span> Laporan Penjualan
    </a>
    <?php if ($role === 'administrator'): ?>
    <div class="nav-label">Administrator</div>
    <a href="produk.php" class="<?= $page==='produk'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-tag"></i></span> Pendataan Barang
    </a>
    <a href="pelanggan.php" class="<?= $page==='pelanggan'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-users"></i></span> Data Pelanggan
    </a>
    <a href="registrasi.php" class="<?= $page==='registrasi'?'active':'' ?>">
      <span class="icon"><i class="ph-fill ph-user-plus"></i></span> Manajemen User
    </a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($nama,0,1)) ?></div>
      <div class="sidebar-user-info">
        <strong><?= htmlspecialchars($nama) ?></strong>
        <small><?= ucfirst($role) ?></small>
      </div>
    </div>
    <form action="../logout.php" method="POST">
      <button class="btn-logout" type="submit"><i class="ph-bold ph-sign-out" style="font-size: 16px;"></i> Keluar</button>
    </form>
  </div>
</aside>
<main class="main">
