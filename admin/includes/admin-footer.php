    </div>
</div>
<div id="toastWrap"></div>

<div id="confirmModal" class="confirm-overlay" aria-hidden="true">
    <div class="confirm-box" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <div class="confirm-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <h4 id="confirmTitle">Konfirmasi</h4>
        <p id="confirmMsg">Yakin ingin melanjutkan?</p>
        <div class="confirm-actions">
            <button type="button" class="btn btn-outline" id="confirmCancel"><i class="bi bi-x-lg"></i> Batal</button>
            <button type="button" class="btn btn-danger" id="confirmOk">Ya, keluar</button>
        </div>
    </div>
</div>

<style>
.confirm-overlay{
    display:none;position:fixed;inset:0;z-index:10000;background:rgba(30,8,12,.55);
    align-items:center;justify-content:center;padding:20px;
}
.confirm-overlay.show{display:flex;animation:fadeIn .2s ease;}
.confirm-box{
    background:var(--field);border:1px solid var(--line);border-radius:16px;width:100%;max-width:380px;
    padding:30px 26px;text-align:center;box-shadow:0 24px 60px rgba(30,8,12,.4);
    animation:modalIn .25s cubic-bezier(.4,0,.2,1);
}
.confirm-icon{
    width:56px;height:56px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;
    background:var(--maroon-tint);color:var(--maroon);font-size:1.5rem;
}
.confirm-box h4{margin:0 0 8px;color:var(--maroon-900);font-family:'Poppins',sans-serif;font-size:1.1rem;}
.confirm-box p{margin:0 0 22px;color:var(--ink-soft);font-size:.88rem;line-height:1.6;}
.confirm-actions{display:flex;gap:10px;}
.confirm-actions .btn{flex:1;justify-content:center;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
@keyframes modalIn{from{opacity:0;transform:translateY(18px) scale(.96);}to{opacity:1;transform:none;}}
</style>
<script>
function showToast(msg, type) {
    var wrap = document.getElementById('toastWrap');
    var t = document.createElement('div');
    t.className = 'toast t-' + (type || 'warning');
    var icons = {success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill'};
    t.innerHTML = '<span class="t-icon"><i class="bi ' + (icons[type || 'warning'] || icons.warning) + '"></i></span><span>' + msg + '</span><button class="t-close" onclick="this.parentElement.remove()">&times;</button>';
    wrap.appendChild(t);
    setTimeout(function(){ t.classList.add('out'); setTimeout(function(){ t.remove(); }, 300); }, 4200);
}
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('adminSidebar');
const sidebarClose = document.getElementById('sidebarClose');
const backdrop = document.getElementById('sidebarBackdrop');

const confirmModal = document.getElementById('confirmModal');
const confirmMsg = document.getElementById('confirmMsg');
const confirmOk = document.getElementById('confirmOk');
const confirmCancel = document.getElementById('confirmCancel');
let confirmHref = null;

function openConfirm(msg, href) {
    if (!confirmModal) return;
    confirmMsg.textContent = msg;
    confirmHref = href;
    confirmModal.classList.add('show');
    confirmModal.setAttribute('aria-hidden', 'false');
    confirmCancel.focus();
}
function closeConfirm() {
    if (!confirmModal) return;
    confirmModal.classList.remove('show');
    confirmModal.setAttribute('aria-hidden', 'true');
    confirmHref = null;
}
confirmOk && confirmOk.addEventListener('click', function () {
    if (confirmHref) window.location.href = confirmHref;
});
confirmCancel && confirmCancel.addEventListener('click', closeConfirm);
confirmModal && confirmModal.addEventListener('click', function (e) {
    if (e.target === confirmModal) closeConfirm();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeConfirm();
});

function confirmAction(msg, href) { openConfirm(msg, href); return false; }

function openSidebar(){ 
    sidebar.classList.add('open'); 
    backdrop.classList.add('show'); 
    menuToggle.innerHTML = '<i class="bi bi-x-lg"></i>';
    // Scroll ke item aktif
    setTimeout(function(){
        var active = sidebar.querySelector('nav a.active');
        if(active) active.scrollIntoView({behavior:'smooth', block:'center'});
    }, 350);
}
function closeSidebar(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); menuToggle.innerHTML = '<i class="bi bi-list"></i>'; }

menuToggle && menuToggle.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
sidebarClose && sidebarClose.addEventListener('click', closeSidebar);
backdrop && backdrop.addEventListener('click', closeSidebar);
sidebar && sidebar.querySelectorAll('a').forEach(a=> a.addEventListener('click', ()=> { if(window.innerWidth <= 880) closeSidebar(); }));
window.addEventListener('resize', ()=>{ if(window.innerWidth > 880) closeSidebar(); });
document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closeSidebar(); });
</script>
</body>
</html>
