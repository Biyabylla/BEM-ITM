<footer class="footer">
    <div class="container">
        <div class="fgrid">
            <div>
                <div class="flogo">
                    <img src="<?php echo esc(setting('logo_bem', 'assets/img/logobem.png')); ?>" alt="Logo BEM ITM" class="flogo-img">
                    <div>
                        <b style="color:#fff;display:block;font-family:'Poppins',sans-serif;"><?php echo esc(SITE_SHORT); ?></b>
                        <span style="font-size:.75rem;"><?php echo esc(SITE_NAME); ?></span>
                    </div>
                </div>
                <p>Badan Eksekutif Mahasiswa <?php echo esc(SITE_NAME); ?> — mewadahi aspirasi, mengembangkan potensi, dan membangun sinergi mahasiswa periode <?php echo esc(SITE_PERIODE); ?>.</p>
                <?php $kab_footer = kabinet_aktif(); if ($kab_footer): ?>
                <div style="margin-top:2px;">
                    <span class="badge-dept" style="background:var(--gold);color:var(--maroon-900);font-weight:700;margin-bottom:4px;display:inline-flex;align-items:center;gap:8px;">
                        <?php if (!empty($kab_footer['logo'])): ?>
                        <img src="<?php echo esc($kab_footer['logo']); ?>" alt="Logo Kabinet" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">
                        <?php endif; ?>
                        <?php echo esc($kab_footer['nama_kabinet']); ?>
                    </span><br>
                    <span style="font-size:.8rem;">Masa bakti <?php echo esc($kab_footer['periode']); ?></span>
                </div>
                <?php endif; ?>
                <div class="socials">
                    <a href="https://www.instagram.com/bem_itmnganjuk/" target="_blank" rel="noopener" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.youtube.com/@BEMITMNganjuk" target="_blank" rel="noopener" title="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="https://www.tiktok.com/@bemitmnganjuk" target="_blank" rel="noopener" title="TikTok"><i class="bi bi-tiktok"></i></a>
                    <a href="mailto:bemitmnganjuk@gmail.com" title="Email"><i class="bi bi-envelope"></i></a>
                </div>
            </div>
            <div>
                <h5>Navigasi</h5>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="tentang.php">Tentang Kami</a></li>
                    <li><a href="struktur.php">Struktur Organisasi</a></li>
                    <li><a href="program-kerja.php">Program Kerja</a></li>
                    <li><a href="berita.php">Berita</a></li>
                </ul>
            </div>
            <div>
                <h5>Layanan</h5>
                <ul>
                    <li><a href="pendaftaran.php">Pendaftaran Pengurus</a></li>
                    <li><a href="aspirasi.php">Kritik &amp; Saran</a></li>
                    <li><a href="berita.php">Info Kegiatan</a></li>
                    <li><a href="struktur.php">Kepengurusan</a></li>
                    <li><a href="admin/login.php">Login Admin</a></li>
                </ul>
            </div>
            <div>
                <h5>Kontak &amp; Media Sosial</h5>
                <ul>
                    <li><i class="bi bi-geo-alt"></i> <a href="https://share.google/LQ0A1wAHZm3ee1ce9" target="_blank" rel="noopener">Mojosari, Ngepeh, Kec. Loceret, Kabupaten Nganjuk, Jawa Timur 64471</a></li>
                    <li><i class="bi bi-envelope"></i> <a href="mailto:bemitmnganjuk@gmail.com">bemitmnganjuk@gmail.com</a></li>
                    <li><i class="bi bi-instagram"></i> <a href="https://www.instagram.com/bem_itmnganjuk/" target="_blank" rel="noopener">@bem_itmnganjuk</a></li>
                    <li><i class="bi bi-youtube"></i> <a href="https://www.youtube.com/@BEMITMNganjuk" target="_blank" rel="noopener">BEM ITM Nganjuk</a></li>
                    <li><i class="bi bi-tiktok"></i> <a href="https://www.tiktok.com/@bemitmnganjuk" target="_blank" rel="noopener">@bemitmnganjuk</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> <?php echo esc(SITE_NAME); ?>. Seluruh hak cipta dilindungi.</span>
            <!-- <span>Dibangun dengan <i class="bi bi-heart-fill" style="color:var(--gold);"></i> untuk mahasiswa ITM</span> -->
        </div>
    </div>
</footer>

<script>
/* ===== NAVBAR SCROLL EFFECT ===== */
const navbar = document.getElementById('mainNavbar');
window.addEventListener('scroll', () => {
    if (window.scrollY > 30) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
});

/* ===== SCROLL TO TOP ===== */
const toTop = document.createElement('button');
toTop.className = 'to-top';
toTop.setAttribute('aria-label', 'Kembali ke atas');
toTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
document.body.appendChild(toTop);
window.addEventListener('scroll', () => {
    toTop.classList.toggle('show', window.scrollY > 420);
});
toTop.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));

/* ===== MOBILE MENU TOGGLE ===== */
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');
const navOverlay = document.getElementById('navOverlay');
const navClose = document.getElementById('navClose');

function closeMenu(){
    navMenu.classList.remove('open');
    navOverlay.classList.remove('open');
    navToggle.innerHTML = '<i class="bi bi-list"></i>';
}
function openMenu(){
    navMenu.classList.add('open');
    navOverlay.classList.add('open');
    navToggle.innerHTML = '<i class="bi bi-x-lg"></i>';
    var active = navMenu.querySelector('a.active');
    if(active) active.scrollIntoView({behavior:'smooth',block:'center'});
}
function toggleMenu(){
    if(navMenu.classList.contains('open')) closeMenu(); else openMenu();
}
navToggle && navToggle.addEventListener('click', toggleMenu);
navOverlay && navOverlay.addEventListener('click', closeMenu);
navClose && navClose.addEventListener('click', closeMenu);
document.querySelectorAll('#navMenu a').forEach(a => a.addEventListener('click', ()=> { if(window.innerWidth <= 991) closeMenu(); }));

/* ===== SCROLL REVEAL SEDERHANA ===== */
const revealItems = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && revealItems.length){
    const io = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{
            if(e.isIntersecting){
                e.target.style.opacity = 1;
                e.target.style.transform = 'translateY(0)';
                io.unobserve(e.target);
            }
        });
    }, {threshold:.12});
    revealItems.forEach(el=>{
        el.style.opacity = 0;
        el.style.transform = 'translateY(24px)';
        el.style.transition = '.6s cubic-bezier(.4,0,.2,1)';
        io.observe(el);
    });
}

/* ===== FILTER TAB GENERIK (dipakai di struktur.php & program-kerja.php) ===== */
function initFilterTabs(tabSelector, itemSelector, dataAttr){
    const tabs = document.querySelectorAll(tabSelector);
    const items = document.querySelectorAll(itemSelector);
    function apply(tab, silent){
        tabs.forEach(t=>t.classList.remove('active'));
        tab.classList.add('active');
        const filter = tab.getAttribute('data-filter');
        items.forEach(item=>{
            const match = (filter === 'all' || item.getAttribute(dataAttr) === filter);
            item.style.display = match ? '' : 'none';
            if(match && !silent){ item.classList.remove('fade-item'); void item.offsetWidth; item.classList.add('fade-item'); }
        });
    }
    tabs.forEach(tab=> tab.addEventListener('click', ()=> apply(tab, false)));
    const activeTab = document.querySelector(tabSelector + '.active') || tabs[0];
    if(activeTab) apply(activeTab, true);
}
</script>
</body>
</html>
