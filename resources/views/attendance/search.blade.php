<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KidsTrack — Check In/Out</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#FF6B6B,#FF9E7D);min-height:100vh;padding:30px 15px;}
        .container{max-width:440px;margin:0 auto;}
        .logo{text-align:center;color:white;margin-bottom:24px;}
        .logo h1{font-size:28px;font-weight:800;}
        .logo p{opacity:.85;margin-top:4px;font-size:14px;}
        .card{background:white;border-radius:24px;padding:24px;box-shadow:0 20px 40px rgba(0,0,0,.15);margin-bottom:14px;}
        .step-label{font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:14px;display:flex;align-items:center;gap:6px;}
        .input-group{margin-bottom:14px;}
        .input-group label{display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:5px;}
        .input-box{display:flex;align-items:center;background:#f8fafc;border:2px solid #e2e8f0;border-radius:14px;padding:12px 14px;gap:10px;transition:border .2s;}
        .input-box:focus-within{border-color:#FF6B6B;}
        .input-box input{flex:1;border:none;background:none;font-size:16px;outline:none;font-family:'Inter',sans-serif;color:#1e293b;}
        .btn{width:100%;border:none;padding:14px;border-radius:14px;font-weight:800;font-size:15px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
        .btn-primary{background:linear-gradient(135deg,#FF6B6B,#FF9E7D);color:white;box-shadow:0 4px 15px rgba(255,107,107,.3);}
        .btn-primary:hover{transform:translateY(-1px);}
        .btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none;}
        .error-msg{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:12px;padding:10px 14px;font-size:13px;font-weight:600;text-align:center;display:none;margin-top:10px;}
        .error-msg.show{display:block;}
        .success-msg{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:12px;padding:10px 14px;font-size:13px;font-weight:600;text-align:center;display:none;margin-bottom:14px;}
        .success-msg.show{display:block;}
        .children-section{display:none;}
        .children-section.show{display:block;}
        .parent-info{background:#f0f9ff;border-radius:12px;padding:12px;margin-bottom:14px;text-align:center;font-weight:700;color:#0369a1;font-size:14px;}
        .child-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:14px;border:2px solid #f1f5f9;cursor:pointer;transition:all .2s;margin-bottom:8px;}
        .child-item:hover{border-color:#3b82f6;background:#eff6ff;}
        .child-item.selected{border-color:#16a34a;background:#f0fdf4;}
        .child-avatar{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#FF6B6B,#FF9E7D);display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:800;flex-shrink:0;}
        .child-info{flex:1;}
        .child-name{font-weight:800;color:#1e293b;font-size:15px;}
        .child-meta{font-size:11px;color:#94a3b8;}
        .check-icon{color:#16a34a;font-size:20px;display:none;}
        .child-item.selected .check-icon{display:block;}
        .status-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .status-checkin{background:#dcfce7;color:#16a34a;}
        .footer{text-align:center;color:white;font-size:12px;opacity:.8;padding:10px;}
        .spinner{display:inline-block;width:16px;height:16px;border:2px solid #f3f3f3;border-top:2px solid #FF6B6B;border-radius:50%;animation:spin .8s linear infinite;}
        @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
    </style>
</head>
<body>
<div class="container">
    <div class="logo"><h1><i class="fas fa-school"></i> KidsTrack</h1><p>Check In / Check Out — Sahkan Identiti</p></div>
    <div class="card" id="verifyCard">
        <div class="step-label"><i class="fas fa-id-card"></i> Sahkan Identiti Ibu Bapa</div>
        <div class="success-msg" id="successMsg"></div>
        <div class="error-msg" id="errorMsg"></div>
        <div class="input-group"><label><i class="fas fa-id-card"></i> IC Number</label><div class="input-box"><i class="fas fa-id-card" style="font-size:18px;color:#94a3b8;"></i><input type="text" id="icInput" placeholder="YYMMDD-BP-####" maxlength="14" autocomplete="off" autofocus></div></div>
        <div class="input-group"><label><i class="fas fa-mobile-alt"></i> Phone Number</label><div class="input-box"><i class="fas fa-mobile-alt" style="font-size:18px;color:#94a3b8;"></i><input type="tel" id="phoneInput" placeholder="0123456789" autocomplete="tel"></div></div>
        <button class="btn btn-primary" id="verifyBtn" onclick="verifyIdentity()"><i class="fas fa-lock-open"></i> Sahkan & Cari Anak</button>
    </div>
    <div class="card children-section" id="childrenCard">
        <div class="step-label"><i class="fas fa-child"></i> Pilih Anak Untuk Check In</div>
        <div class="parent-info" id="parentInfo"></div>
        <div id="childrenList"></div>
        <button class="btn btn-primary" id="checkinBtn" onclick="submitCheckin()" style="margin-top:12px;"><i class="fas fa-check-circle"></i> Check In Anak Dipilih</button>
    </div>
    <div class="footer"><i class="fas fa-shield-alt"></i> Sistem selamat — KidsTrack SAFECARE</div>
</div>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let verifiedParentId = null;
let childrenData = [];

function hideMsg(){document.getElementById('errorMsg').classList.remove('show');document.getElementById('successMsg').classList.remove('show');}
function showErr(m){const e=document.getElementById('errorMsg');e.innerHTML='<i class=\"fas fa-exclamation-triangle\"></i> '+m;e.classList.add('show');document.getElementById('successMsg').classList.remove('show');}
function showOk(m){const e=document.getElementById('successMsg');e.innerHTML='<i class=\"fas fa-check-circle\"></i> '+m;e.classList.add('show');document.getElementById('errorMsg').classList.remove('show');}

async function verifyIdentity(){
    const ic=document.getElementById('icInput').value.trim();
    const phone=document.getElementById('phoneInput').value.trim();
    const btn=document.getElementById('verifyBtn');
    if(!ic||ic.replace(/[^0-9]/g,'').length<12){showErr('Sila masukkan IC number lengkap (12 digit).');return;}
    if(!phone||phone.replace(/[^0-9]/g,'').length<7){showErr('Sila masukkan nombor telefon yang sah.');return;}
    hideMsg();btn.disabled=true;btn.innerHTML='<span class=\"spinner\"></span> Mengesahkan...';
    try{
        const r=await fetch('/attendance-scan/verify-parent',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:JSON.stringify({ic:ic,phone:phone})});
        const d=await r.json();
        if(!d.success){showErr(d.message||'Gagal. Semak IC & telefon.');btn.disabled=false;btn.innerHTML='<i class=\"fas fa-lock-open\"></i> Sahkan & Cari Anak';return;}
        verifiedParentId=d.parent_id;childrenData=d.children||[];
        if(childrenData.length===0){showErr('Tiada anak berdaftar.');btn.disabled=false;btn.innerHTML='<i class=\"fas fa-lock-open\"></i> Sahkan & Cari Anak';return;}
        document.getElementById('parentInfo').innerHTML = '<i class=\"fas fa-user\"></i> ' + d.parent_name + ' &mdash; ' + childrenData.length + ' anak';
        renderChildren();document.getElementById('childrenCard').classList.add('show');
        showOk('Identiti disahkan! Pilih anak untuk check-in.');
        btn.disabled=false;btn.innerHTML='<i class=\"fas fa-check-circle\"></i> Disahkan';btn.style.background='#16a34a';
    }catch(e){showErr('Ralat: '+e.message);btn.disabled=false;btn.innerHTML='<i class=\"fas fa-lock-open\"></i> Sahkan & Cari Anak';}
}

function renderChildren(){
    const c=document.getElementById('childrenList');let h='';
    childrenData.forEach((ch,i)=>{
        const badge=ch.checked_in?'<span class="status-badge status-checkin"><i class="fas fa-check-circle"></i> Checked In</span>':'';
        const dis=ch.checked_in?' style="opacity:0.5;pointer-events:none;"':'';
        h+=`<div class="child-item" onclick="toggleChild(${i})"${dis} data-index="${i}"><div class="child-avatar">${ch.initial}</div><div class="child-info"><div class="child-name">${ch.name} ${badge}</div><div class="child-meta"><i class="fas fa-school"></i> ${ch.classroom} &bull; <i class="fas fa-child"></i> ${ch.age} thn</div></div><span class="check-icon"><i class="fas fa-check-circle"></i></span></div>`;
    });
    c.innerHTML=h;
}

function toggleChild(i){const el=document.querySelector('.child-item[data-index="'+i+'"]');if(!el||childrenData[i].checked_in)return;el.classList.toggle('selected');}

async function submitCheckin(){
    const sel=document.querySelectorAll('.child-item.selected');
    if(sel.length===0){showErr('Sila pilih sekurang-kurangnya seorang anak.');return;}
    const ids=[];sel.forEach(e=>ids.push(childrenData[e.dataset.index].id));
    const btn=document.getElementById('checkinBtn');btn.disabled=true;btn.innerHTML='<span class=\"spinner\"></span> Mendaftar...';hideMsg();
    try{
        const r=await fetch('/attendance-scan/bulk-checkin',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:JSON.stringify({parent_id:verifiedParentId,child_ids:ids})});
        const d=await r.json();
        if(d.success){showOk('Check-in berjaya untuk '+d.count+' anak!');childrenData=d.children||childrenData;renderChildren();btn.innerHTML='<i class=\"fas fa-check-circle\"></i> Selesai';btn.style.background='#16a34a';}
        else{showErr(d.message||'Gagal.');btn.disabled=false;btn.innerHTML='<i class=\"fas fa-check-circle\"></i> Check In Anak Dipilih';}
    }catch(e){showErr('Ralat: '+e.message);btn.disabled=false;btn.innerHTML='<i class=\"fas fa-check-circle\"></i> Check In Anak Dipilih';}
}
</script>
</body>
</html>
