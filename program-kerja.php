<?php
$page_title = 'Program Kerja';
include 'header.php';

// Pilih kabinet (default: aktif) — kontrol pemilih kabinet
$kabinet_list = daftar_kabinet();
$kab_aktif = kabinet_aktif();
$kabinet_id = isset($_GET['kabinet']) ? (int)$_GET['kabinet'] : (int)($kab_aktif['id'] ?? 0);
$kab_selected = null;
foreach ($kabinet_list as $kk) if ((int)$kk['id'] === (int)$kabinet_id) { $kab_selected = $kk; break; }
if (!$kab_selected && $kabinet_list) $kab_selected = $kabinet_list[0];
if ($kab_selected) $kabinet_id = (int)$kab_selected['id'];
$kab_where = $kab_selected ? " AND pk.kabinet_id=" . (int)$kab_selected['id'] : '';

$departemen = [];
$qd = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($qd)) $departemen[] = $row;

$proker = [];
$q = mysqli_query($koneksi, "SELECT pk.*, d.nama_departemen FROM program_kerja pk LEFT JOIN departemen d ON pk.departemen_id = d.id WHERE 1 $kab_where ORDER BY pk.tanggal_kegiatan ASC");
while ($row = mysqli_fetch_assoc($q)) $proker[] = $row;

$status_label = [
    'akan datang' => ['Akan Datang', 'badge-gold'],
    'berlangsung' => ['Berlangsung', 'badge-dept'],
    'selesai'     => ['Selesai', 'badge-dept'],
];
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span>Program Kerja</div>
        <span class="eyebrow"><i class="bi bi-clipboard2-check"></i> Agenda Kegiatan</span>
        <h1>Program Kerja BEM ITM</h1>
        <p class="lead">Rangkaian program kerja dari setiap departemen BEM Institut Teknologi Mojosari<?php echo $kab_selected ? ' — ' . esc($kab_selected['nama_kabinet']) . ' (' . esc($kab_selected['periode']) . ')' : ''; ?>.</p>

        <?php if (count($kabinet_list) > 0): ?>
        <div class="kabinet-select">
            <?php foreach ($kabinet_list as $kk): $act = ((int)$kk['id'] === (int)$kabinet_id); ?>
            <a href="program-kerja.php?kabinet=<?php echo (int)$kk['id']; ?>"
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
    </div>
</section>

<section>
    <div class="container">
        <div class="filter-tabs" id="prokerTabs">
            <button class="active" data-filter="all">Semua Departemen</button>
            <?php foreach ($departemen as $d): ?>
            <button data-filter="dept-<?php echo $d['id']; ?>"><?php echo esc($d['nama_departemen']); ?></button>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-3" id="prokerGrid">
            <?php foreach ($proker as $p):
                $st = $status_label[$p['status']] ?? ['-', 'badge-dept'];
            ?>
            <div class="card fade-item" data-dept="<?php echo $p['departemen_id'] ? 'dept-' . (int)$p['departemen_id'] : 'dept-0'; ?>">
                <div class="card-media">
                    <img src="<?php echo esc($p['gambar'] ?: 'https://placehold.co/400x220/7A1F2B/fff?text=Program+Kerja'); ?>" alt="<?php echo esc($p['judul']); ?>">
                </div>
                <div class="card-body-pad">
                    <div class="card-meta">
                        <span class="badge-dept"><?php echo esc($p['nama_departemen'] ?? 'Umum'); ?></span>
                        <span class="badge-dept <?php echo $st[1]; ?>"><?php echo $st[0]; ?></span>
                    </div>
                    <h4 class="card-title"><?php echo esc($p['judul']); ?></h4>
                    <p class="card-text"><?php echo esc($p['deskripsi']); ?></p>
                    <div class="card-foot"><i class="bi bi-calendar-event"></i> <?php echo tanggal_indo($p['tanggal_kegiatan']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($proker)): ?>
                <p class="text-center" style="grid-column:1/-1;color:var(--ink-soft);">Belum ada program kerja yang tercatat.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
