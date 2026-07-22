<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAFECARE — Check In/Out</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --p: #FF6B6B; --pe: #FF9E7D;
            --g: #16a34a; --gl: #dcfce7;
            --b: #2563eb; --bl: #dbeafe;
            --a: #d97706; --al: #fef3c7;
            --r: #dc2626; --rl: #fee2e2;
            --s50:#f8fafc; --s100:#f1f5f9; --s200:#e2e8f0; --s300:#cbd5e1; --s400:#94a3b8; --s600:#475569; --s800:#1e293b; --s900:#0f172a;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Inter',sans-serif;
            background:#FDF2F0;
            min-height:100dvh;display:flex;flex-direction:column;align-items:center;
            padding:0;color:var(--s800);
        }

        /* ── Header ── */
        .hdr{
            width:100%;max-width:440px;padding:20px 16px 8px;
            display:flex;align-items:center;justify-content:space-between;
        }
        .hdr .logo{
            display:flex;align-items:center;gap:10px;
        }
        .hdr .logo .sq{
            width:38px;height:38px;border-radius:12px;
            background:linear-gradient(135deg,var(--p),var(--pe));
            display:flex;align-items:center;justify-content:center;
            color:white;font-size:18px;
        }
        .hdr .logo .tx{font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;font-weight:800;color:var(--s900)}
        .hdr .logo .tx span{color:var(--p)}
        .hdr .date{font-size:11px;color:var(--s400);font-weight:600}

        /* ── Verify card ── */
        .card{
            width:100%;max-width:440px;margin:0 16px 10px;
            background:white;border-radius:18px;
            padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 12px rgba(0,0,0,.03);
            border:1px solid var(--s100);
        }
        .card-label{
            font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;
            color:var(--s400);margin-bottom:14px;display:flex;align-items:center;gap:6px;
        }
        .card-label i{color:var(--p);font-size:13px}

        .fld{margin-bottom:12px}
        .fld .lbl{font-size:10px;font-weight:700;color:var(--s600);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
        .fld .inp{
            display:flex;align-items:center;gap:10px;
            background:var(--s50);border:1.5px solid var(--s200);border-radius:12px;
            padding:12px 14px;transition:border .2s,box-shadow .2s;
        }
        .fld .inp:focus-within{border-color:var(--p);box-shadow:0 0 0 3px rgba(255,107,107,.08)}
        .fld .inp i{color:var(--s400);font-size:16px}
        .fld .inp input{
            flex:1;border:none;background:none;font-size:15px;outline:none;
            font-family:'Inter',sans-serif;color:var(--s900);font-weight:600;
        }
        .fld .inp input::placeholder{color:var(--s300);font-weight:500}

        .btn{
            width:100%;border:none;padding:13px 16px;border-radius:12px;
            font-weight:700;font-size:13px;cursor:pointer;transition:all .2s;
            display:flex;align-items:center;justify-content:center;gap:7px;
            font-family:'Inter',sans-serif;
        }
        .btn:active{transform:scale(.97)}
        .btn-pri{
            background:var(--s900);color:white;
        }
        .btn-pri:disabled{opacity:.4;cursor:not-allowed;transform:none}
        .btn-out{background:white;color:var(--b);border:1.5px solid var(--b)}
        .btn-out:disabled{opacity:.4;cursor:not-allowed}
        .btn-done{background:var(--g);color:white}
        .btn-done:disabled{opacity:.5;cursor:not-allowed}

        .ver{display:block}
        .ver.done{display:none}

        /* ── Verified strip ── */
        .strip{
            display:none;align-items:center;gap:10px;
            background:white;border-radius:14px;padding:12px 16px;margin:0 16px 10px;max-width:440px;width:calc(100% - 32px);
            box-shadow:0 1px 3px rgba(0,0,0,.04);border:1px solid var(--s100);
        }
        .strip.show{display:flex}
        .strip .av{
            width:36px;height:36px;border-radius:10px;flex-shrink:0;
            background:linear-gradient(135deg,var(--p),var(--pe));
            display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:14px;
        }
        .strip .tx{flex:1;min-width:0}
        .strip .tx strong{display:block;font-size:13px;color:var(--s900)}
        .strip .tx span{font-size:10px;color:var(--s400)}
        .strip .sw{
            font-size:10px;font-weight:700;color:var(--p);cursor:pointer;
            padding:6px 12px;border-radius:8px;background:var(--s50);border:none;font-family:'Inter',sans-serif;
        }

        /* ── Alerts ── */
        .alert{
            display:none;padding:12px 14px;border-radius:12px;font-size:12px;font-weight:600;margin-bottom:12px;
        }
        .alert.show{display:block}
        .alert-ok{background:var(--gl);color:var(--g)}
        .alert-err{background:var(--rl);color:var(--r)}

        /* ── Children card ── */
        .kids{display:none}
        .kids.show{display:block}

        .chi{
            display:flex;align-items:center;gap:12px;padding:12px;
            border-radius:14px;border:1.5px solid var(--s100);
            cursor:pointer;transition:all .2s;margin-bottom:8px;background:white;
        }
        .chi:active{transform:scale(.98)}
        .chi.sel{border-color:var(--g);background:var(--gl)}
        .chi .av{
            width:42px;height:42px;border-radius:12px;flex-shrink:0;
            background:var(--s100);display:flex;align-items:center;justify-content:center;
            color:var(--s600);font-size:16px;font-weight:800;
        }
        .chi.sel .av{background:linear-gradient(135deg,var(--p),var(--pe));color:white}
        .chi .inf{flex:1;min-width:0}
        .chi .inf .nm{font-weight:700;font-size:14px;color:var(--s900);display:flex;align-items:center;gap:6px}
        .chi .inf .sub{font-size:11px;color:var(--s400);margin-top:2px}
        .chi .ck{
            width:24px;height:24px;border-radius:50%;border:2px solid var(--s200);
            display:flex;align-items:center;justify-content:center;color:transparent;font-size:12px;transition:all .2s;
        }
        .chi.sel .ck{background:var(--g);border-color:var(--g);color:white}

        .chip{
            display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;
        }
        .chip-in{background:var(--gl);color:var(--g)}
        .chip-out{background:var(--bl);color:var(--b)}

        .btns{display:flex;gap:10px;margin-top:12px}
        .btns .btn{flex:1}

        /* ── Summary ── */
        .sum{
            margin-top:14px;padding-top:14px;border-top:1px solid var(--s100);display:none;
        }
        .sum.show{display:block}
        .sum h4{font-size:12px;font-weight:800;color:var(--s900);margin-bottom:8px;display:flex;align-items:center;gap:6px}
        .sr{
            display:flex;align-items:center;gap:10px;padding:8px 0;
            border-bottom:1px solid var(--s50);
        }
        .sr:last-child{border-bottom:none}
        .sr .sa{
            width:28px;height:28px;border-radius:8px;flex-shrink:0;
            background:var(--s100);display:flex;align-items:center;justify-content:center;
            color:var(--s400);font-size:11px;font-weight:800;
        }
        .sr .sa.done{background:var(--s200);color:var(--s400)}
        .sr .si{flex:1;min-width:0}
        .sr .si strong{display:block;font-size:12px;color:var(--s800)}
        .sr .si span{font-size:10px;color:var(--s400)}
        .sr .ss{text-align:right;flex-shrink:0}
        .sr .ss .sb{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700}
        .sr .ss .sb.in{background:var(--gl);color:var(--g)}
        .sr .ss .sb.out{background:var(--bl);color:var(--b)}
        .sr .ss .stm{display:block;font-size:10px;font-family:monospace;color:var(--s600);margin-top:2px}

        /* ── Result mini ── */
        .rm{
            display:flex;align-items:center;gap:10px;padding:10px 12px;
            border-radius:12px;font-size:12px;margin-bottom:6px;
        }
        .rm .ri{font-size:22px;flex-shrink:0}
        .rm .rd{flex:1;min-width:0}
        .rm .rd b{display:block;color:var(--s800)}
        .rm .rd u{font-size:10px;color:var(--s400);text-decoration:none}
        .rm .rs{text-align:right;flex-shrink:0}
        .rm .rs .lb{font-weight:800;font-size:11px;display:block}
        .rm .rs .ts{font-size:10px;font-family:monospace;color:var(--s600)}
        .rm .rs .hi{font-size:9px;color:var(--s400);display:block}

        .ft{
            text-align:center;padding:16px;font-size:10px;color:var(--s400);font-weight:600;
            display:flex;align-items:center;justify-content:center;gap:5px;margin-top:auto;
        }

        .sp{
            display:inline-block;width:16px;height:16px;
            border:2px solid rgba(255,255,255,.3);border-top-color:white;
            border-radius:50%;animation:spin .7s linear infinite;
        }
        @keyframes spin{to{transform:rotate(360deg)}}
        @keyframes in{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
    </style>
</head>
<body>

<div class="hdr">
    <div class="logo"><div class="sq"><i class="fas fa-shield-heart"></i></div><div class="tx">SAFE<span>CARE</span></div></div>
    <div class="date" id="today"></div>
</div>

{{-- Verify card --}}
<div class="card ver" id="vc">
    <div class="card-label"><i class="fas fa-fingerprint"></i> Sahkan Identiti</div>
    <div class="alert alert-ok" id="ok"></div>
    <div class="alert alert-err" id="er"></div>
    <div class="fld"><div class="lbl">IC Number</div><div class="inp"><i class="fas fa-id-card"></i><input type="text" id="ic" placeholder="YYMMDDXXXXXX" maxlength="12" inputmode="numeric" autocomplete="off" autofocus></div></div>
    <div class="fld"><div class="lbl">Phone</div><div class="inp"><i class="fas fa-mobile-alt"></i><input type="tel" id="ph" placeholder="0123456789" inputmode="numeric" autocomplete="tel"></div></div>
    <button class="btn btn-pri" id="vb" onclick="verify()"><i class="fas fa-arrow-right"></i> Sahkan & Cari Anak</button>
</div>

{{-- Verified strip (collapsed after verify) --}}
<div class="strip" id="strip">
    <div class="av" id="sav">P</div>
    <div class="tx"><strong id="snm">Parent</strong><span id="scn">0 anak</span></div>
    <button class="sw" onclick="switchParent()"><i class="fas fa-sync-alt"></i> Tukar</button>
</div>

{{-- Children card --}}
<div class="card kids" id="kc">
    <div class="card-label"><i class="fas fa-child"></i> Pilih Anak</div>
    <div class="alert alert-ok" id="ok2"></div>
    <div class="alert alert-err" id="er2"></div>
    <div id="cl"></div>
    <div class="btns">
        <button class="btn btn-pri" id="cib" onclick="checkin()"><i class="fas fa-sign-in-alt"></i> Check In</button>
        <button class="btn btn-out" id="cob" onclick="checkout()" style="display:none"><i class="fas fa-sign-out-alt"></i> Check Out</button>
    </div>
    <div class="sum" id="sum">
        <h4><i class="fas fa-clipboard-check"></i> Ringkasan Hari Ini</h4>
        <div id="sumBody"></div>
    </div>
</div>

<div class="ft"><i class="fas fa-shield-alt"></i> KidsTrack SAFECARE</div>

<script>
document.getElementById('today').textContent=new Date().toLocaleDateString('ms-MY',{weekday:'short',day:'numeric',month:'short'});
const csrf=document.querySelector('meta[name="csrf-token"]').content;
const $=id=>document.getElementById(id);
let pid=null,cd=[],checkedOutAll=false;

function hm(){$('er').classList.remove('show');$('ok').classList.remove('show');$('er2').classList.remove('show');$('ok2').classList.remove('show')}
function err(m){$('er2').innerHTML='<i class="fas fa-exclamation-triangle"></i> '+m;$('er2').classList.add('show');$('ok2').classList.remove('show')}
function okk(m){$('ok2').innerHTML='<i class="fas fa-check-circle"></i> '+m;$('ok2').classList.add('show');$('er2').classList.remove('show')}
function verr(m){$('er').innerHTML='<i class="fas fa-exclamation-triangle"></i> '+m;$('er').classList.add('show');$('ok').classList.remove('show')}
function vok(m){$('ok').innerHTML='<i class="fas fa-check-circle"></i> '+m;$('ok').classList.add('show');$('er').classList.remove('show')}

async function verify(){
    const ic=$('ic').value.replace(/[^0-9]/g,''),ph=$('ph').value.trim(),b=$('vb');
    if(ic.length<12){verr('IC mesti 12 digit.');return}
    if(ph.replace(/[^0-9]/g,'').length<7){verr('Sila masukkan nombor telefon.');return}
    hm();b.disabled=true;b.innerHTML='<span class="sp"></span> Mengesahkan...';
    try{
        const r=await fetch('/attendance-scan/verify-parent',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,Accept:'application/json'},body:JSON.stringify({ic,phone:ph})});
        const d=await r.json();
        if(!d.success){verr(d.message||'Gagal.');b.disabled=false;b.innerHTML='<i class="fas fa-arrow-right"></i> Sahkan & Cari Anak';return}
        pid=d.parent_id;cd=d.children||[];
        if(!cd.length){verr('Tiada anak berdaftar.');b.disabled=false;b.innerHTML='<i class="fas fa-arrow-right"></i> Sahkan & Cari Anak';return}
        // Collapse verify, show strip + children
        $('vc').classList.add('done');
        $('sav').textContent=(d.parent_name||'P').charAt(0).toUpperCase();
        $('snm').textContent=d.parent_name;$('scn').textContent=cd.length+' anak';
        $('strip').classList.add('show');
        checkedOutAll=false;
        render();$('kc').classList.add('show');
        vok('Identiti disahkan! Pilih anak di bawah.');
        b.disabled=false;b.innerHTML='<i class="fas fa-arrow-right"></i> Sahkan & Cari Anak';
    }catch(x){verr('Ralat: '+x.message);b.disabled=false;b.innerHTML='<i class="fas fa-arrow-right"></i> Sahkan & Cari Anak'}
}

function switchParent(){
    $('vc').classList.remove('done');$('strip').classList.remove('show');
    $('kc').classList.remove('show');$('sum').classList.remove('show');
    pid=null;cd=[];checkedOutAll=false;$('ic').value='';$('ph').value='';$('ic').focus();
    hm();
}

function render(){
    let h='';let anyIn=false,allOut=true;
    cd.forEach((c,i)=>{
        if(c.checked_in)anyIn=true;
        if(!c.checked_out)allOut=false;
        const bd=c.checked_out?'<span class="chip chip-out"><i class="fas fa-sign-out-alt"></i> Out</span>':c.checked_in?'<span class="chip chip-in"><i class="fas fa-check-circle"></i> In</span>':'';
        h+=`<div class="chi" onclick="t(${i})" data-i="${i}"><div class="av">${c.initial||'?'}</div><div class="inf"><div class="nm">${c.name} ${bd}</div><div class="sub"><i class="fas fa-school"></i> ${c.classroom} &bull; ${c.age}tahun</div></div><div class="ck"><i class="fas fa-check"></i></div></div>`;
    });
    $('cl').innerHTML=h;
    checkedOutAll=allOut&&cd.length>0;
    if(checkedOutAll){
        $('cib').style.display='none';$('cob').style.display='none';
    }else{
        $('cib').style.display='';$('cib').disabled=false;
        $('cib').innerHTML='<i class="fas fa-sign-in-alt"></i> Check In';$('cib').className='btn btn-pri';
        $('cob').style.display=anyIn?'':'none';$('cob').disabled=false;
    }
    updateSummary();
}

function t(i){document.querySelector('.chi[data-i="'+i+'"]')?.classList.toggle('sel')}

function updateSummary(){
    let h='';let any=false;
    cd.forEach(c=>{
        if(!c.checked_in && !c.checked_out) return;
        any=true;
        const ini=c.initial||'?';
        const ci=c.checked_in?(c.ci_time||'—') : '';
        const co=c.checked_out?(c.co_time||'—') : '';
        let stHtml='';
        if(c.checked_out&&c.checked_in){
            stHtml=`<span class="sb in"><i class="fas fa-check-circle"></i> In</span><span class="stm">${ci}</span><span class="sb out" style="margin-top:2px;"><i class="fas fa-sign-out-alt"></i> Out</span><span class="stm">${co}</span>`;
        }else if(c.checked_in){
            stHtml=`<span class="sb in"><i class="fas fa-check-circle"></i> In</span><span class="stm">${ci}</span>`;
        }
        h+=`<div class="sr"><div class="sa ${c.checked_out?'done':''}">${ini}</div><div class="si"><strong>${c.name}</strong><span>${c.classroom} &bull; ${c.age}tahun</span></div><div class="ss">${stHtml}</div></div>`;
    });
    if(!any)h='<div style="text-align:center;color:var(--s400);font-size:11px;padding:6px 0;">Tiada aktiviti hari ini</div>';
    $('sumBody').innerHTML=h;
    $('sum').classList.toggle('show',any);
}

async function checkin(){
    const sel=document.querySelectorAll('.chi.sel');if(!sel.length){err('Sila pilih anak.');return}
    const ids=[];sel.forEach(x=>ids.push(cd[x.dataset.i].id));
    const b=$('cib');b.disabled=true;b.innerHTML='<span class="sp"></span>';hm();
    try{
        const r=await fetch('/attendance-scan/bulk-checkin',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,Accept:'application/json'},body:JSON.stringify({parent_id:pid,child_ids:ids})});
        const d=await r.json();
        if(d.success){
            let h=`<div style="font-weight:800;font-size:13px;margin-bottom:6px;"><i class="fas fa-check-circle"></i> ${d.count} anak check-in!</div>`;
            if(d.results){d.results.forEach(r=>{
                const lb=r.is_late?'LATE':'ON TIME',tc=r.is_late?'var(--a)':'var(--g)',bg=r.is_late?'var(--al)':'var(--gl)';
                h+=`<div class="rm" style="background:${bg}"><div class="ri">${r.is_late?'⏰':'<i class="fas fa-check-circle"></i>'}</div><div class="rd"><b>${r.child_name}</b><u>${r.classroom}</u></div><div class="rs"><span class="lb" style="color:${tc}">${lb}</span><span class="ts">🕐 ${r.checkin_time}</span><span class="hi">⏰ Jadual: ${r.start_time}</span></div></div>`;
            });}
            $('ok2').innerHTML=h;$('ok2').classList.add('show');
            if(d.results){d.results.forEach(r=>{const ci=cd.find(x=>x.id==r.child_id);if(ci){ci.checked_in=true;ci.ci_time=r.checkin_time;}})}
            cd=d.children||cd;render();b.innerHTML='<i class="fas fa-sign-in-alt"></i> Check In';b.className='btn btn-pri';b.disabled=false;
        }else{err(d.message||'Gagal.');b.disabled=false;b.innerHTML='<i class="fas fa-sign-in-alt"></i> Check In'}
    }catch(x){err('Ralat: '+x.message);b.disabled=false;b.innerHTML='<i class="fas fa-sign-in-alt"></i> Check In'}
}

async function checkout(){
    const sel=document.querySelectorAll('.chi.sel');if(!sel.length){err('Sila pilih anak.');return}
    const ids=[];sel.forEach(x=>ids.push(cd[x.dataset.i].id));
    const b=$('cob');b.disabled=true;b.innerHTML='<span class="sp"></span>';hm();
    try{
        const r=await fetch('/attendance-scan/bulk-checkout',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,Accept:'application/json'},body:JSON.stringify({parent_id:pid,child_ids:ids})});
        const d=await r.json();
        if(d.success){
            let h=`<div style="font-weight:800;font-size:13px;margin-bottom:6px;"><i class="fas fa-upload"></i> Check-out berjaya!</div>`;
            if(d.results){d.results.forEach(r=>{
                const ea=r.is_early,bg=ea?'var(--al)':'var(--gl)',lb=ea?'EARLY PICKUP':'ON TIME',tc=ea?'var(--a)':'var(--g)';
                h+=`<div class="rm" style="background:var(--bl)"><div class="ri"><i class="fas fa-upload"></i></div><div class="rd"><b>${r.child_name}</b><u>${r.classroom} &bull; <i class="fas fa-user"></i> ${r.pickup_by||'-'}</u></div><div class="rs"><span class="lb" style="color:var(--b)">CHECKED OUT</span><span class="ts">🕐 ${r.checkout_time}</span><span class="sb ${ea?'in':''}" style="${ea?'background:'+bg+';color:'+tc+'':'background:var(--gl);color:var(--g)'}">${lb}</span><span class="hi">⏰ Until: ${r.end_time}</span></div></div>`;
            });}
            $('ok2').innerHTML=h;$('ok2').classList.add('show');
            if(d.results){d.results.forEach(r=>{const ci=cd.find(x=>x.id==r.child_id);if(ci){ci.checked_out=true;ci.co_time=r.checkout_time;}})}
            cd=d.children||cd;render();
            if(checkedOutAll){
                $('cib').style.display='none';$('cob').style.display='none';
                const done=`<div style="text-align:center;font-weight:800;color:var(--g);font-size:13px;padding:10px 0;"><i class="fas fa-check-double"></i> Semua anak selesai — terima kasih!</div>`;
                $('ok2').innerHTML+=done;
            }
        }else{err(d.message||'Gagal.');b.disabled=false;b.innerHTML='<i class="fas fa-sign-out-alt"></i> Check Out'}
    }catch(x){err('Ralat: '+x.message);b.disabled=false;b.innerHTML='<i class="fas fa-sign-out-alt"></i> Check Out'}
}
</script>
</body>
</html>
