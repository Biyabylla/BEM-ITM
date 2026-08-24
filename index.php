<?php
$page_title = 'Home';
include 'header.php';

// Ambil profil BEM (deskripsi singkat)
$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil_bem ORDER BY id DESC LIMIT 1"));

// Ambil sambutan (Wakil Rektor III, Pembina, Presiden Mahasiswa) berdasar urutan
$sambutan_list = [];
$q = mysqli_query($koneksi, "SELECT * FROM sambutan ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($q)) $sambutan_list[] = $row;

// Ambil 3 berita terbaru
$berita_terbaru = [];
$q2 = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_publish DESC, id DESC LIMIT 3");
while ($row = mysqli_fetch_assoc($q2)) $berita_terbaru[] = $row;

// Ambil beberapa program kerja terdekat
$proker_terdekat = [];
$q3 = mysqli_query($koneksi, "SELECT pk.*, d.nama_departemen FROM program_kerja pk LEFT JOIN departemen d ON pk.departemen_id = d.id ORDER BY pk.tanggal_kegiatan ASC LIMIT 3");
while ($row = mysqli_fetch_assoc($q3)) $proker_terdekat[] = $row;
?>

<?php
$kab = kabinet_aktif();
?>
<!-- ===== HERO ===== -->
<section class="page-hero">
    <div class="container">
        <div>
            <?php if ($kab): ?>
            <div class="kabinet-chip">
                <?php if (!empty($kab['logo'])): ?>
                <img src="<?php echo esc($kab['logo']); ?>" alt="Logo Kabinet" class="kabinet-logo">
                <?php endif; ?>
                <div>
                    <span class="kabinet-name"><?php echo esc($kab['nama_kabinet']); ?></span>
                    <span class="kabinet-kicker">Periode <?php echo esc($kab['periode']); ?></span>
                </div>
            </div>
            <?php else: ?>
            <span class="eyebrow"><i class="bi bi-mortarboard"></i> Periode <?php echo SITE_PERIODE; ?></span>
            <?php endif; ?>
            <h1>Badan Eksekutif Mahasiswa<br>Institut Teknologi Mojosari</h1>
            <p class="lead"><?php echo esc($profil['deskripsi'] ?? 'Wadah aspirasi, kreativitas, dan pergerakan mahasiswa Institut Teknologi Mojosari dalam membangun kampus yang lebih maju dan bersinergi.'); ?></p>
            <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:30px;">
                <a href="tentang.php" class="btn btn-gold">Kenali Kami <i class="bi bi-arrow-right"></i></a>
                <a href="aspirasi.php" class="btn btn-outline">Sampaikan Aspirasi</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== SAMBUTAN ===== -->
<section style="background:var(--cream-soft);margin-top:-56px;position:relative;z-index:3;padding-top:52px;">
    <div class="container">
        <div class="section-head">
            <span class="section-tag">Kata Sambutan</span>
            <h2 class="section-title">Pimpinan &amp; Pembina</h2>
            <div class="divider-mini"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($sambutan_list as $s): ?>
            <div class="sambut-card reveal">
                <div class="sc-top">
                    <i class="bi bi-quote"></i>
                    <span class="badge-dept"><?php echo esc($s['jabatan']); ?></span>
                </div>
                <p class="sc-text"><?php echo esc(mb_strimwidth($s['isi_sambutan'], 0, 150, '...')); ?></p>
                <div class="sc-person">
                    <img src="<?php echo esc($s['foto'] ?: foto_default($s['nama'])); ?>" alt="<?php echo esc($s['nama']); ?>">
                    <div>
                        <b><?php echo esc($s['nama']); ?></b>
                        <span><?php echo esc($s['jabatan']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($sambutan_list)): ?>
                <p class="text-center" style="grid-column:1/-1;color:var(--ink-soft);">Data sambutan belum tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== VISI SINGKAT ===== -->
<section>
    <div class="container about-grid">
        <div class="reveal">
            <span class="section-tag">Tentang BEM ITM</span>
            <h2 class="section-title">Bergerak Bersama, Berdampak Nyata</h2>
            <p class="section-sub">BEM Institut Teknologi Mojosari merupakan lembaga eksekutif tertinggi di tingkat mahasiswa yang berperan sebagai penyalur aspirasi, penggerak kegiatan kemahasiswaan, serta mitra strategis civitas akademika dalam mewujudkan kampus yang inovatif dan berintegritas.</p>
            <a href="tentang.php" class="btn btn-primary" style="margin-top:14px;">Selengkapnya Tentang Kami <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="grid" style="grid-template-columns:1fr 1fr;gap:20px;">
            <div class="card reveal" style="padding:26px;">
                <i class="bi bi-eye" style="font-size:1.6rem;color:var(--maroon);"></i>
                <h4 style="margin:14px 0 6px;color:var(--maroon-900);">Visi</h4>
                <p style="font-size:.85rem;color:var(--ink-soft);line-height:1.7;"><?php echo esc(mb_strimwidth($profil['visi'] ?? 'Mewujudkan BEM yang aspiratif, kolaboratif, dan berintegritas.', 0, 110, '...')); ?></p>
            </div>
            <div class="card reveal" style="padding:26px;margin-top:26px;">
                <i class="bi bi-flag" style="font-size:1.6rem;color:var(--maroon);"></i>
                <h4 style="margin:14px 0 6px;color:var(--maroon-900);">Misi</h4>
                <p style="font-size:.85rem;color:var(--ink-soft);line-height:1.7;">Menjalankan program kerja yang inklusif dan berdampak bagi seluruh mahasiswa.</p>
            </div>
            <div class="card reveal" style="padding:26px;grid-column:1/-1;">
                <i class="bi bi-people" style="font-size:1.6rem;color:var(--maroon);"></i>
                <h4 style="margin:14px 0 6px;color:var(--maroon-900);">Kolaborasi</h4>
                <p style="font-size:.85rem;color:var(--ink-soft);line-height:1.7;">Bersinergi dengan seluruh elemen kampus: mahasiswa, dosen, hingga pimpinan institut.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== PROGRAM KERJA TERDEKAT ===== -->
<?php if (!empty($proker_terdekat)): ?>
<section style="background:var(--maroon-tint);">
    <div class="container">
        <div class="section-head reveal">
            <span class="section-tag">Agenda</span>
            <h2 class="section-title">Program Kerja Terdekat</h2>
            <div class="divider-mini"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($proker_terdekat as $p): ?>
            <div class="card reveal" style="padding:24px;">
                <span class="badge-dept"><?php echo esc($p['nama_departemen'] ?? 'Umum'); ?></span>
                <h4 style="margin:14px 0 8px;color:var(--maroon-900);"><?php echo esc($p['judul']); ?></h4>
                <p style="font-size:.85rem;color:var(--ink-soft);line-height:1.7;"><?php echo esc(mb_strimwidth($p['deskripsi'], 0, 100, '...')); ?></p>
                <div style="margin-top:14px;font-size:.8rem;color:var(--maroon);font-weight:600;">
                    <i class="bi bi-calendar-event"></i> <?php echo tanggal_indo($p['tanggal_kegiatan']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center" style="margin-top:34px;">
            <a href="program-kerja.php" class="btn btn-outline-maroon">Lihat Semua Program Kerja</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== BERITA TERBARU ===== -->
<?php if (!empty($berita_terbaru)): ?>
<section>
    <div class="container">
        <div class="section-head reveal">
            <span class="section-tag">Update</span>
            <h2 class="section-title">Berita &amp; Kegiatan Terbaru</h2>
            <div class="divider-mini"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($berita_terbaru as $b): ?>
            <a href="berita-detail.php?slug=<?php echo esc($b['slug']); ?>" class="card reveal">
                <div class="card-media">
                    <img src="<?php echo esc($b['gambar'] ?: 'https://placehold.co/400x220/7A1F2B/fff?text=BEM+ITM'); ?>" alt="<?php echo esc($b['judul']); ?>">
                    <span class="media-date"><i class="bi bi-calendar3"></i> <?php echo tanggal_indo($b['tanggal_publish']); ?></span>
                </div>
                <div class="card-body-pad">
                    <h4 class="card-title"><?php echo esc($b['judul']); ?></h4>
                    <span class="link-more">Baca Selengkapnya <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="text-center" style="margin-top:34px;">
            <a href="berita.php" class="btn btn-outline-maroon">Lihat Semua Berita</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CTA ASPIRASI ===== -->
<section style="background:linear-gradient(120deg,var(--maroon-900),var(--maroon));color:#fff;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(circle at 15% 20%,rgba(169,127,61,.18),transparent 40%),radial-gradient(circle at 85% 80%,rgba(169,127,61,.14),transparent 40%);"></div>
    <div class="container text-center reveal" style="position:relative;z-index:2;">
        <span class="eyebrow" style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.25);"><i class="bi bi-megaphone"></i> Suara Mahasiswa</span>
        <h2 style="color:#fff;margin:14px 0;font-size:clamp(1.4rem,3vw,2rem);">Punya Kritik atau Saran untuk Kampus?</h2>
        <p style="opacity:.9;max-width:560px;margin:0 auto 28px;">Suaramu penting bagi kami. Sampaikan aspirasimu melalui kanal resmi BEM ITM.</p>
        <a href="aspirasi.php" class="btn btn-gold">Sampaikan Sekarang <i class="bi bi-send"></i></a>
    </div>
</section>

<?php include 'footer.php'; ?>
