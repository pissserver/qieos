<?php 
include '../sessions/session.php'; 

$show_welcome_modal = false;
if (!isset($_SESSION['welcome_shown'])) {
    $show_welcome_modal = true;
    $_SESSION['welcome_shown'] = true;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Dashboard - Qieos</title>
    <?php include '../script/headscript.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/dashboard.css?v=<?php echo filemtime(__DIR__ . '/../css/pages/dashboard.css'); ?>">
</head>
<body>
    <?php include 'components/sidebar.php'; ?>
    <main class="content">
        <?php include 'components/navbar.php'; ?>

        <div class="dash-hdr fi">
            <div class="hdr-row">
                <div>
                    <h1>Dashboard</h1>
                    <p class="hdr-sub">Ringkasan data bisnis Anda secara real-time</p>
                    <div class="hdr-dots"><span></span><span></span><span></span></div>
                </div>
                <div class="hdr-icon"><i class="fas fa-chart-pie"></i></div>
            </div>
        </div>

        <div class="flt fi" id="fltBar">
            <div class="flt-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="fg"><span class="fl">Dari</span><input type="date" class="fi" id="fS" value="<?php echo date('Y-m-01'); ?>"></div>
            <span class="fs">—</span>
            <div class="fg"><span class="fl">Sampai</span><input type="date" class="fi" id="fE" value="<?php echo date('Y-m-d'); ?>"></div>
            <button class="fb" id="fBtn" onclick="go()"><i class="fas fa-filter"></i>Terapkan<div class="fb-load" id="fLd"></div></button>
            <button class="fb-rst" onclick="rst()"><i class="fas fa-redo"></i> Reset</button>
            <span class="flt-tag" id="fTag"></span>
        </div>

        <div id="dc"><div class="st4" id="sk1"></div><div class="g21" id="sk2"></div></div>

        <?php if ($show_welcome_modal): ?>
        <div class="w-overlay" id="wModal" onclick="if(event.target===this)closeWelcome()">
            <div class="w-modal">
                <div class="w-icon-wrap"><i class="fas fa-hat-wizard"></i></div>
                <div class="w-badge"><i class="fas fa-crown"></i> Welcome</div>
                <div class="w-title">Halo, <span><?php echo htmlspecialchars($user['fullname'] ? $user['fullname'] : $user['username']); ?></span>!</div>
                <div class="w-sub">Selamat datang kembali di sistem manajemen Qieos. Berikut ringkasan hari ini.</div>
                <div class="w-info-grid">
                    <div class="w-info-card">
                        <div class="w-info-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="w-info-lbl" style="color:#94a3b8 !important">Jam</div>
                            <div class="w-info-val" style="color:#f1f5f9 !important" id="wTime"></div>
                        </div>
                    </div>
                    <div class="w-info-card">
                        <div class="w-info-icon"><i class="fas fa-calendar-day"></i></div>
                        <div>
                            <div class="w-info-lbl" style="color:#94a3b8 !important">Hari</div>
                            <div class="w-info-val" style="color:#f1f5f9 !important" id="wDay"></div>
                        </div>
                    </div>
                    <div class="w-info-card">
                        <div class="w-info-icon"><i class="fas fa-mountain-sun"></i></div>
                        <div>
                            <div class="w-info-lbl" style="color:#94a3b8 !important">Waktu</div>
                            <div class="w-info-val" style="color:#f1f5f9 !important" id="wGreet"></div>
                        </div>
                    </div>
                    <div class="w-info-card">
                        <div class="w-info-icon"><i class="fas fa-shield-halved"></i></div>
                        <div>
                            <div class="w-info-lbl" style="color:#94a3b8 !important">Role</div>
                            <div class="w-info-val" style="color:#f1f5f9 !important"><?php echo htmlspecialchars(ucwords($user['role'])); ?></div>
                        </div>
                    </div>
                </div>
                <button class="w-btn" onclick="closeWelcome()">
                    <i class="fas fa-rocket"></i> Mulai Bekerja
                </button>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include "../script/footscript.php"; ?>
    <script>
    var CI={},CD=null;
    function fmt(v){return Math.round(v||0).toLocaleString('id-ID')}
    function rp(v){return'Rp '+fmt(v)}

    function sk(){
        var h='<div class="st4">';
        var cls=['sc-dk','sc-rd','sc-em','sc-am'];
        for(var i=0;i<4;i++) h+='<div class="sc '+cls[i]+' fi"><div class="sk" style="width:100px;height:12px"></div><div class="sk" style="width:160px;height:28px;margin-top:16px"></div><div class="sk" style="width:80px;height:10px;margin-top:10px"></div></div>';
        h+='</div><div class="g21"><div class="dc"><div class="sk" style="width:200px;height:16px;margin-bottom:18px"></div><div class="sk" style="width:100%;height:260px;border-radius:14px"></div></div><div class="dc"><div class="sk" style="width:160px;height:16px;margin-bottom:18px"></div><div class="sk" style="width:100%;height:260px;border-radius:14px"></div></div></div>';
        document.getElementById('dc').innerHTML=h;
    }

    function go(){
        var s=document.getElementById('fS').value,e=document.getElementById('fE').value;
        document.getElementById('fLd').style.display='inline-block';document.getElementById('fBtn').disabled=true;
        fetch(BASE_URL + '/pages/components/data/dashboard-data.php?start='+encodeURIComponent(s)+'&end='+encodeURIComponent(e)+'&_='+Date.now())
        .then(function(r){if(!r.ok)throw new Error(r.status);return r.text()})
        .then(function(t){
            var d;try{d=JSON.parse(t)}catch(x){
                document.getElementById('dc').innerHTML='<div style="color:var(--q-danger);padding:24px;background:var(--q-danger-bg);border-radius:16px;border:1px solid rgba(248,113,113,.2)"><b><i class="fas fa-exclamation-triangle"></i> Error</b><pre style="margin-top:10px;font-size:.8rem;white-space:pre-wrap;max-height:120px;overflow:auto">'+t.substring(0,600)+'</pre></div>';
                done();return;
            }
            done();
            document.getElementById('fTag').textContent=new Date(s).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'})+' — '+new Date(e).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
            render(d);
        }).catch(function(err){
            document.getElementById('dc').innerHTML='<div style="color:var(--q-danger);padding:24px;background:var(--q-danger-bg);border-radius:16px"><b>'+err.message+'</b></div>';done();
        });
        function done(){document.getElementById('fLd').style.display='none';document.getElementById('fBtn').disabled=false}
    }
    function rst(){
        document.getElementById('fS').value='<?php echo date("Y-01-01"); ?>';document.getElementById('fE').value='<?php echo date("Y-m-d"); ?>';
        document.getElementById('fTag').textContent='';go();
    }

    function render(d){
        CD=d;var s=d.status,st=d.stats;var h='';

        // 4 MAIN CARDS
        h+='<div class="st4">';
        h+=mcard('sc-dk','#6366f1','fa-wallet','Total Pendapatan',rp(st.pendapatan),'Hari ini: '+rp(st.today_pendapatan),'pendapatan');
        h+=mcard('sc-rd','#f43f5e','fa-shopping-cart','Total Pengeluaran',rp(st.total_pengeluaran),'Dari list pembelian barang','pengeluaran');
        h+=mcard('sc-em','#10b981','fa-chart-pie','Laba Bersih',rp(st.laba),st.laba>=0?'Untung':'Rugi — perlu review','laba');
        h+=mcard('sc-am','#f59e0b','fa-clipboard-list','Total Pesanan',fmt(st.pesanan),st.today_orders+' hari ini','pesanan');
        h+='</div>';

        // 3 SECONDARY CARDS
        h+='<div class="st3">';
        h+=scard('ic-violet','fa-store','Pembayaran Sewa Tenant',rp(st.bayar_tenant),'bayar_tenant');
        h+=scard('ic-cyan','fa-bolt','Air & Listrik',rp(st.bayar_utility),'bayar_utility');
        h+=scard('ic-indigo','fa-boxes','Total Produk',fmt(st.produk),'produk');
        h+='</div>';

        // CHARTS ROW 1
        h+='<div class="g21">';
        h+=dcc('Pendapatan vs Pengeluaran','Perbandingan 6 bulan terakhir','fa-chart-bar','c1',270,'<div class="lg"><span><i style="background:#818cf8"></i>Pendapatan</span><span><i style="background:#fb7185"></i>Pengeluaran</span></div>');
        h+='<div class="dc fi"><div class="dc-h"><div><div class="dc-t">Status Pesanan</div><div class="dc-s">Periode filter aktif</div></div><div class="dc-i"><i class="fas fa-chart-pie"></i></div></div><div id="c2" style="height:140px"></div>';
        h+='<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;margin-top:14px">';
        h+=msInd('Dibayar',s.paid,'#34d399','#10b981');
        h+=msInd('Pending',s.waiting,'#fbbf24','#f59e0b');
        h+=msInd('Dibatal',s.cancelled,'#fb7185','#f43f5e');
        h+='</div></div></div>';

        // CHARTS ROW 2 + TABLE
        h+='<div class="g11">';
        h+=dcc('Omzet 7 Hari','Pendapatan harian minggu ini','fa-chart-line','c3',220);
        h+='<div class="dc fi"><div class="dc-h"><div><div class="dc-t">Pesanan Terbaru</div><div class="dc-s">Transaksi terakhir masuk</div></div><div class="dc-i"><i class="fas fa-receipt"></i></div></div>';
        if(!d.recent_orders.length) h+='<div class="de"><i class="fas fa-receipt"></i>Belum ada pesanan</div>';
        else d.recent_orders.forEach(function(o){
            var bc=o.status_payment==='paid'?'b-p':o.status_payment==='waiting'?'b-w':'b-c';
            h+='<div class="ri"><div class="ri-c">'+o.code+'</div><div class="ri-n" style="color:var(--q-text);font-weight:600">'+(o.operator||'—')+'</div><div class="ri-a">'+rp(o.total)+'</div><div class="ri-b '+bc+'">'+o.status_payment+'</div></div>';
        });
        h+='</div></div>';

        // BOTTOM TABLES
        h+='<div class="g11">';
        h+='<div class="dc fi"><div class="dc-h"><div><div class="dc-t">Produk Terlaris</div><div class="dc-s">Top 5 berdasarkan revenue</div></div><div class="dc-i"><i class="fas fa-trophy"></i></div></div>';
        if(!d.top_products.length) h+='<div class="de"><i class="fas fa-box"></i>Belum ada data</div>';
        else{var r=1;d.top_products.forEach(function(p){
            h+='<div class="ri"><div class="rk">'+(r++)+'</div><div class="ri-n" style="font-weight:600;color:var(--q-text)">'+p.name+'</div><div class="ct">'+p.category+'</div><div class="ri-a">'+rp(p.rev)+'</div></div>';
        });}
        h+='</div>';

        h+='<div class="dc fi"><div class="dc-h"><div><div class="dc-t">List Pembelian Terakhir</div><div class="dc-s">Riwayat pengadaan barang</div></div><div class="dc-i"><i class="fas fa-clipboard-list"></i></div></div>';
        if(!d.recent_list_purchases.length) h+='<div class="de"><i class="fas fa-truck"></i>Belum ada pembelian</div>';
        else d.recent_list_purchases.forEach(function(p){
            var dt=new Date(p.date_list).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
            h+='<div class="ri"><div class="ri-c">'+dt+'</div><div class="ri-n">'+p.items+' item</div><div class="ri-a">'+rp(p.total)+'</div></div>';
        });
        h+='</div></div>';

        document.getElementById('dc').innerHTML=h;
        setTimeout(function(){
            document.querySelectorAll('.fi').forEach(function(el,i){setTimeout(function(){el.classList.add('vis')},40+i*50)});
            document.querySelectorAll('[data-w]').forEach(function(el){el.style.width=el.getAttribute('data-w')});
        },20);
        renderCharts(d);
    }

    function mcard(cls,color,icon,label,val,sub,key){
        var pct=0;if(CD&&CD.stats.pendapatan>0){
            if(key==='pendapatan')pct=100;
            else if(key==='pengeluaran')pct=Math.min(Math.round(CD.stats.total_pengeluaran/CD.stats.pendapatan*100),100);
            else if(key==='pesanan')pct=100;
            else if(key==='laba')pct=CD.stats.laba>=0?100:0;
        }
        var isNeg=key==='laba'&&CD&&CD.stats.laba<0;
        var arrow=isNeg?'fa-arrow-down':'fa-arrow-up';
        var ac=isNeg?'color:var(--q-danger)':'color:var(--q-success)';
        return'<div class="sc '+cls+' fi"><div class="sc-glow" style="background:'+color+'"></div><div class="sc-top"><div><div class="sc-lbl">'+label+'</div><div class="sc-val">'+val+'</div><div class="sc-sub"><i class="fas '+arrow+'" style="'+ac+'"></i> '+sub+'</div></div><div class="sc-ic '+cls.replace('sc-','ic-')+'"><i class="fas '+icon+'"></i></div></div><div class="sc-bar"><div class="sc-bar-fill '+cls.replace('sc-','bar-')+'" data-w="'+pct+'%"></div></div><i class="fas '+icon+' sc-deco"></i></div>';
    }

    function scard(ic,icon,label,val,key){
        var pct=0;if(CD&&CD.stats.pendapatan>0){
            if(key==='bayar_tenant')pct=Math.min(Math.round(CD.stats.bayar_tenant/CD.stats.pendapatan*100),100);
            else if(key==='bayar_utility')pct=Math.min(Math.round(CD.stats.bayar_utility/CD.stats.pendapatan*100),100);
            else if(key==='produk')pct=100;
        }
        return'<div class="scs fi"><div class="scs-top"><div><div class="scs-lbl">'+label+'</div><div class="scs-val">'+val+'</div></div><div class="scs-ic '+ic+'"><i class="fas '+icon+'"></i></div></div><div class="scs-bar"><div class="scs-bar-fill '+(ic==='ic-indigo'?'bar-indigo':ic==='ic-cyan'?'bar-cyan':'bar-violet')+'" data-w="'+pct+'%"></div></div></div>';
    }

    function dcc(title,sub,icon,id,h,extra){
        return'<div class="dc fi"><div class="dc-h"><div><div class="dc-t">'+title+'</div><div class="dc-s">'+sub+'</div></div><div class="dc-i"><i class="fas '+icon+'"></i></div></div><div id="'+id+'" style="height:'+h+'px"></div>'+(extra||'')+'</div>';
    }

    function msInd(label,val,bgColor,dotColor){
        return'<div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:var(--q-bg-raised);transition:all .25s;cursor:default" onmouseover="this.style.transform=\'translateX(4px)\'" onmouseout="this.style.transform=\'none\'"><div style="width:32px;height:32px;border-radius:10px;background:'+bgColor+';flex-shrink:0;display:flex;align-items:center;justify-content:center"><div style="width:10px;height:10px;border-radius:50%;background:'+dotColor+';box-shadow:0 0 8px '+dotColor+'"></div></div><div style="flex:1;min-width:0"><div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--q-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+label+'</div><div style="font-size:1.1rem;font-weight:800;color:var(--q-text);letter-spacing:-.03em;margin-top:1px">'+val+'</div></div></div>';
    }

    function renderCharts(d){
        if(typeof Chartist==='undefined')return;
        Object.values(CI).forEach(function(c){try{c.detach()}catch(e){}});CI={};
        var o={plugins:[Chartist.plugins.tooltip()],axisY:{offset:55,labelInterpolationFnc:function(v){return v>=1e6?(v/1e6).toFixed(1)+'jt':v>=1e3?(v/1e3).toFixed(0)+'rb':v}},axisX:{showGrid:false},chartPadding:{top:20,right:12,bottom:8,left:12}};

        setTimeout(function(){
            if(d.chart_months&&d.chart_months.length&&document.getElementById('c1')){
                CI.bar=new Chartist.Bar('#c1',{labels:d.chart_months,series:[{name:'Pendapatan',data:d.chart_pendapatan,className:'ct-series-a'},{name:'Pengeluaran',data:d.chart_pengeluaran,className:'ct-series-b'}]},Object.assign({},o,{seriesBarDistance:8}));
            }
            var pd=[d.status.paid,d.status.waiting,d.status.cancelled];
            if(pd.some(function(v){return v>0})&&document.getElementById('c2')){
                CI.pie=new Chartist.Pie('#c2',{labels:['Dibayar','Pending','Dibatal'],series:pd},{donut:true,donutWidth:40,donutSolid:true,showLabel:false,plugins:[Chartist.plugins.tooltip()]});
            }
            if(d.chart_week&&d.chart_week.length&&document.getElementById('c3')){
                CI.line=new Chartist.Line('#c3',{labels:d.chart_week.map(function(w){return w.l}),series:[{data:d.chart_week.map(function(w){return w.v})}]},Object.assign({},o,{lineSmooth:Chartist.Interpolation.cardinal({tension:.3}),fullWidth:true}));
            }
        },120);
    }

    function closeWelcome(){
        var m=document.getElementById('wModal');
        if(!m)return;
        m.style.transition='opacity .3s ease, transform .3s ease';
        m.style.opacity='0';
        m.querySelector('.w-modal').style.transform='scale(.9) translateY(20px)';
        setTimeout(function(){m.remove()},300);
    }

    function initWelcomeClock(){
        var now=new Date();
        var h=now.getHours();
        var greet='Selamat Malam';
        if(h>=4&&h<11) greet='Selamat Pagi';
        else if(h>=11&&h<15) greet='Selamat Siang';
        else if(h>=15&&h<18) greet='Selamat Sore';

        var timeEl=document.getElementById('wTime');
        var dayEl=document.getElementById('wDay');
        var greetEl=document.getElementById('wGreet');

        if(timeEl) timeEl.textContent=now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})+' WIB';
        if(dayEl) dayEl.textContent=now.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'short'});
        if(greetEl) greetEl.textContent=greet;
    }

    document.addEventListener('DOMContentLoaded',function(){sk();go();initWelcomeClock()});
    </script>
</body>
</html>
