<?php
$page_title = 'Tentang Kami';
include 'header.php';

$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil_bem ORDER BY id DESC LIMIT 1"));

$misi_list = [];
$q = mysqli_query($koneksi, "SELECT * FROM misi ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($q)) $misi_list[] = $row;

$tujuan_list = [];
$q2 = mysqli_query($koneksi, "SELECT * FROM tujuan ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($q2)) $tujuan_list[] = $row;
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span>Tentang Kami</div>
        <span class="eyebrow"><i class="bi bi-info-circle"></i> Profil Organisasi</span>
        <h1>Tentang BEM Institut Teknologi Mojosari</h1>
        <p class="lead">Mengenal lebih dekat visi, misi, dan tujuan lembaga eksekutif mahasiswa Institut Teknologi Mojosari.</p>
    </div>
</section>

<section>
    <div class="container">
        <div class="card reveal" style="padding:38px 34px;margin-bottom:40px;">
            <span class="section-tag">Siapa Kami</span>
            <h2 class="section-title">Deskripsi Organisasi</h2>
            <p class="section-sub" style="max-width:100%;">
                <?php echo nl2br(esc($profil['deskripsi'] ?? 'BEM Institut Teknologi Mojosari adalah lembaga eksekutif tertinggi di tingkat mahasiswa yang bertugas menghimpun aspirasi, mengoordinasikan kegiatan kemahasiswaan, dan menjadi representasi mahasiswa dalam pengambilan kebijakan kampus.')); ?>
            </p>
        </div>

        <div class="grid grid-2" style="align-items:stretch;">
            <div class="card reveal vm-visi" style="background:linear-gradient(135deg,var(--maroon-900),var(--maroon));color:#fff;">
                <i class="bi bi-eye" style="font-size:2rem;color:var(--gold-light);"></i>
                <h3 style="color:#fff;margin:16px 0 12px;">Visi</h3>
                <p style="opacity:.92;line-height:1.8;font-size:.95rem;"><?php echo nl2br(esc($profil['visi'] ?? 'Mewujudkan BEM Institut Teknologi Mojosari yang aspiratif, kolaboratif, inovatif, dan berintegritas dalam mendukung kemajuan mahasiswa dan institusi.')); ?></p>
            </div>

            <div class="card reveal vm-misi">
                <i class="bi bi-flag" style="font-size:2rem;color:var(--maroon);"></i>
                <h3 style="color:var(--maroon-900);margin:16px 0 12px;">Misi</h3>
                <ul class="vm-misi-list">
                    <?php if (!empty($misi_list)): foreach ($misi_list as $i => $m): ?>
                    <li class="vm-misi-item">
                        <button class="vm-misi-toggle" onclick="this.parentElement.classList.toggle('open')">
                            <span class="nomor"><?php echo $i+1; ?></span>
                            <span><?php echo esc(mb_strimwidth($m['isi_misi'],0,60,'...')); ?></span>
                            <i class="bi bi-chevron-down arrow"></i>
                        </button>
                        <div class="vm-misi-body">
                            <div class="vm-misi-body-inner"><?php echo esc($m['isi_misi']); ?></div>
                        </div>
                    </li>
                    <?php endforeach; else: ?>
                        <li style="color:var(--ink-soft);font-size:.9rem;padding:14px 4px;">Data misi belum tersedia.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card reveal" style="padding:38px 34px;margin-top:26px;">
            <div class="section-head">
                <i class="bi bi-bullseye" style="font-size:2rem;color:var(--maroon);"></i>
                <h3 style="color:var(--maroon-900);margin:14px 0 4px;">Tujuan</h3>
                <div class="divider-mini"></div>
            </div>
            <div class="grid grid-3">
                <?php if (!empty($tujuan_list)): foreach ($tujuan_list as $t): ?>
                <div class="tujuan-card" style="border:1px solid var(--line);border-radius:14px;padding:22px;">
                    <i class="bi bi-check2-circle" style="color:var(--gold-dark);font-size:1.3rem;"></i>
                    <p style="font-size:.87rem;color:var(--ink-soft);line-height:1.7;margin-top:10px;"><?php echo esc($t['isi_tujuan']); ?></p>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-center" style="grid-column:1/-1;color:var(--ink-soft);">Data tujuan belum tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
