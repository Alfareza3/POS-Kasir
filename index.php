<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'classes/Auth.php';
    $auth = new Auth();
    if($auth->login($_POST['username'], $_POST['password'])) {
        header("Location: pages/dashboard.php");
        exit;
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
    <title>Login – Aplikasi Kasir</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
        --brand: #1a56db; --brand-dark: #1340a8; --accent: #f59e0b;
        --bg: #f0f4ff; --white: #ffffff; --text: #1e293b; --muted: #64748b;
        --danger: #ef4444; --radius: 14px;
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg);
            display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .card {
        background: var(--white); border-radius: var(--radius); padding: 48px 40px;
        width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(26,86,219,.15);
    }
    .logo { text-align: center; margin-bottom: 32px; }
    .logo-icon {
        width: 60px; height: 60px; background: var(--brand); border-radius: 16px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 32px; margin-bottom: 12px; color: #fff;
    }
    h1 { font-size: 26px; font-weight: 800; color: var(--text); }
    p.sub { color: var(--muted); font-size: 14px; margin-top: 4px; }
    label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    input[type=text], input[type=password] {
        width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 15px; font-family: inherit; outline: none; transition: border .2s;
    }
    input:focus { border-color: var(--brand); }
    .field { margin-bottom: 18px; }
    .btn {
        width: 100%; padding: 14px; background: var(--brand); color: #fff;
        border: none; border-radius: 10px; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: background .2s; margin-top: 8px;
    }
    .btn:hover { background: var(--brand-dark); }
    .error { background: #fef2f2; color: var(--danger); border-radius: 8px;
                padding: 10px 14px; font-size: 13px; margin-bottom: 16px; border: 1px solid #fecaca; }
    .hint { text-align:center; font-size:12px; color:var(--muted); margin-top:20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon"><i class="ph-fill ph-shopping-cart"></i></div>
            <h1>Aplikasi Kasir</h1>
            <p class="sub">Masuk untuk melanjutkan</p>
        </div>
        <?php if ($error): ?>
            <div class="error"><i class="ph-fill ph-warning-circle" style="vertical-align: text-bottom; font-size: 16px;"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="field">
                <label>Username</label>
                <input type="text" name="username" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn">Masuk</button>
        </form>
    </div>
</body>
</html>