<!-- Modal Detail Item Request -->
<div class="modal fade" id="requestDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rd-modal">

            <div class="rd-head">
                <div class="rd-head-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>

                <div class="rd-head-text">
                    <div class="rd-head-title" id="rdCode">-</div>
                    <div class="rd-head-sub" id="rdMeta">-</div>
                </div>

                <span class="status-badge status-pending rd-status" id="rdStatus">Pending</span>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body rd-body">
                <div class="rd-list" id="rdList"></div>
            </div>

        </div>
    </div>
</div>

<script>
(function(){
    if(window.__requestDetailModalBound) return;
    window.__requestDetailModalBound = true;

    var modal = null;

    var STATUS = {
        pending:  { label:'Pending',  icon:'fa-clock',        cls:'status-pending' },
        approved: { label:'Approved', icon:'fa-circle-check', cls:'status-approved' },
        rejected: { label:'Rejected', icon:'fa-circle-xmark', cls:'status-rejected' }
    };

    function fmt(n){
        return String(n || 0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function esc(s){
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function itemHtml(it){
        var img = it.photo
            ? '<img class="product-img" src="'+esc(it.photo)+'" alt="">'
            : '<div class="product-icon"><i class="fas fa-box-open"></i></div>';

        return '<div class="detail-item">'
                + '<div class="detail-prod">'
                + img
                + '<div>'
                + '<div class="detail-name">'+esc(it.name)+'</div>'
                + '<div class="detail-code">'+esc(it.code)+'</div>'
                + '</div>'
                + '</div>'
                + '<div class="detail-qty"><i class="fas fa-cubes me-1"></i>'+fmt(it.qty)+' pcs</div>'
            + '</div>';
    }

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.act-view');
        if(!btn) return;

        const tr = btn.closest('tr');
        if(!tr) return;

        if(!modal){
            const el = document.getElementById('requestDetailModal');
            if(!el || typeof bootstrap === 'undefined') return;
            modal = new bootstrap.Modal(el);
        }

        const d = tr.dataset;
        const st = STATUS[d.status] || STATUS.pending;

        document.getElementById('rdCode').textContent = d.code || '-';
        document.getElementById('rdMeta').textContent = d.date || '-';

        const badge = document.getElementById('rdStatus');
        badge.className = 'status-badge rd-status ' + st.cls;
        badge.innerHTML = '<i class="fas '+st.icon+'"></i>' + st.label;

        let items = [];
        try{ items = JSON.parse(d.items || '[]'); }catch(err){ items = []; }

        document.getElementById('rdList').innerHTML = items.length
            ? items.map(itemHtml).join('')
            : '<div class="rd-empty">Tidak ada item pada request ini</div>';

        modal.show();
    });
})();
</script>
