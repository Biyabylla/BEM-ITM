<?php
$page_title = 'Struktur Organisasi';
include 'header.php';

// Pilih kabinet (default: aktif)
$kabinet_list = daftar_kabinet();
$kab_aktif = kabinet_aktif();
$kabinet_id = isset($_GET['kabinet']) ? (int)$_GET['kabinet'] : (int)($kab_aktif['id'] ?? 0);
$kab_selected = null;
foreach ($kabinet_list as $kk) if ((int)$kk['id'] === (int)$kabinet_id) { $kab_selected = $kk; break; }
if (!$kab_selected && $kabinet_list) $kab_selected = $kabinet_list[0];
if ($kab_selected) $kabinet_id = (int)$kab_selected['id'];
$kab_where = $kab_selected ? " AND kabinet_id=" . (int)$kab_selected['id'] : '';

// Pembina (tingkat tertinggi - dosen, tanpa prodi)
$pembina = [];
$q = mysqli_query($koneksi, "SELECT * FROM pengurus WHERE kategori='pembina' $kab_where ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($q)) $pembina[] = $row;

// BPH: Pimpinan BPH (Presiden & Wakil Presiden) - ditaruh paling atas ranah BPH
$pimpinan = [];
$q2 = mysqli_query($koneksi, "SELECT * FROM pengurus WHERE kategori='pimpinan' $kab_where ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($q2)) $pimpinan[] = $row;

// BPH: Anggota BPH (Sekretaris, Bendahara beserta jajarannya)
$anggota_bph = [];
$q3 = mysqli_query($koneksi, "SELECT * FROM pengurus WHERE kategori='bph' $kab_where ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($q3)) $anggota_bph[] = $row;

// Pisahkan Sekretaris & Bendahara (kepala BPH) dan jajarannya (staf)
$bph_kunci = [];
$bph_staf = [];
foreach ($anggota_bph as $p) {
    $is_kunci = (stripos($p['jabatan'], 'Sekretaris') !== false && stripos($p['jabatan'], 'Wakil') === false)
             || (stripos($p['jabatan'], 'Bendahara') !== false && stripos($p['jabatan'], 'Wakil') === false);
    if ($is_kunci) $bph_kunci[] = $p; else $bph_staf[] = $p;
}

// Daftar departemen (dengan ketua departemen ditaruh paling atas di ranahnya)
$departemen = [];
$qd = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($qd)) $departemen[] = $row;

// Pengurus departemen (ketua per departemen selalu pertama)
$anggota_dept = [];
$qa = mysqli_query($koneksi, "SELECT p.*, d.nama_departemen, d.deskripsi AS dept_desc, d.icon AS dept_icon FROM pengurus p LEFT JOIN departemen d ON p.departemen_id = d.id WHERE p.kategori='departemen' $kab_where ORDER BY d.id ASC, (p.jabatan LIKE 'Ketua%') DESC, p.urutan ASC");
while ($row = mysqli_fetch_assoc($qa)) $anggota_dept[] = $row;

// Kelompokkan anggota per departemen (ketua paling awal)
$dept_members = [];
foreach ($anggota_dept as $a) {
    $dept_members[$a['departemen_id']][] = $a;
}
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span>Struktur Organisasi</div>

        <?php if (count($kabinet_list) > 0): ?>
        <div class="kabinet-select" style="margin-top:14px;">
            <?php foreach ($kabinet_list as $kk): $act = ((int)$kk['id'] === (int)$kabinet_id); ?>
            <a href="struktur.php?kabinet=<?php echo (int)$kk['id']; ?>"
               class="kabinet-opt <?php echo $act ? 'active' : ''; ?>"
               title="<?php echo esc($kk['periode']); ?>">
                <?php if (!empty($kk['logo'])): ?><img src="<?php echo esc($kk['logo']); ?>" alt="" class="kabinet-opt-logo"><?php endif; ?>
                <span>
                    <b><?php echo esc($kk['nama_kabinet']); ?> <?php echo $kk['is_aktif'] ? '<i class="bi bi-check-circle-fill" style="color:var(--gold);font-size:.8rem;"></i>' : ''; ?></b>
                    <em><?php echo esc($kk['periode']); ?></em>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h1>Struktur Organisasi BEM ITM</h1>
        <p class="lead">Susunan pengurus Badan Eksekutif Mahasiswa Institut Teknologi Mojosari <?php echo $kab_selected ? 'untuk Kabinet <b>'.esc($kab_selected['nama_kabinet']).'</b> periode '.esc($kab_selected['periode']) : 'periode '.SITE_PERIODE; ?>.</p>
    </div>
</section>

<?php if (empty($pembina) && empty($pimpinan) && empty($anggota_bph) && empty($anggota_dept)): ?>
<section>
    <div class="container">
        <div class="kabinet-empty reveal" style="text-align:center;">
            <i class="bi bi-inbox" style="font-size:2.4rem;color:var(--gold-dark);"></i>
            <p style="margin:12px 0 0;">Data kepengurusan <?php echo $kab_selected ? esc($kab_selected['nama_kabinet']) : ''; ?> belum tersedia.</p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== PEMBINA ===== -->
<?php if (!empty($pembina)): ?>
<section>
    <div class="container">
        <div class="section-head reveal" style="text-align:center;">
            <span class="section-tag">Pembina</span>
            <h2 class="section-title">Pembina BEM ITM 2026</h2>
            <div class="divider-mini" style="margin:0 auto;"></div>
        </div>
        <div class="grid grid-2" style="max-width:760px;margin:0 auto;">
            <?php foreach ($pembina as $p): ?>
            <div class="card leader-card reveal">
                <div class="leader-band"><i class="bi bi-award-fill"></i> <?php echo esc($p['jabatan']); ?></div>
                <div class="card-body">
                    <img src="<?php echo esc($p['foto'] ?: foto_default($p['nama'])); ?>" class="avatar" alt="<?php echo esc($p['nama']); ?>">
                    <h4><?php echo esc($p['nama']); ?></h4>
                    <?php if (!empty($p['gmail']) || (!empty($p['instagram']) && $p['instagram'] !== '#')): ?>
                    <div class="contact-links">
                        <?php if (!empty($p['gmail'])): ?><a href="mailto:<?php echo esc($p['gmail']); ?>" class="cl-gmail" title="Kirim email"><i class="bi bi-envelope-fill"></i> <?php echo esc($p['gmail']); ?></a><?php endif; ?>
                        <?php if (!empty($p['instagram']) && $p['instagram'] !== '#'): ?><a href="<?php echo esc($p['instagram']); ?>" target="_blank" rel="noopener" class="cl-ig" title="Lihat Instagram"><i class="bi bi-instagram"></i> <?php echo esc(username_ig($p['instagram'])); ?></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== BPH (Pimpinan + Sekretaris/Bendahara + Staf) ===== -->
<?php if (!empty($pimpinan) || !empty($bph_kunci) || !empty($bph_staf)): ?>
<section style="background:var(--maroon-tint);">
    <div class="container">
        <div class="section-head reveal" style="text-align:center;">
            <span class="section-tag"><i class="bi bi-diagram-3"></i> BPH</span>
            <h2 class="section-title">Badan Pengurus Harian</h2>
            <div class="divider-mini" style="margin:0 auto;"></div>
        </div>

        <?php if (!empty($pimpinan)): ?>
        <div class="group-heading reveal">
            <span class="gh-marker"><i class="bi bi-stars"></i></span>
            <div>
                <h3>Pimpinan BPH</h3>
                <span>Presiden &amp; Wakil Presiden Mahasiswa</span>
            </div>
        </div>
        <div class="grid grid-2" style="max-width:760px;margin:0 auto 48px;">
            <?php foreach ($pimpinan as $p): ?>
            <div class="card leader-card reveal">
                <div class="leader-band"><i class="bi bi-star-fill"></i> <?php echo esc($p['jabatan']); ?></div>
                <div class="card-body">
                    <img src="<?php echo esc($p['foto'] ?: foto_default($p['nama'])); ?>" class="avatar" alt="<?php echo esc($p['nama']); ?>">
                    <h4><?php echo esc($p['nama']); ?></h4>
                    <div class="prodi"><i class="bi bi-mortarboard"></i> <?php echo esc($p['program_studi'] ?: '-'); ?></div>
                    <?php if (!empty($p['gmail']) || (!empty($p['instagram']) && $p['instagram'] !== '#')): ?>
                    <div class="contact-links">
                        <?php if (!empty($p['gmail'])): ?><a href="mailto:<?php echo esc($p['gmail']); ?>" class="cl-gmail" title="Kirim email"><i class="bi bi-envelope-fill"></i> <?php echo esc($p['gmail']); ?></a><?php endif; ?>
                        <?php if (!empty($p['instagram']) && $p['instagram'] !== '#'): ?><a href="<?php echo esc($p['instagram']); ?>" target="_blank" rel="noopener" class="cl-ig" title="Lihat Instagram"><i class="bi bi-instagram"></i> <?php echo esc(username_ig($p['instagram'])); ?></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($bph_kunci) || !empty($bph_staf)): ?>
        <div class="group-heading reveal">
            <span class="gh-marker"><i class="bi bi-people"></i></span>
            <div>
                <h3>Staf BPH</h3>
                <span>Badan Pengurus Harian</span>
            </div>
        </div>

        <?php if (!empty($bph_kunci)): ?>
        <div style="display:flex;justify-content:center;gap:22px;flex-wrap:wrap;margin:0 auto 40px;max-width:880px;">
            <?php foreach ($bph_kunci as $p): ?>
            <div class="card leader-card small fade-item reveal" style="width:100%;max-width:420px;">
                <div class="leader-band"><i class="bi bi-award-fill"></i> <?php echo esc($p['jabatan']); ?></div>
                <div class="card-body">
                    <img src="<?php echo esc($p['foto'] ?: foto_default($p['nama'])); ?>" class="avatar" alt="<?php echo esc($p['nama']); ?>">
                    <h4><?php echo esc($p['nama']); ?></h4>
                    <div class="prodi"><i class="bi bi-mortarboard"></i> <?php echo esc($p['program_studi'] ?: '-'); ?></div>
                    <?php if (!empty($p['gmail']) || (!empty($p['instagram']) && $p['instagram'] !== '#')): ?>
                    <div class="contact-links">
                        <?php if (!empty($p['gmail'])): ?><a href="mailto:<?php echo esc($p['gmail']); ?>" class="cl-gmail" title="Kirim email"><i class="bi bi-envelope-fill"></i> <?php echo esc($p['gmail']); ?></a><?php endif; ?>
                        <?php if (!empty($p['instagram']) && $p['instagram'] !== '#'): ?><a href="<?php echo esc($p['instagram']); ?>" target="_blank" rel="noopener" class="cl-ig" title="Lihat Instagram"><i class="bi bi-instagram"></i> <?php echo esc(username_ig($p['instagram'])); ?></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($bph_staf)): ?>
        <div class="grid grid-4" style="max-width:1100px;margin:0 auto;">
            <?php foreach ($bph_staf as $p): ?>
            <div class="card member-card fade-item reveal">
                <img src="<?php echo esc($p['foto'] ?: foto_default($p['nama'])); ?>" class="avatar" alt="<?php echo esc($p['nama']); ?>">
                <h4><?php echo esc($p['nama']); ?></h4>
                <div class="role"><?php echo esc($p['jabatan']); ?></div>
                <div class="prodi"><i class="bi bi-mortarboard"></i> <?php echo esc($p['program_studi'] ?: '-'); ?></div>
                <?php if (!empty($p['gmail']) || (!empty($p['instagram']) && $p['instagram'] !== '#')): ?>
                <div class="contact-links">
                    <?php if (!empty($p['gmail'])): ?><a href="mailto:<?php echo esc($p['gmail']); ?>" class="cl-gmail" title="Kirim email"><i class="bi bi-envelope-fill"></i> <?php echo esc($p['gmail']); ?></a><?php endif; ?>
                    <?php if (!empty($p['instagram']) && $p['instagram'] !== '#'): ?><a href="<?php echo esc($p['instagram']); ?>" target="_blank" rel="noopener" class="cl-ig" title="Lihat Instagram"><i class="bi bi-instagram"></i> <?php echo esc(username_ig($p['instagram'])); ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ===== DEPARTEMEN (per departemen, ketua paling atas) ===== -->
<section>
    <div class="container">
        <div class="section-head reveal" style="text-align:center;">
            <span class="section-tag">Departemen</span>
            <h2 class="section-title">Anggota per Departemen</h2>
            <div class="divider-mini" style="margin:0 auto;"></div>
        </div>

        <div class="filter-tabs" id="deptTabs" style="display:flex;justify-content:center;flex-wrap:wrap;gap:10px;margin-bottom:36px;">
            <?php $dept_first = true; foreach ($departemen as $d): ?>
            <button class="<?php echo $dept_first ? 'active' : ''; ?>" data-filter="dept-<?php echo $d['id']; ?>"><?php echo esc($d['nama_departemen']); ?></button>
            <?php $dept_first = false; endforeach; ?>
        </div>

        <div id="deptContainer">
            <?php foreach ($departemen as $d): $members = $dept_members[$d['id']] ?? []; if (empty($members)) continue; ?>
            <div class="dept-block fade-item reveal" data-dept="dept-<?php echo $d['id']; ?>">
                <div class="dept-head" style="display:flex;align-items:center;justify-content:center;gap:16px;text-align:center;border-bottom:1px solid var(--line);margin-bottom:24px;padding-bottom:16px;">
                    <span class="d-icon"><i class="bi <?php echo esc($d['icon'] ?: 'bi-people'); ?>"></i></span>
                    <div>
                        <h3><?php echo esc($d['nama_departemen']); ?></h3>
                        <span><?php echo esc(mb_strimwidth($d['deskripsi'] ?? '', 0, 72, '...')); ?></span>
                    </div>
                    <span class="dept-count"><i class="bi bi-people"></i> <?php echo count($members); ?> Pengurus</span>
                </div>

                <?php $ketua = null; foreach ($members as $i => $m) { if (strpos($m['jabatan'], 'Ketua') === 0) { $ketua = $m; array_splice($members, $i, 1); break; } } ?>
                <?php if ($ketua): ?>
                <div style="display:flex;justify-content:center;margin:0 auto 28px;">
                    <div class="card leader-card reveal" style="width:100%;max-width:420px;">
                        <div class="leader-band"><i class="bi bi-award-fill"></i> Ketua Departemen</div>
                        <div class="card-body">
                            <img src="<?php echo esc($ketua['foto'] ?: foto_default($ketua['nama'])); ?>" class="avatar" alt="<?php echo esc($ketua['nama']); ?>">
                            <h4><?php echo esc($ketua['nama']); ?></h4>
                            <div class="prodi"><i class="bi bi-mortarboard"></i> <?php echo esc($ketua['program_studi'] ?: '-'); ?></div>
                            <?php if (!empty($ketua['gmail']) || (!empty($ketua['instagram']) && $ketua['instagram'] !== '#')): ?>
                            <div class="contact-links">
                                <?php if (!empty($ketua['gmail'])): ?><a href="mailto:<?php echo esc($ketua['gmail']); ?>" class="cl-gmail" title="Kirim email"><i class="bi bi-envelope-fill"></i> <?php echo esc($ketua['gmail']); ?></a><?php endif; ?>
                                <?php if (!empty($ketua['instagram']) && $ketua['instagram'] !== '#'): ?><a href="<?php echo esc($ketua['instagram']); ?>" target="_blank" rel="noopener" class="cl-ig" title="Lihat Instagram"><i class="bi bi-instagram"></i> <?php echo esc(username_ig($ketua['instagram'])); ?></a><?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($members)): ?>
                <div class="grid grid-4" style="max-width:1100px;margin:0 auto;">
                    <?php foreach ($members as $a): ?>
                    <div class="card member-card fade-item">
                        <img src="<?php echo esc($a['foto'] ?: foto_default($a['nama'])); ?>" class="avatar" alt="<?php echo esc($a['nama']); ?>">
                        <h4><?php echo esc($a['nama']); ?></h4>
                        <div class="role"><?php echo esc($a['jabatan']); ?></div>
                        <div class="prodi"><i class="bi bi-mortarboard"></i> <?php echo esc($a['program_studi'] ?: '-'); ?></div>
                        <?php if (!empty($a['gmail']) || (!empty($a['instagram']) && $a['instagram'] !== '#')): ?>
                        <div class="contact-links">
                            <?php if (!empty($a['gmail'])): ?><a href="mailto:<?php echo esc($a['gmail']); ?>" class="cl-gmail" title="Kirim email"><i class="bi bi-envelope-fill"></i> <?php echo esc($a['gmail']); ?></a><?php endif; ?>
                            <?php if (!empty($a['instagram']) && $a['instagram'] !== '#'): ?><a href="<?php echo esc($a['instagram']); ?>" target="_blank" rel="noopener" class="cl-ig" title="Lihat Instagram"><i class="bi bi-instagram"></i> <?php echo esc(username_ig($a['instagram'])); ?></a><?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php if (empty($anggota_dept)): ?>
                <p class="text-center" style="color:var(--ink-soft);padding:30px 0;text-align:center;">Data anggota departemen belum tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>


<?php include 'footer.php'; ?>
<script>
initFilterTabs('#deptTabs button', '#deptContainer .dept-block', 'data-dept');
</script>
