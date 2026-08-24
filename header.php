<?php
require_once __DIR__ . '/config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? esc($page_title) . ' - ' . SITE_SHORT : SITE_NAME; ?></title>
<meta name="description" content="Website resmi Badan Eksekutif Mahasiswa (BEM) Institut Teknologi Mojosari periode <?php echo SITE_PERIODE; ?>">
<link rel="icon" href="<?php echo esc(setting('logo_bem', 'assets/img/logobem.png')); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
/* ==========================================================
   BEM INSTITUT TEKNOLOGI MOJOSARI — STYLE TERPADU (CSS+JS)
   Tema Utama: Maroon | Sekunder: Emas/Gold (pelengkap maroon)
   ========================================================== */
:root{
    --maroon-900:#44121b;
    --maroon-800:#501824;
    --maroon-700:#5f212d;
    --maroon:#6d2130;      /* warna utama */
    --maroon-600:#7b2c3b;
    --maroon-500:#8d3a49;
    --maroon-tint:#f6e8ec;
    --gold:#a97f3d;        /* warna sekunder — champagne */
    --gold-dark:#8b682e;
    --gold-light:#ead8a6;
    --cream:#f7f0e2;       /* latar website — krem hangat */
    --cream-soft:#fdf8ec;  /* permukaan kartu */
    --card-cream:#fdfaf2;  /* kartu kepengurusan — krem pucat lembut */
    --field:#fffef8;       /* input */
    --ink:#2a2126;
    --ink-soft:#6b5a5f;
    --line:#e7ddd3;
    --radius:18px;
    --shadow-sm:0 2px 10px rgba(68,18,27,.06);
    --shadow-md:0 12px 30px rgba(68,18,27,.12);
    --shadow-lg:0 20px 50px rgba(68,18,27,.18);
}
*{box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{
    margin:0;
    font-family:'Inter',sans-serif;
    background:linear-gradient(180deg,#f6eede 0%,var(--cream) 32%,var(--cream) 100%);
    color:var(--ink);
    overflow-x:hidden;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    -webkit-font-smoothing:antialiased;
    text-rendering:optimizeLegibility;
}
h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;}
a{text-decoration:none;color:inherit;}
img{max-width:100%;display:block;}
.container{width:100%;max-width:1180px;margin:0 auto;padding:0 20px;}
.btn{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    padding:13px 28px;border-radius:50px;font-weight:600;font-size:.92rem;
    border:2px solid transparent;cursor:pointer;transition:.25s cubic-bezier(.4,0,.2,1);
    font-family:'Poppins',sans-serif; line-height:1;
}
.btn-primary{background:var(--maroon);color:#fff;box-shadow:0 8px 20px rgba(109,33,48,.25);}
.btn-primary:hover{background:var(--maroon-900);transform:translateY(-3px);box-shadow:0 12px 26px rgba(109,33,48,.32);}
.btn-gold{background:var(--gold);color:var(--maroon-900);box-shadow:0 8px 20px rgba(169,127,61,.3);}
.btn-gold:hover{background:var(--gold-dark);color:#fff;transform:translateY(-3px);}
.btn-outline{background:transparent;border-color:#fff;color:#fff;}
.btn-outline:hover{background:rgba(255,255,255,.12);transform:translateY(-3px);}
.btn-outline-maroon{background:transparent;border-color:var(--maroon);color:var(--maroon);}
.btn-outline-maroon:hover{background:var(--maroon);color:#fff;transform:translateY(-3px);}
.btn-sm{padding:9px 18px;font-size:.82rem;}

/* ===== NAVBAR ===== */
.navbar{
    position:sticky;top:0;z-index:1000;
    background:linear-gradient(120deg,var(--maroon-900),var(--maroon-700));
    padding:14px 0; transition:.3s ease; box-shadow:0 2px 16px rgba(0,0,0,.12);
}
.navbar.scrolled{padding:8px 0;box-shadow:0 8px 26px rgba(0,0,0,.2);}
.navbar .container{display:flex;align-items:center;justify-content:space-between;}
.nav-brand{display:flex;align-items:center;gap:12px;color:#fff;}
.nav-brand .logo-badge{
    width:44px;height:44px;border-radius:50%;background:var(--gold);
    display:flex;align-items:center;justify-content:center;font-weight:800;
    color:var(--maroon-900);font-family:'Poppins',sans-serif;font-size:1.1rem;
    flex-shrink:0;box-shadow:0 4px 12px rgba(169,127,61,.4);
}
.nav-brand .brand-text b{display:block;font-size:1.02rem;font-weight:700;font-family:'Poppins',sans-serif;}
.nav-brand .brand-text span{display:block;font-size:.68rem;opacity:.85;letter-spacing:.03em;}
.nav-menu{display:flex;align-items:center;gap:6px;list-style:none;margin:0;padding:0;}
.nav-menu a{
    color:#f2e4e6;font-size:.86rem;font-weight:600;padding:10px 14px;border-radius:8px;
    transition:.2s;position:relative;
}
.nav-menu a:hover, .nav-menu a.active{color:#fff;background:rgba(255,255,255,.1);}
.nav-menu a.active::after{
    content:'';position:absolute;left:14px;right:14px;bottom:3px;height:2.5px;background:var(--gold);border-radius:3px;
}
.nav-cta{margin-left:6px;}
.nav-toggle{
    display:none;background:rgba(255,255,255,.12);border:none;width:42px;height:42px;border-radius:10px;
    color:#fff;font-size:1.3rem;cursor:pointer;align-items:center;justify-content:center;
}

@media(max-width:991px){
    .nav-toggle{display:flex;}
    .nav-menu{
        position:fixed;top:0;right:-100%;height:100vh;width:78%;max-width:320px;
        background:linear-gradient(180deg,var(--maroon-900),var(--maroon-800));
        flex-direction:column;align-items:stretch;padding:90px 24px 24px;gap:4px;
        transition:right .35s cubic-bezier(.4,0,.2,1);box-shadow:-10px 0 40px rgba(0,0,0,.3);
        overflow-y:auto;
    }
    .nav-menu.open{right:0;}
    .nav-menu a{padding:14px 16px;font-size:.95rem;border-bottom:1px solid rgba(255,255,255,.06);}
    .nav-cta{margin:14px 0 0;}
    .nav-cta .btn{width:100%;}
    .nav-overlay{
        display:none;position:fixed;inset:0;background:rgba(20,5,8,.55);z-index:998;
    }
    .nav-overlay.open{display:block;}
}

/* ===== HERO / PAGE HEADER ===== */
.page-hero{
    background:linear-gradient(135deg,var(--maroon-900) 0%,var(--maroon) 60%,var(--maroon-600) 100%);
    color:#fff;position:relative;overflow:hidden;padding:64px 0 76px;
}
.page-hero::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(circle at 85% 15%,rgba(169,127,61,.35),transparent 45%),
               radial-gradient(circle at 5% 90%,rgba(169,127,61,.18),transparent 40%);
}
.page-hero .container{position:relative;z-index:2;}
.breadcrumb-nav{font-size:.82rem;opacity:.85;margin-bottom:14px;}
.breadcrumb-nav a{color:var(--gold-light);}
.breadcrumb-nav span{margin:0 6px;opacity:.6;}
.eyebrow{
    display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);
    border:1px solid rgba(255,255,255,.2);color:var(--gold-light);font-size:.74rem;font-weight:700;
    letter-spacing:.06em;text-transform:uppercase;padding:7px 16px;border-radius:50px;margin-bottom:18px;
}
.page-hero h1{font-size:clamp(1.8rem,4vw,2.7rem);font-weight:800;margin:0 0 14px;}
.page-hero p.lead{font-size:1.02rem;opacity:.92;max-width:640px;line-height:1.7;margin:0;}

/* ===== KABINET IDENTITY ===== */
.kabinet-chip{
    display:inline-flex;align-items:center;gap:12px;margin-bottom:14px;
    background:rgba(255,255,255,.1);border:1px solid rgba(234,216,166,.4);
    padding:10px 18px 10px 12px;border-radius:50px;
    backdrop-filter:blur(4px);
}
.kabinet-chip + .eyebrow{margin-bottom:18px;}
.kabinet-logo{width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;}
.kabinet-chip .kabinet-kicker{display:block;font-size:.62rem;letter-spacing:.1em;text-transform:uppercase;color:var(--gold-light);opacity:.9;}
.kabinet-chip .kabinet-name{display:block;font-size:.95rem;font-weight:700;color:#fff;font-family:'Poppins',sans-serif;line-height:1.2;}

/* ===== KABINET SELECTOR (halaman struktur) ===== */
.kabinet-select{
    display:flex;flex-wrap:wrap;gap:10px;margin-top:24px;
}
.kabinet-select-label{
    display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:50px;
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
    color:var(--gold-light);font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;
    white-space:nowrap;
}
.kabinet-opt{
    display:flex;align-items:center;gap:10px;padding:8px 16px;border-radius:50px;
    background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.25);color:#fff;transition:.2s;
}
.kabinet-opt:hover{background:rgba(255,255,255,.16);transform:translateY(-2px);}
.kabinet-opt.active{background:var(--gold);border-color:var(--gold);color:var(--maroon-900);box-shadow:0 6px 18px rgba(169,127,61,.4);}
.kabinet-opt-logo{width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;}
.kabinet-opt b{display:block;font-size:.82rem;line-height:1.2;font-family:'Poppins',sans-serif;}
.kabinet-opt em{display:block;font-style:normal;font-size:.68rem;opacity:.85;}
.kabinet-empty{
    text-align:center;padding:40px 20px;color:var(--ink-soft);font-size:.9rem;
}

/* ===== SECTIONS / CARDS ===== */
section{padding:70px 0;}
.section-tag{color:var(--maroon);font-weight:700;text-transform:uppercase;font-size:.78rem;letter-spacing:.08em;display:block;margin-bottom:10px;}
.section-title{font-size:clamp(1.5rem,3vw,2.1rem);font-weight:800;color:var(--maroon-900);margin:0 0 16px;}
.section-sub{color:var(--ink-soft);font-size:1rem;line-height:1.75;max-width:680px;}
.divider-mini{width:64px;height:5px;background:linear-gradient(90deg,var(--maroon),var(--gold));border-radius:4px;}

.card{
    background:var(--cream-soft);border:1px solid var(--line);border-radius:var(--radius);
    box-shadow:var(--shadow-sm);transition:.3s cubic-bezier(.4,0,.2,1);overflow:hidden;
}
.card:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);}

.grid{display:grid;gap:26px;}
.grid-2{grid-template-columns:repeat(2,1fr);}
.grid-3{grid-template-columns:repeat(3,1fr);}
.grid-4{grid-template-columns:repeat(4,1fr);}
@media(max-width:900px){.grid-3,.grid-4{grid-template-columns:repeat(2,1fr);}}
@media(max-width:600px){.grid-2,.grid-3,.grid-4{grid-template-columns:1fr;}}

/* ===== FILTER TABS (dept/struktur) ===== */
.filter-tabs{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:36px;}
.filter-tabs button{
    background:var(--cream-soft);border:1.5px solid var(--line);color:var(--ink-soft);font-family:'Poppins',sans-serif;
    padding:9px 18px;border-radius:50px;font-size:.82rem;font-weight:600;cursor:pointer;transition:.2s;
}
.filter-tabs button:hover{border-color:var(--maroon);color:var(--maroon);}
.filter-tabs button.active{background:var(--maroon);border-color:var(--maroon);color:#fff;}

.fade-item{animation:fadeUp .5s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

/* ===== PROFILE / MEMBER CARD ===== */
.member-card{text-align:center;padding:26px 20px 24px;}
.member-card .avatar{
    width:104px;height:104px;border-radius:50%;object-fit:cover;margin:0 auto 16px;
    border:4px solid var(--gold-light);box-shadow:var(--shadow-sm);
}
.member-card h4{margin:0 0 4px;font-size:1.02rem;color:var(--maroon-900);}
.member-card .role{color:var(--maroon);font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:.03em;margin-bottom:4px;}
.member-card .prodi{color:var(--ink-soft);font-size:.82rem;margin-bottom:12px;}

.contact-links{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:auto;}
.contact-links a{
    display:inline-flex;align-items:center;gap:6px;font-size:.8rem;font-weight:600;color:var(--maroon);
    border:1px solid var(--line);padding:6px 14px;border-radius:50px;transition:.2s;text-decoration:none;
}
.contact-links a:hover{background:var(--maroon);color:#fff;border-color:var(--maroon);}
.contact-links a.cl-gmail i{color:#d4422b;}
.contact-links a.cl-gmail:hover i{color:#fff;}
.contact-links a.cl-ig i{color:#e1306c;}
.contact-links a.cl-ig:hover i{color:#fff;}
.leader-card .contact-links,.member-card .contact-links{justify-content:center;}

/* ===== LEADER CARD (pembina/pimpinan/ketua) ===== */
.leader-card{
    position:relative;overflow:hidden;
    background:var(--card-cream);
    border:2px solid var(--gold) !important;
    box-shadow:0 14px 34px rgba(169,127,61,.22);
    text-align:center;
}
.leader-card::after{ /* ornamen lingkaran emas di pojok bawah */
    content:'';position:absolute;right:-52px;bottom:-52px;width:170px;height:170px;border-radius:50%;
    background:radial-gradient(circle,rgba(169,127,61,.14),transparent 65%);
    pointer-events:none;
}
.leader-band{
    position:relative;display:flex;align-items:center;justify-content:center;gap:10px;
    padding:15px 16px 22px;
    background:linear-gradient(120deg,#2f0a12,#44121b 40%,var(--maroon-600));
    color:var(--gold-light);
    font-size:.72rem;font-weight:800;letter-spacing:.22em;text-transform:uppercase;
    clip-path:polygon(0 0,100% 0,100% calc(100% - 16px),50% 100%,0 calc(100% - 16px));
}
.leader-band i{font-size:1.02rem;color:var(--gold);line-height:1;}
.leader-card .card-body{position:relative;padding:26px 20px 28px;}
.leader-card .avatar{
    width:112px;height:112px;border-radius:50%;object-fit:cover;margin:0 auto 16px;
    border:4px solid #fff;
    box-shadow:0 0 0 3px var(--gold),0 12px 26px rgba(169,127,61,.38);
}
.leader-card h4{margin:0 0 5px;font-size:1.1rem;color:var(--maroon-900);}
.leader-card .role{
    display:inline-flex;align-items:center;gap:8px;
    color:var(--gold-dark);font-weight:700;font-size:.72rem;text-transform:uppercase;
    letter-spacing:.18em;margin:0 0 6px;
}
.leader-card .role::before,.leader-card .role::after{
    content:'';width:22px;height:1px;background:linear-gradient(90deg,transparent,var(--gold));
}
.leader-card .role::after{background:linear-gradient(90deg,var(--gold),transparent);}
.leader-card .role b{
    font-size:1rem;color:var(--gold);font-weight:800;letter-spacing:0;
    transform:translateY(-1px);display:inline-block;
}
.leader-card .prodi{color:var(--ink-soft);font-size:.82rem;margin-bottom:14px;}

/* ===== SUB-HEADING BPH ===== */
.group-heading{display:flex;align-items:center;justify-content:center;gap:12px;margin:0 0 24px;}
.group-heading .gh-marker{
    width:38px;height:38px;border-radius:12px;flex-shrink:0;
    background:linear-gradient(135deg,var(--maroon),var(--maroon-600));
    color:var(--gold-light);display:flex;align-items:center;justify-content:center;font-size:1.1rem;
}
.group-heading h3{margin:0;font-size:1.06rem;color:var(--maroon-900);}
.group-heading span{display:block;font-size:.74rem;color:var(--ink-soft);font-weight:500;letter-spacing:.03em;}

/* ===== DEPARTEMEN BLOCK (per-dept layout) ===== */
.dept-block{margin-bottom:48px;}
.dept-block:last-child{margin-bottom:0;}
.dept-head{
    display:flex;align-items:center;gap:14px;margin-bottom:22px;padding-bottom:14px;
    border-bottom:2px solid var(--line);
}
.dept-head .d-icon{
    width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--maroon),var(--maroon-600));
    color:var(--gold-light);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;
}
.dept-head h3{margin:0;font-size:1.12rem;color:var(--maroon-900);}
.dept-head span{display:block;font-size:.78rem;color:var(--ink-soft);font-weight:500;}
.dept-count{
    margin-left:auto;flex-shrink:0;font-size:.72rem;font-weight:700;color:var(--maroon);
    background:var(--maroon-tint);border:1px solid var(--line);padding:5px 14px;border-radius:50px;
}
@media(max-width:520px){.dept-head{flex-direction:column;align-items:flex-start;}.dept-count{margin-left:0;}}

/* ===== BADGE ===== */
.badge-dept{
    display:inline-block;background:var(--maroon-tint);color:var(--maroon);font-size:.72rem;font-weight:700;
    padding:5px 13px;border-radius:50px;text-transform:uppercase;letter-spacing:.03em;
}
.badge-gold{background:var(--gold-light);color:var(--gold-dark);}

/* ===== FOOTER ===== */
.footer{
    margin-top:auto;background:linear-gradient(180deg,var(--maroon-900),#2c0c12);color:#f0dde0;
    padding:60px 0 26px;
}
.footer h5{color:#fff;font-size:1rem;margin-bottom:18px;}
.footer p, .footer li, .footer a{color:#e2c6ca;font-size:.88rem;line-height:1.9;}
.footer a:hover{color:var(--gold-light);}
.footer .flogo{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
.footer .logo-badge{
    width:44px;height:44px;border-radius:50%;background:var(--gold);
    display:flex;align-items:center;justify-content:center;font-weight:800;
    color:var(--maroon-900);font-family:'Poppins',sans-serif;font-size:1rem;flex-shrink:0;
    box-shadow:0 4px 12px rgba(169,127,61,.35);
}
.footer li i{color:var(--gold-light);width:18px;text-align:center;margin-right:6px;}
.footer ul{list-style:none;padding:0;margin:0;}
.footer .fgrid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1.2fr;gap:36px;}
@media(max-width:800px){.footer .fgrid{grid-template-columns:1fr 1fr;}}
@media(max-width:520px){.footer .fgrid{grid-template-columns:1fr;}}
.footer .socials{display:flex;gap:10px;margin-top:16px;}
.footer .socials a{
    width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.08);display:flex;
    align-items:center;justify-content:center;transition:.2s;
}
.footer .socials a:hover{background:var(--gold);color:var(--maroon-900);}
.footer-bottom{border-top:1px solid rgba(255,255,255,.1);margin-top:36px;padding-top:22px;
    display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;font-size:.8rem;color:#c9a7ab;}

/* ===== MISC ===== */
.alert{padding:16px 20px;border-radius:12px;font-size:.9rem;margin-bottom:22px;display:flex;gap:10px;align-items:flex-start;}
.alert-success{background:#eaf7ee;color:#1e6b3a;border:1px solid #bfe6cb;}
.alert-danger{background:#fdecec;color:#a3272a;border:1px solid #f4c3c4;}
.form-control{
    width:100%;padding:12px 16px;border:1.5px solid var(--line);border-radius:10px;font-family:'Inter',sans-serif;
    font-size:.92rem;transition:.2s;background:var(--field);color:var(--ink);
}
.form-control:focus{outline:none;border-color:var(--maroon);box-shadow:0 0 0 3px rgba(109,33,48,.1);}
label{font-weight:600;font-size:.85rem;color:var(--maroon-900);margin-bottom:7px;display:block;}
.form-group{margin-bottom:20px;}
.text-center{text-align:center;}
.mt-0{margin-top:0;}

/* ==========================================================
   POLISH — tata letak profesional & konsisten
   ========================================================== */
/* ===== SECTION HEADING STANDAR ===== */
.section-head{text-align:center;margin-bottom:44px;}
.section-head .divider-mini{margin:0 auto;}
.section-head.left{text-align:left;}
.section-head.left .divider-mini{margin:0;}
.section-head .section-title{margin-bottom:12px;}

/* ===== KARTU MEDIA (berita / proker) ===== */
.card-media{position:relative;overflow:hidden;aspect-ratio:16/10;}
.card-media img{width:100%;height:100%;object-fit:cover;transition:transform .55s cubic-bezier(.4,0,.2,1);}
.card:hover .card-media img{transform:scale(1.06);}
.card-media .media-date{
    position:absolute;top:14px;left:14px;z-index:2;
    background:rgba(68,18,27,.88);backdrop-filter:blur(4px);color:#fff;
    font-size:.72rem;font-weight:600;padding:6px 12px;border-radius:50px;
    display:inline-flex;align-items:center;gap:6px;
}
.card-body-pad{padding:22px 22px 24px;}
.card .card-title{font-size:1.05rem;color:var(--maroon-900);margin:0 0 8px;line-height:1.4;}
.card .card-text{font-size:.86rem;color:var(--ink-soft);line-height:1.7;margin:0;}
.link-more{
    display:inline-flex;align-items:center;gap:6px;margin-top:14px;
    font-size:.82rem;font-weight:700;color:var(--maroon);
    transition:gap .2s;
}
.link-more:hover{gap:10px;color:var(--maroon-900);}

/* ===== META ROW KARTU (badge kiri, status kanan) ===== */
.card-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:8px;}

/* ===== FOOTER KARTU ===== */
.card-foot{
    margin-top:16px;border-top:1px solid var(--line);padding-top:14px;
    font-size:.8rem;color:var(--maroon);font-weight:700;
    display:flex;align-items:center;gap:7px;
}

/* ===== RESPONSIVE UTILITY ===== */
@media(max-width:640px){
    .card-body-pad{padding:18px;}
}

/* ===== PAGINATION ===== */
.pagination{display:flex;justify-content:center;gap:8px;margin-top:42px;flex-wrap:wrap;}
.pagination .page-btn{
    min-width:42px;height:42px;display:inline-flex;align-items:center;justify-content:center;
    border:1.5px solid var(--line);background:var(--cream-soft);color:var(--ink-soft);border-radius:12px;
    font-weight:600;font-size:.88rem;transition:.2s;padding:0 14px;
}
.pagination .page-btn:hover{border-color:var(--maroon);color:var(--maroon);transform:translateY(-2px);}
.pagination .page-btn.active{background:var(--maroon);border-color:var(--maroon);color:#fff;box-shadow:0 6px 16px rgba(109,33,48,.25);}

/* ===== NAVBAR LOGO RAPI ===== */
.nav-brand img{width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,.18);border:2px solid rgba(234,216,166,.5);}

/* ===== FILTER TABS — scroll aman di layar kecil ===== */
@media(max-width:720px){
    .filter-tabs{flex-wrap:nowrap;overflow-x:auto;padding-bottom:8px;margin-bottom:30px;scrollbar-width:thin;}
    .filter-tabs button{flex-shrink:0;}
}

/* ===== MEMBER CARD POLISH ===== */
.member-card{border:1px solid var(--line);background:var(--card-cream);}
.member-card .avatar{width:96px;height:96px;}

/* ===== GRID ALIGN ===== */
.grid-2{grid-template-columns:repeat(2,1fr);}
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:center;}
@media(max-width:900px){.about-grid{grid-template-columns:1fr;gap:34px;}}
/* Grid inline dua kolom di halaman publik → tumpuk jadi satu kolom */
@media(max-width:640px){
    .grid[style*="grid-template-columns"]{grid-template-columns:1fr !important;}
}

/* ===== HERO RESPONSIVE ===== */
.page-hero{padding:64px 0 76px;}
.page-hero .lead{font-size:1.02rem;line-height:1.75;}
@media(max-width:640px){
    .page-hero{padding:48px 0 60px;}
    .page-hero h1{font-size:1.65rem;}
    .page-hero .lead{font-size:.95rem;}
}

/* ===== SCROLL TO TOP ===== */
.to-top{
    position:fixed;right:22px;bottom:22px;z-index:900;
    width:46px;height:46px;border-radius:14px;border:none;cursor:pointer;
    background:linear-gradient(135deg,var(--maroon),var(--maroon-600));
    color:#fff;font-size:1.15rem;display:flex;align-items:center;justify-content:center;
    box-shadow:0 10px 24px rgba(109,33,48,.35);
    opacity:0;visibility:hidden;transform:translateY(16px);
    transition:.3s cubic-bezier(.4,0,.2,1);
}
.to-top.show{opacity:1;visibility:visible;transform:translateY(0);}
.to-top:hover{background:var(--maroon-900);transform:translateY(-3px);}

/* ===== FOOTER LOGO ===== */
.footer .flogo-img{height:52px;width:52px;object-fit:cover;border-radius:50%;border:2px solid rgba(234,216,166,.45);}

/* ===== EYEBROW POLISH ===== */
.eyebrow i{font-size:.9rem;}

/* ===== SAMBUTAN CARD ===== */
.sambut-card{
    display:flex;flex-direction:column;gap:14px;
    background:linear-gradient(180deg,var(--cream-soft),#f3e8d3);
    border:1px solid var(--line);border-radius:18px;padding:26px;
    box-shadow:var(--shadow-sm);transition:.3s cubic-bezier(.4,0,.2,1);
    position:relative;overflow:hidden;
}
.sambut-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);}
.sambut-card::before{
    content:'';position:absolute;top:0;left:0;right:0;height:4px;
    background:linear-gradient(90deg,var(--maroon),var(--gold));
}
.sambut-card .sc-top{display:flex;align-items:center;justify-content:space-between;gap:8px;}
.sambut-card .sc-top i{font-size:1.8rem;color:var(--gold);opacity:.85;}
.sambut-card .sc-top .badge-dept{
    font-family:'Poppins',sans-serif;font-size:.7rem;font-weight:700;letter-spacing:.08em;
    background:linear-gradient(120deg,var(--maroon),var(--maroon-600));color:var(--gold-light);
    border:1px solid rgba(169,127,61,.35);box-shadow:0 3px 10px rgba(109,33,48,.18);
}
.sambut-card .sc-text{
    font-size:.94rem;color:var(--ink);line-height:1.85;margin:0;flex:1;
    font-family:'Poppins',sans-serif;font-weight:400;
}
.sambut-card .sc-person{display:flex;align-items:center;gap:12px;padding-top:14px;border-top:1px solid var(--line);}
.sambut-card .sc-person img{width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--gold-light);box-shadow:0 4px 12px rgba(169,127,61,.25);}
.sambut-card .sc-person b{
    display:block;font-size:.98rem;color:var(--maroon-900);
    font-family:'Poppins',sans-serif;font-weight:700;line-height:1.3;
}
.sambut-card .sc-person span{
    display:inline-block;margin-top:2px;font-size:.72rem;color:var(--gold-dark);
    font-family:'Poppins',sans-serif;font-weight:600;text-transform:uppercase;letter-spacing:.07em;
}

/* ==========================================================
   POLISH — Halaman Struktur (profesional & clean)
   ========================================================== */
/* Kartu anggota — bersih, tinggi sejajar, transisi halus */
.member-card{
    display:flex;flex-direction:column;align-items:center;
    text-align:center;padding:28px 22px 24px;height:100%;
    border:1px solid var(--line);border-radius:16px;
}
.member-card .avatar{
    width:96px;height:96px;border-radius:50%;object-fit:cover;margin:0 auto 16px;
    border:3px solid var(--maroon-tint);box-shadow:0 8px 20px rgba(109,33,48,.12);
}
.member-card h4{margin:0 0 5px;font-size:1rem;color:var(--maroon-900);line-height:1.35;}
.member-card .role{font-size:.76rem;letter-spacing:.05em;}
.member-card .prodi{margin-bottom:14px;}
.member-card .contact-links{margin-top:auto;}

/* Kartu pemimpin — bingkai emas tipis, bayangan lembut */
.leader-card{
    border:1.5px solid var(--gold-light) !important;
    box-shadow:0 10px 28px rgba(169,127,61,.16);
    display:flex;flex-direction:column;height:100%;
}
.leader-card .leader-band{
    padding:14px 16px 20px;font-size:.68rem;letter-spacing:.18em;
}
.leader-card .card-body{display:flex;flex-direction:column;align-items:center;padding:24px 20px 26px;}
.leader-card .avatar{
    width:104px;height:104px;border:3px solid #fff;
    box-shadow:0 0 0 2.5px var(--gold),0 10px 22px rgba(109,33,48,.18);
}
.leader-card .prodi{margin-bottom:14px;}
.leader-card .contact-links{margin-top:auto;}

/* Leader-card ukuran kecil (menyatu dengan grid staf) */
.leader-card.small .leader-band{
    padding:10px 12px 16px;font-size:.6rem;letter-spacing:.14em;
}
.leader-card.small .card-body{padding:22px 16px 24px;}
.leader-card.small .avatar{
    width:96px;height:96px;border-width:2.5px;
    box-shadow:0 0 0 2px var(--gold),0 8px 18px rgba(109,33,48,.15);
}
.leader-card.small h4{font-size:1rem;line-height:1.35;}
.leader-card.small .role,.leader-card.small .prodi,.leader-card.small h4{text-align:center;}

/* Jarak antar grup di halaman struktur */
.group-heading{margin:0 0 22px;}
.group-heading + .grid{margin-bottom:40px;}
.group-heading + .grid-2 + .group-heading,
.group-heading + div + .group-heading{margin-top:16px;}

/* Susunan departemen lebih rapi */
.dept-head{border-bottom:1px solid var(--line);margin-bottom:24px;}
.dept-count{background:var(--cream-soft);border:1px solid var(--line);}
.dept-block .grid-2{margin-bottom:24px;}
.dept-block .grid-4{row-gap:18px;}

/* Grid seimbang di layar sedang */
@media(max-width:640px){
    .member-card{padding:22px 16px 20px;}
    .group-heading .gh-marker{width:34px;height:34px;font-size:1rem;}
}

/* Empty state struktur — lebih elegan */
.kabinet-empty{
    background:var(--cream-soft);border:1.5px dashed var(--line);border-radius:18px;
    padding:52px 24px;text-align:center;color:var(--ink-soft);
}
.kabinet-empty i{color:var(--gold);}

/* ==========================================================
   POLISH PROFESIONAL — aksesibilitas & kenyamanan membaca
   ========================================================== */
::selection{background:rgba(169,127,61,.25);color:var(--maroon-900);}
a,button,.btn,.filter-tabs button,.kabinet-opt,.contact-links a,.card,.to-top{transition:all .25s cubic-bezier(.4,0,.2,1);}
:is(a,button,input,select,textarea,.filter-tabs button,.kabinet-opt,.page-btn):focus-visible{
    outline:3px solid rgba(169,127,61,.5);outline-offset:2px;border-radius:8px;
}
.card{box-shadow:var(--shadow-sm);}
.card:hover{transform:translateY(-6px);box-shadow:0 18px 42px rgba(68,18,27,.14);}
::placeholder{color:#a99a9d;}
::-webkit-scrollbar{width:11px;}
::-webkit-scrollbar-track{background:var(--cream);}
::-webkit-scrollbar-thumb{background:linear-gradient(180deg,var(--maroon-700),var(--maroon));border-radius:8px;border:2px solid var(--cream);}
::-webkit-scrollbar-thumb:hover{background:var(--maroon-600);}

/* ===== INTERAKTIF DESKTOP ↔ MOBILE ===== */
html{scroll-behavior:smooth;}
*{-webkit-tap-highlight-color:transparent;}
a,button,.btn,.card,.contact-links a,.page-btn,.kabinet-opt,.filter-tabs button{-webkit-touch-callout:none;}
@media(pointer:coarse){
    .btn:active,.contact-links a:active,.page-btn:active{transform:scale(.96);}
    .card:active{transform:scale(.985);}
    .nav-menu a:active,.sidebar nav a:active{transform:scale(.98);}
}
@media(max-width:640px){
    .container{padding-left:20px;padding-right:20px;}
    .section-tag{font-size:.72rem;}
    .section-title{font-size:1.45rem;}
}
@media (prefers-reduced-motion: reduce){
    *,*::before,*::after{animation-duration:.01ms !important;animation-iteration-count:1 !important;transition-duration:.01ms !important;scroll-behavior:auto !important;}
}
</style>
</head>
<body>

<nav class="navbar" id="mainNavbar">
    <div class="container">
        <a href="index.php" class="nav-brand">
            <img src="<?php echo esc(setting('logo_bem', 'assets/img/logobem.png')); ?>" alt="BEM Logo">
            <span class="brand-text">
                <b><?php echo esc(SITE_SHORT); ?></b>
                <span><?php echo esc(SITE_NAME); ?></span>
            </span>
        </a>
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php" class="<?php echo ($current_page=='index.php'||$current_page=='')?'active':''; ?>">Home</a></li>
            <li><a href="tentang.php" class="<?php echo ($current_page=='tentang.php')?'active':''; ?>">Tentang Kami</a></li>
            <li><a href="struktur.php" class="<?php echo ($current_page=='struktur.php')?'active':''; ?>">Struktur</a></li>
            <li><a href="program-kerja.php" class="<?php echo ($current_page=='program-kerja.php')?'active':''; ?>">Program Kerja</a></li>
            <li><a href="berita.php" class="<?php echo (in_array($current_page,['berita.php','berita-detail.php']))?'active':''; ?>">Berita</a></li>
            <li><a href="pendaftaran.php" class="<?php echo ($current_page=='pendaftaran.php')?'active':''; ?>">Pendaftaran</a></li>
            <li class="nav-cta"><a href="aspirasi.php" class="btn btn-gold btn-sm"><i class="bi bi-chat-dots"></i> Sampaikan Aspirasi</a></li>
        </ul>
        <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
    </div>
</nav>
<div class="nav-overlay" id="navOverlay"></div>
