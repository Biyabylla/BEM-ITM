<?php
require_once __DIR__ . '/config.php';

$slug = $_GET['slug'] ?? '';
$stmt = mysqli_prepare($koneksi, "SELECT * FROM berita WHERE slug = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$b = mysqli_fetch_assoc($result);

if (!$b) {
    header('Location: berita.php');
    exit;
}

$page_title = $b['judul'];
require_once __DIR__ . '/header.php';

// Berita terkait
$terkait = [];
$stmt2 = mysqli_prepare($koneksi, "SELECT * FROM berita WHERE id != ? ORDER BY tanggal_publish DESC LIMIT 3");
mysqli_stmt_bind_param($stmt2, 'i', $b['id']);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
while ($row = mysqli_fetch_assoc($res2)) $terkait[] = $row;
?>

<section class="page-hero" style="padding:56px 0 70px;">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span><a href="berita.php">Berita</a><span>/</span><?php echo esc(mb_strimwidth($b['judul'],0,40,'...')); ?></div>
        <span class="eyebrow"><i class="bi bi-calendar3"></i> <?php echo tanggal_indo($b['tanggal_publish']); ?></span>
        <h1 style="max-width:820px;"><?php echo esc($b['judul']); ?></h1>
        <p class="lead">Ditulis oleh <?php echo esc($b['penulis'] ?: 'Redaksi BEM ITM'); ?></p>
    </div>
</section>

<section>
    <div class="container" style="max-width:820px;">
        <div class="card-media" style="border-radius:18px;margin-bottom:34px;aspect-ratio:16/9;box-shadow:var(--shadow-md);">
            <img src="<?php echo esc($b['gambar'] ?: 'https://placehold.co/800x420/7A1F2B/fff?text=BEM+ITM'); ?>" alt="<?php echo esc($b['judul']); ?>">
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:22px;">
            <span class="badge-dept"><i class="bi bi-person"></i> <?php echo esc($b['penulis'] ?: 'Redaksi BEM ITM'); ?></span>
            <span class="badge-gold badge-dept"><i class="bi bi-calendar3"></i> <?php echo tanggal_indo($b['tanggal_publish']); ?></span>
        </div>
        <div style="font-size:.98rem;line-height:1.9;color:var(--ink);border-left:3px solid var(--gold);padding-left:18px;">
            <?php echo nl2br(esc($b['konten'])); ?>
        </div>
        <a href="berita.php" class="btn btn-outline-maroon" style="margin-top:36px;"><i class="bi bi-arrow-left"></i> Kembali ke Berita</a>
    </div>
</section>

<?php if (!empty($terkait)): ?>
<section style="background:var(--maroon-tint);">
    <div class="container">
        <div class="section-head" style="margin-bottom:28px;">
            <h3 style="color:var(--maroon-900);margin:0;">Berita Lainnya</h3>
            <div class="divider-mini"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($terkait as $t): ?>
            <a href="berita-detail.php?slug=<?php echo esc($t['slug']); ?>" class="card">
                <div class="card-media" style="aspect-ratio:16/9;">
                    <img src="<?php echo esc($t['gambar'] ?: 'https://placehold.co/400x220/7A1F2B/fff?text=BEM+ITM'); ?>" alt="<?php echo esc($t['judul']); ?>">
                </div>
                <div class="card-body-pad">
                    <span class="media-date" style="position:static;"><i class="bi bi-calendar3"></i> <?php echo tanggal_indo($t['tanggal_publish']); ?></span>
                    <h5 class="card-title" style="font-size:.95rem;"><?php echo esc($t['judul']); ?></h5>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
