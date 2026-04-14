<?php
require_once '../classes/Auth.php';
Auth::cekAdmin();

$auth = new Auth();
$msg = ''; $msgType = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (isset($_POST['simpan'])) {
        if (strlen($password) < 6) {
            $msg = "Password minimal 6 karakter."; $msgType = 'danger';
        } else {
            $ok = $auth->register($nama, $username, $password, $role);
            $msg = $ok ? "User berhasil didaftarkan!" : "Gagal: username mungkin sudah digunakan.";
            $msgType = $ok ? 'success' : 'danger';
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $ok = $auth->edit($id, $nama, $username, $password, $role);
        $msg = $ok ? "Data user diperbarui!" : "Gagal memperbarui data user.";
        $msgType = $ok ? 'success' : 'danger';
    }
}

if (isset($_GET['hapus'])) {
    $ok = $auth->hapus((int)$_GET['hapus']);
    $msg = $ok ? "User dihapus." : "Gagal menghapus user (tidak bisa menghapus akun sendiri).";
    $msgType = $ok ? 'success' : 'danger';
}

if (isset($_GET['edit'])) {
    $edit = $auth->getById((int)$_GET['edit']);
}

$users = $auth->getAll();

include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-user-plus"></i> Manajemen User</h1></div>
<div class="content">
<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start">
  <div class="card">
    <div class="card-header"><h2><?= $edit ? '<i class="ph-bold ph-pencil-simple"></i> Edit User' : '<i class="ph-bold ph-plus"></i> Tambah User' ?></h2></div>
    <div class="card-body">
    <form method="POST">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['UserID'] ?>"><?php endif; ?>
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" required
               value="<?= htmlspecialchars($edit['NamaUser'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required
               value="<?= htmlspecialchars($edit['Username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password <?= $edit ? '<small style="color:var(--muted);font-weight:normal;font-size:11px">(Kosongkan jika tidak diubah)</small>' : '' ?></label>
        <input type="password" name="password" class="form-control" <?= $edit ? '' : 'required minlength="6"' ?>>
      </div>
      <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control">
          <option value="petugas" <?= ($edit && $edit['Role'] === 'petugas') ? 'selected' : '' ?>>Petugas</option>
          <option value="administrator" <?= ($edit && $edit['Role'] === 'administrator') ? 'selected' : '' ?>>Administrator</option>
        </select>
      </div>
      <?php if ($edit): ?>
        <button name="update" class="btn btn-primary" style="width:100%"><i class="ph-bold ph-floppy-disk"></i> Update User</button>
        <a href="registrasi.php" class="btn btn-secondary" style="width:100%;margin-top:8px;text-align:center;display:block">Batal</a>
      <?php else: ?>
        <button name="simpan" class="btn btn-primary" style="width:100%"><i class="ph-bold ph-plus"></i> Tambah User</button>
      <?php endif; ?>
    </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Daftar User</h2>
      <span style="font-size:13px;color:var(--muted)"><?= count($users) ?> user</span>
    </div>
    <table>
      <thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Role</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($users as $i => $u): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($u['NamaUser']) ?></td>
          <td><code><?= htmlspecialchars($u['Username']) ?></code></td>
          <td><span class="badge <?= $u['Role']==='administrator'?'badge-blue':'badge-green' ?>">
            <?= ucfirst($u['Role']) ?>
          </span></td>
          <td>
            <a href="?edit=<?= $u['UserID'] ?>" class="btn btn-secondary btn-sm"><i class="ph-bold ph-pencil-simple"></i></a>
            <?php if ($u['UserID'] != $_SESSION['user_id']): ?>
              <a href="?hapus=<?= $u['UserID'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Hapus user ini?')"><i class="ph-bold ph-trash"></i></a>
            <?php else: ?>
              <button disabled class="btn btn-secondary btn-sm" style="opacity:0.5;cursor:not-allowed" title="Anda tidak bisa menghapus akun sendiri"><i class="ph-bold ph-trash"></i></button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>
