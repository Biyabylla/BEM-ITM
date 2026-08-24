<?php
$page_title = 'Berita';
include 'header.php';

$per_page = 9;
$page_num = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page_num - 1) * $per_page;

$r_total = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM berita"));
$total = (int)($r_total[0] ?? 0);
$total_page = max(1, ceil($total / $per_page));

$berita = [];
$q = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_publish DESC, id DESC LIMIT $per_page OFFSET $offset");
while ($row = mysqli_fetch_assoc($q)) $berita[] = $row;
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span>Berita</div>
        <span class="eyebrow"><i class="bi bi-newspaper"></i> Update Terkini</span>
        <h1>Berita & Kegiatan</h1>
        <p class="lead">Informasi terbaru seputar kegiatan dan program kerja BEM Institut Teknologi Mojosari.</p>
    </div>
</section>

<section>
    <div class="container">
        <div class="grid grid-3">
            <?php foreach ($berita as $b): ?>
            <a href="berita-detail.php?slug=<?php echo esc($b['slug']); ?>" class="card reveal">
                <div class="card-media">
                    <img src="<?php echo esc($b['gambar'] ?: 'https://placehold.co/400x220/7A1F2B/fff?text=BEM+ITM'); ?>" alt="<?php echo esc($b['judul']); ?>">
                    <span class="media-date"><i class="bi bi-calendar3"></i> <?php echo tanggal_indo($b['tanggal_publish']); ?></span>
                </div>
                <div class="card-body-pad">
                    <h4 class="card-title"><?php echo esc($b['judul']); ?></h4>
                    <p class="card-text"><?php echo esc(mb_strimwidth(strip_tags($b['konten']), 0, 100, '...')); ?></p>
                    <span class="link-more">Baca Selengkapnya <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if (empty($berita)): ?>
                <p class="text-center" style="grid-column:1/-1;color:var(--ink-soft);">Belum ada berita yang dipublikasikan.</p>
            <?php endif; ?>
        </div>

        <?php if ($total_page > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_page; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="page-btn <?php echo $i==$page_num?'active':''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
