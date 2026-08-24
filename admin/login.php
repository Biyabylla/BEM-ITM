<?php
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    // Rate-limit sederhana: blokir setelah 5x gagal selama 5 menit
    $max_coba = 5;
    $waktu_blokir = 300; // detik
    $now = time();
    $gagal = $_SESSION['login_gagal'] ?? ['jumlah' => 0, 'sampai' => 0];

    if ($gagal['jumlah'] >= $max_coba && $now < $gagal['sampai']) {
        $sisa = $gagal['sampai'] - $now;
        $error = 'Terlalu banyak percobaan gagal. Silakan coba lagi dalam ' . ceil($sisa / 60) . ' menit.';
    } elseif (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Sesi kedaluwarsa. Silakan muat ulang halaman lalu coba lagi.';
    } else {
        // Anti-bot: honeypot & timing
        $honeypot = $_POST['website'] ?? '';
        $waktu_submit = (int)($_POST['waktu_submit'] ?? 0);
        if ($honeypot !== '' || ($waktu_submit > 0 && time() - $waktu_submit < 2)) {
            $error = 'Login gagal.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $stmt = mysqli_prepare($koneksi, "SELECT * FROM admin_users WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin = mysqli_fetch_assoc($result);

            if ($admin && password_verify($password, $admin['password'])) {
                unset($_SESSION['login_gagal']);
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                $_SESSION['admin_role'] = $admin['role'];
                header('Location: index.php');
                exit;
            } else {
                $jumlah = $gagal['jumlah'] + 1;
                $_SESSION['login_gagal'] = ['jumlah' => $jumlah, 'sampai' => $jumlah >= $max_coba ? $now + $waktu_blokir : $now];
                $error = 'Username atau password salah.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - BEM ITM</title>
<link rel="icon" href="<?php echo esc('../' . setting('logo_bem', 'assets/img/logobem.png')); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;background:#f5f1ec;}

.login-left{
    flex:1;background:linear-gradient(160deg,#2a0a10 0%,#44121b 35%,#6d2130 65%,#7b2c3b 100%);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:40px;color:#fff;position:relative;overflow:hidden;
}
.login-left::before{
    content:'';position:absolute;top:-30%;left:-20%;width:600px;height:600px;
    border-radius:50%;background:rgba(169,127,61,.06);animation:float 15s ease-in-out infinite;
}
.login-left::after{
    content:'';position:absolute;bottom:-20%;right:-10%;width:400px;height:400px;
    border-radius:50%;background:rgba(169,127,61,.04);animation:float 12s ease-in-out infinite reverse;
}
@keyframes float{0%,100%{transform:translate(0,0);}50%{transform:translate(15px,-15px);}}
.login-left .brand{position:relative;z-index:2;text-align:center;max-width:340px;}
.login-left .logo-lg{
    width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#a97f3d,#8b682e);
    color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;
    font-family:'Poppins',sans-serif;font-size:1.6rem;margin:0 auto 24px;
    box-shadow:0 12px 36px rgba(0,0,0,.3);
}
.login-left .brand h2{font-family:'Poppins',sans-serif;font-size:1.6rem;font-weight:800;margin-bottom:10px;line-height:1.3;}
.login-left .brand p{font-size:.9rem;color:rgba(255,255,255,.7);line-height:1.6;margin:0;}
.login-left .features{position:relative;z-index:2;margin-top:40px;text-align:left;}
.login-left .features li{list-style:none;padding:10px 0;font-size:.85rem;color:rgba(255,255,255,.8);display:flex;align-items:center;gap:12px;}
.login-left .features li i{font-size:1.1rem;color:#a97f3d;}

.login-right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px;}

.login-card{width:100%;max-width:400px;animation:slideUp .4s ease;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:none;}}
.login-card .card-header{text-align:center;margin-bottom:32px;}
.login-card .card-header h1{font-family:'Poppins',sans-serif;font-size:1.5rem;color:#44121b;font-weight:800;margin:0 0 6px;}
.login-card .card-header p{color:#8a7478;font-size:.88rem;margin:0;}
.login-card .card-header .divider-line{width:50px;height:3px;background:linear-gradient(90deg,#a97f3d,#6d2130);border-radius:2px;margin:14px auto 0;}

.alert-box{
    padding:13px 16px;border-radius:10px;font-size:.85rem;margin-bottom:22px;
    display:flex;align-items:center;gap:10px;animation:shake .4s ease;
}
@keyframes shake{0%,100%{transform:translateX(0);}20%,60%{transform:translateX(-5px);}40%,80%{transform:translateX(5px);}}
.alert-danger{background:#fdecec;color:#a3272a;border:1px solid #f5c6c7;}

.form-group{margin-bottom:20px;}
.form-group label{font-weight:600;font-size:.82rem;color:#44121b;display:block;margin-bottom:7px;}
.input-wrap{position:relative;}
.input-wrap .field-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#b8a89e;font-size:1rem;pointer-events:none;}
.input-wrap input{
    width:100%;padding:13px 44px 13px 42px;border:1.5px solid #e0d8ce;border-radius:10px;
    font-size:.9rem;background:#fff;font-family:'Inter',sans-serif;transition:.2s;color:#333;
}
.input-wrap input:focus{outline:none;border-color:#6d2130;box-shadow:0 0 0 3px rgba(109,33,48,.08);}
.input-wrap input::placeholder{color:#c4b5aa;}
.toggle-pass{
    position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;
    cursor:pointer;color:#b8a89e;font-size:1.1rem;padding:4px;transition:.2s;display:flex;
}
.toggle-pass:hover{color:#6d2130;}

.form-options{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.remember{display:flex;align-items:center;gap:7px;font-size:.82rem;color:#666;cursor:pointer;}
.remember input[type=checkbox]{width:15px;height:15px;accent-color:#6d2130;cursor:pointer;border-radius:3px;}

.btn-login{
    width:100%;padding:14px;border:none;border-radius:10px;
    background:linear-gradient(135deg,#6d2130 0%,#501824 100%);
    color:#fff;font-weight:700;font-size:.92rem;cursor:pointer;
    font-family:'Poppins',sans-serif;transition:.2s;
    display:flex;align-items:center;justify-content:center;gap:8px;
    box-shadow:0 4px 15px rgba(109,33,48,.25);
}
.btn-login:hover{background:linear-gradient(135deg,#501824 0%,#44121b 100%);box-shadow:0 6px 20px rgba(109,33,48,.35);transform:translateY(-1px);}
.btn-login:active{transform:translateY(0);box-shadow:0 2px 8px rgba(109,33,48,.25);}
.btn-login:disabled{opacity:.7;cursor:not-allowed;transform:none;}

.card-footer{text-align:center;margin-top:28px;padding-top:20px;border-top:1px solid #ece5dc;}
.card-footer .back-link{
    display:inline-flex;align-items:center;gap:6px;font-size:.84rem;color:#6d2130;
    text-decoration:none;font-weight:600;transition:.2s;
}
.card-footer .back-link:hover{color:#44121b;}

.security-badge{text-align:center;margin-top:16px;font-size:.72rem;color:#b8a89e;display:flex;align-items:center;justify-content:center;gap:5px;}
.security-badge i{color:#a97f3d;}

@media(max-width:900px){
    body{flex-direction:column;}
    .login-left{padding:32px 24px;min-height:auto;}
    .login-left .brand h2{font-size:1.3rem;}
    .login-left .features{display:none;}
    .login-right{padding:28px 24px 40px;}
}
@media(max-width:480px){
    .login-card .card-header h1{font-size:1.3rem;}
    .form-options{flex-direction:column;gap:10px;align-items:flex-start;}
}
</style>
</head>
<body>

<div class="login-left">
    <div class="brand">
        <div class="logo-lg">BEM</div>
        <h2>Badan Eksekutif Mahasiswa ITM</h2>
        <p>Sistem manajemen website untuk mengelola konten, pengurus, dan informasi organisasi dalam satu panel terintegrasi.</p>
    </div>
    <ul class="features">
        <li><i class="bi bi-check-circle-fill"></i> Kelola pengurus & departemen</li>
        <li><i class="bi bi-check-circle-fill"></i> Publikasi berita & program kerja</li>
        <li><i class="bi bi-check-circle-fill"></i> Pantau aspirasi mahasiswa</li>
        <li><i class="bi bi-check-circle-fill"></i> Statistik & analitik real-time</li>
    </ul>
</div>

<div class="login-right">
    <div class="login-card">
        <div class="card-header">
            <h1>Selamat Datang</h1>
            <p>Masuk ke panel admin BEM ITM</p>
            <div class="divider-line"></div>
        </div>

        <?php if ($error): ?>
        <div class="alert-box alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span><?php echo esc($error); ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" id="loginForm">
            <?php echo csrf_field(); ?>
            <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0;">
            <input type="hidden" name="waktu_submit" value="<?php echo time(); ?>">

            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person field-icon"></i>
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock field-icon"></i>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    <button type="button" class="toggle-pass" onclick="togglePassword()" tabindex="-1">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="remember">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login" id="btnSubmit">
                <span>Masuk</span>
                <i class="bi bi-arrow-right-circle"></i>
            </button>
        </form>

        <div class="card-footer">
            <a href="../index.php" class="back-link">
                <i class="bi bi-arrow-left"></i> Kembali ke Situs
            </a>
        </div>

        <div class="security-badge">
            <i class="bi bi-shield-lock-fill"></i>
            Login dilindungi enkripsi & keamanan berlapis
        </div>
    </div>
</div>

<script>
function togglePassword(){
    var p=document.getElementById('password'),e=document.getElementById('eyeIcon');
    if(p.type==='password'){p.type='text';e.className='bi bi-eye-slash';}
    else{p.type='password';e.className='bi bi-eye';}
}
document.getElementById('loginForm').addEventListener('submit',function(){
    var b=document.getElementById('btnSubmit');
    b.disabled=true;
    b.innerHTML='<span>Memproses...</span><i class="bi bi-arrow-repeat" style="animation:spin .8s linear infinite;"></i>';
});
</script>
<style>@keyframes spin{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}</style>
</body>
</html>
