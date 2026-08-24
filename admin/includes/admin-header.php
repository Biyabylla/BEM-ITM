<?php
// Ambil data admin untuk foto profil
$admin_foto = '';
if (isset($_SESSION['admin_id'])) {
    $adm_q = mysqli_query($koneksi, "SELECT foto FROM admin_users WHERE id=" . (int)$_SESSION['admin_id']);
    $adm_row = mysqli_fetch_assoc($adm_q);
    $admin_foto = $adm_row['foto'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($admin_title) ? esc($admin_title) . ' - Admin BEM ITM' : 'Admin BEM ITM'; ?></title>
<link rel="icon" href="<?php echo esc('../' . setting('logo_bem', 'assets/img/logobem.png')); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{
    --maroon-900:#44121b;--maroon-800:#501824;--maroon-700:#5f212d;
    --maroon:#6d2130;--maroon-600:#7b2c3b;--maroon-tint:#f6e8ec;
    --gold:#a97f3d;--gold-dark:#8b682e;--gold-light:#ead8a6;
    --cream:#f7f0e2;--cream-soft:#fdf8ec;--field:#fffef8;
    --ink:#2a2126;--ink-soft:#6b5a5f;--line:#e7ddd3;--radius:14px;
    --shadow-sm:0 2px 10px rgba(68,18,27,.06);
    --shadow-md:0 12px 30px rgba(68,18,27,.12);
}
*{box-sizing:border-box;}
body{margin:0;font-family:'Inter',sans-serif;background:var(--cream);color:var(--ink);display:flex;min-height:100vh;}
h1,h2,h3,h4,h5{font-family:'Poppins',sans-serif;}
a{text-decoration:none;color:inherit;}
.sidebar{
    width:250px;background:linear-gradient(180deg,var(--maroon-900),var(--maroon-800));color:#fff;
    padding:26px 18px;position:sticky;top:0;height:100vh;flex-shrink:0;overflow-y:auto;transition:.3s;z-index:100;
}
.sidebar .brand{display:flex;align-items:center;gap:10px;margin-bottom:30px;padding:0 6px;}
.sidebar .brand .badge-b{width:38px;height:38px;border-radius:50%;background:var(--gold);color:var(--maroon-900);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;flex-shrink:0;}
.sidebar .brand b{font-size:.95rem;}
.sidebar .brand span{display:block;font-size:.65rem;opacity:.8;}
.sidebar nav a{
    display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;font-size:.86rem;font-weight:500;
    color:#eadfe1;margin-bottom:4px;transition:.2s;
}
.sidebar nav a:hover{background:rgba(255,255,255,.08);}
.sidebar nav a.active{background:var(--gold);color:var(--maroon-900);font-weight:700;}
.sidebar-close{display:none;position:absolute;top:14px;right:14px;width:32px;height:32px;border-radius:50%;border:none;background:rgba(255,255,255,.1);color:#fff;font-size:.85rem;cursor:pointer;z-index:10;align-items:center;justify-content:center;}
.sidebar-close:hover{background:rgba(255,255,255,.2);}
.sidebar .grp{font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;opacity:.55;margin:18px 6px 8px;}
.main{flex:1;min-width:0;}
.topbar{
    background:var(--field);border-bottom:1px solid var(--line);padding:16px 30px;display:flex;
    align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;
}
.topbar .menu-btn{display:none;background:var(--maroon-tint);color:var(--maroon);border:none;width:38px;height:38px;border-radius:8px;font-size:1.1rem;}
.content{padding:30px;}
.card{background:var(--cream-soft);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 2px 10px rgba(68,18,27,.06);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:9px;font-weight:600;font-size:.85rem;border:none;cursor:pointer;transition:.2s;font-family:'Poppins',sans-serif;}
.btn-primary{background:var(--maroon);color:#fff;}
.btn-primary:hover{background:var(--maroon-900);}
.btn-gold{background:var(--gold);color:var(--maroon-900);}
.btn-gold:hover{background:var(--gold-dark);color:#fff;}
.btn-outline{background:transparent;border:1.5px solid var(--line);color:var(--ink-soft);}
.btn-outline:hover{border-color:var(--maroon);color:var(--maroon);}
.btn-danger{background:#fdecec;color:#a3272a;}
.btn-danger:hover{background:#f8c9ca;}
.btn-sm{padding:7px 14px;font-size:.78rem;}
.table-responsive{overflow-x:auto;}
table{width:100%;border-collapse:collapse;min-width:600px;}
th,td{padding:13px 14px;text-align:left;font-size:.85rem;border-bottom:1px solid var(--line);}
th{background:var(--maroon-tint);color:var(--maroon-900);font-weight:700;font-size:.76rem;text-transform:uppercase;letter-spacing:.03em;}
tr:hover td{background:var(--cream);}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--line);border-radius:9px;font-size:.88rem;font-family:'Inter',sans-serif;background:var(--field);}
.form-control:focus{outline:none;border-color:var(--maroon);}
label{font-weight:600;font-size:.82rem;margin-bottom:6px;display:block;color:var(--maroon-900);}
.form-group{margin-bottom:16px;}
.alert{padding:14px 18px;border-radius:10px;font-size:.85rem;margin-bottom:20px;display:flex;gap:10px;}
.alert-success{background:#eaf7ee;color:#1e6b3a;}
.alert-danger{background:#fdecec;color:#a3272a;}
#toastWrap{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:360px;}
.toast{
    display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-radius:12px;font-size:.85rem;font-weight:600;
    background:var(--field);box-shadow:0 12px 34px rgba(68,18,27,.18);border:1px solid var(--line);border-left:4px solid var(--maroon);
    animation:toastIn .25s ease;font-family:'Inter',sans-serif;
}
.toast .t-icon{flex-shrink:0;font-size:1.05rem;line-height:1.3;}
.toast .t-close{margin-left:auto;cursor:pointer;color:var(--ink-soft);background:none;border:none;font-size:1rem;line-height:1;padding:2px;}
.toast.t-success{border-left-color:#2e9e5b;}
.toast.t-success .t-icon{color:#2e9e5b;}
.toast.t-error{border-left-color:#d4422b;}
.toast.t-error .t-icon{color:#d4422b;}
.toast.t-warning{border-left-color:var(--gold);}
.toast.t-warning .t-icon{color:var(--gold-dark);}
.toast.out{animation:toastOut .3s ease forwards;}
@keyframes toastIn{from{opacity:0;transform:translateX(30px);}to{opacity:1;transform:none;}}
@keyframes toastOut{to{opacity:0;transform:translateX(30px);}}
@media(max-width:480px){#toastWrap{left:14px;right:14px;max-width:none;}}
.badge{display:inline-block;padding:4px 11px;border-radius:50px;font-size:.72rem;font-weight:700;}
.badge-maroon{background:var(--maroon-tint);color:var(--maroon);}
.badge-gold{background:var(--gold-light);color:var(--gold-dark);}
.badge-danger{background:#fdecec;color:#a3272a;}
.stat-card{padding:22px;border-radius:14px;color:#fff;}
.page-head{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
.page-head h3{margin:0;color:var(--maroon-900);font-size:1.25rem;font-family:'Poppins',sans-serif;font-weight:700;}
.page-head h3 i{color:var(--maroon);margin-right:8px;}
.page-head p{color:var(--ink-soft);font-size:.85rem;font-weight:400;font-family:'Inter',sans-serif;margin:4px 0 0;}
.avatar-sm{width:38px;height:38px;border-radius:50%;object-fit:cover;}
.stat-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:18px;margin-bottom:30px;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:1000px){.stat-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:620px){.stat-grid{grid-template-columns:repeat(2,1fr);}.form-grid-2{grid-template-columns:1fr;}}
@media(max-width:880px){
    .sidebar{position:fixed;left:-280px;top:0;width:280px;box-shadow:none;transition:left .3s cubic-bezier(.4,0,.2,1);padding:20px 14px;overflow-y:auto;-webkit-overflow-scrolling:touch;}
    .sidebar.open{left:0;box-shadow:20px 0 60px rgba(0,0,0,.45);}
    .topbar .menu-btn{display:flex;align-items:center;justify-content:center;}
    .content{padding:18px;}
    .sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(30,8,12,.55);z-index:99;opacity:0;transition:opacity .3s ease;}
    .sidebar-backdrop.show{display:block;opacity:1;}
    .content [style*="grid-template-columns"]{grid-template-columns:1fr !important;}
    .sidebar .brand{margin-bottom:20px;padding:0 8px;}
    .sidebar .brand .badge-b{width:36px;height:36px;font-size:.8rem;}
    .sidebar .brand b{font-size:.9rem;}
    .sidebar .grp{font-size:.65rem;margin:14px 8px 6px;opacity:.6;letter-spacing:.08em;}
    .sidebar nav a{
        display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:12px;font-size:.9rem;font-weight:500;
        color:#eadfe1;margin-bottom:2px;transition:.15s;min-height:48px;
        touch-action:manipulation;-webkit-tap-highlight-color:transparent;
    }
    .sidebar nav a i{font-size:1.15rem;width:22px;text-align:center;flex-shrink:0;}
    .sidebar nav a:hover{background:rgba(255,255,255,.1);}
    .sidebar nav a.active{background:var(--gold);color:var(--maroon-900);font-weight:700;box-shadow:0 2px 8px rgba(169,127,61,.3);}
    .sidebar nav a:active{background:rgba(255,255,255,.15);transform:scale(.97);}
    .sidebar-close{display:flex;}
}
@media(max-width:420px){
    .sidebar{width:100%;left:-100%;}
    .sidebar.open{left:0;}
}
@media(max-width:620px){
    .content{padding:14px;}
    .page-head h3{font-size:1.05rem;}
    .page-head .btn{width:100%;justify-content:center;}
}
::selection{background:rgba(169,127,61,.25);color:var(--maroon-900);}
:is(a,button,input,select,textarea,.toast .t-close):focus-visible{outline:3px solid rgba(169,127,61,.5);outline-offset:2px;}
::-webkit-scrollbar{width:11px;}
::-webkit-scrollbar-track{background:var(--cream);}
::-webkit-scrollbar-thumb{background:linear-gradient(180deg,var(--maroon-700),var(--maroon));border-radius:8px;border:2px solid var(--cream);}
::-webkit-scrollbar-thumb:hover{background:var(--maroon-600);}

/* ===== INTERAKTIF DESKTOP ↔ MOBILE (admin) ===== */
html{scroll-behavior:smooth;}
*{-webkit-tap-highlight-color:transparent;}
@media(pointer:coarse){
    .btn:active{transform:scale(.96);}
    .card:active{transform:scale(.985);}
    .sidebar nav a:active{transform:scale(.98);}
}
@media (prefers-reduced-motion: reduce){
    *,*::before,*::after{animation-duration:.01ms !important;animation-iteration-count:1 !important;transition-duration:.01ms !important;}
}
</style>
</head>
<body>
<?php $adm = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="adminSidebar">
    <button class="sidebar-close" id="sidebarClose" aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
    <div class="brand" style="cursor:pointer;" onclick="window.location='profil-admin.php'">
        <?php if (!empty($admin_foto)): ?>
        <img src="<?php echo esc(img_url($admin_foto)); ?>?v=<?php echo time(); ?>" alt="Foto"
             style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--gold);flex-shrink:0;">
        <?php else: ?>
        <span class="badge-b"><?php echo strtoupper(substr($_SESSION['admin_nama'] ?? 'A',0,1)); ?></span>
        <?php endif; ?>
        <div><b><?php echo esc($_SESSION['admin_nama'] ?? 'Admin'); ?></b><span><?php echo esc(ROLES[$_SESSION['admin_role']]['label'] ?? 'Admin'); ?></span></div>
    </div>    <div class="grp">Utama</div>
    <nav>
        <a href="index.php" class="<?php echo $adm=='index.php'?'active':''; ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
    </nav>
    <?php if (cek_akses('pengaturan') || cek_akses('kabinet') || cek_akses('profil') || cek_akses('sambutan') || cek_akses('departemen') || cek_akses('pengurus') || cek_akses('pendaftaran')): ?>
    <div class="grp">Konten Situs</div>
    <nav>
        <?php if (cek_akses('pengaturan')): ?><a href="pengaturan.php" class="<?php echo $adm=='pengaturan.php'?'active':''; ?>"><i class="bi bi-sliders"></i> Identitas &amp; Pengaturan</a><?php endif; ?>
        <?php if (cek_akses('kabinet')): ?><a href="kabinet.php" class="<?php echo $adm=='kabinet.php'?'active':''; ?>"><i class="bi bi-flag-fill"></i> Kabinet</a><?php endif; ?>
        <?php if (cek_akses('profil')): ?><a href="profil.php" class="<?php echo $adm=='profil.php'?'active':''; ?>"><i class="bi bi-person-lines-fill"></i> Profil &amp; Visi Misi</a><?php endif; ?>
        <?php if (cek_akses('sambutan')): ?><a href="sambutan.php" class="<?php echo $adm=='sambutan.php'?'active':''; ?>"><i class="bi bi-megaphone-fill"></i> Sambutan</a><?php endif; ?>
        <?php if (cek_akses('departemen')): ?><a href="departemen.php" class="<?php echo $adm=='departemen.php'?'active':''; ?>"><i class="bi bi-diagram-3-fill"></i> Departemen</a><?php endif; ?>
        <?php if (cek_akses('pengurus')): ?><a href="pengurus.php" class="<?php echo $adm=='pengurus.php'?'active':''; ?>"><i class="bi bi-person-raised-hand"></i> Struktur / Pengurus</a><?php endif; ?>
        <?php if (cek_akses('pendaftaran')): ?><a href="pendaftaran.php" class="<?php echo $adm=='pendaftaran.php'?'active':''; ?>"><i class="bi bi-person-check-fill"></i> Pendaftaran Rekrutmen</a><?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php if (cek_akses('program-kerja') || cek_akses('berita')): ?>
    <div class="grp">Publikasi</div>
    <nav>
        <?php if (cek_akses('program-kerja')): ?><a href="program-kerja.php" class="<?php echo $adm=='program-kerja.php'?'active':''; ?>"><i class="bi bi-kanban-fill"></i> Program Kerja</a><?php endif; ?>
        <?php if (cek_akses('berita')): ?><a href="berita.php" class="<?php echo $adm=='berita.php'?'active':''; ?>"><i class="bi bi-newspaper"></i> Berita</a><?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php if (cek_akses('aspirasi')): ?>
    <div class="grp">Interaksi</div>
    <nav>
        <a href="aspirasi.php" class="<?php echo $adm=='aspirasi.php'?'active':''; ?>"><i class="bi bi-chat-left-text-fill"></i> Aspirasi Masuk</a>
    </nav>
    <?php endif; ?>
    <?php if (cek_akses('kelola-admin')): ?>
    <div class="grp">Pengelolaan</div>
    <nav>
        <a href="kelola-admin.php" class="<?php echo $adm=='kelola-admin.php'?'active':''; ?>"><i class="bi bi-people-fill"></i> Kelola Admin</a>
    </nav>
    <?php endif; ?>
    <div class="grp">Lainnya</div>
    <nav>
        <a href="profil-admin.php" class="<?php echo $adm=='profil-admin.php'?'active':''; ?>"><i class="bi bi-person-circle"></i> Profil Saya</a>
        <a href="../index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Lihat Situs</a>
        <a href="javascript:void(0)" onclick="return confirmAction('Yakin ingin logout?', 'logout.php')"><i class="bi bi-power"></i> Logout</a>
    </nav>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<div class="main">
    <div class="topbar">
        <button class="menu-btn" id="menuToggle"><i class="bi bi-list"></i></button>
        <h2 style="margin:0;font-size:1.15rem;color:var(--maroon-900);"><?php echo esc($admin_title ?? 'Dashboard'); ?></h2>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:.82rem;color:var(--ink-soft);"><?php echo esc($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
            <?php if (!empty($admin_foto)): ?>
            <a href="profil-admin.php" style="display:block;" title="Profil Saya">
                <img src="<?php echo esc(img_url($admin_foto)); ?>?v=<?php echo time(); ?>" alt="Foto Profil"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--gold);cursor:pointer;">
            </a>
            <?php else: ?>
            <a href="profil-admin.php" class="avatar-sm" style="background:var(--maroon);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;text-decoration:none;border:2px solid var(--gold);cursor:pointer;" title="Profil Saya">
                <?php echo strtoupper(substr($_SESSION['admin_nama'] ?? 'A',0,1)); ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="content">
    <?php if (!empty($msg)): ?><script>document.addEventListener('DOMContentLoaded',function(){showToast('<?php echo addslashes($msg); ?>','success');});</script><?php endif; ?>
    <?php if (!empty($err)): ?><script>document.addEventListener('DOMContentLoaded',function(){showToast('<?php echo addslashes($err); ?>','error');});</script><?php endif; ?>
