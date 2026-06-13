<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidsTrack — Cari Anak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container { max-width: 500px; margin: 0 auto; }

        .logo { text-align: center; color: white; margin-bottom: 30px; }
        .logo h1 { font-size: 32px; font-weight: 800; }
        .logo p   { opacity: 0.85; margin-top: 5px; font-size: 14px; }

        .card {
            background: white;
            border-radius: 30px;
            padding: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .section-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            display: block;
            font-size: 15px;
        }

        /* Search box */
        .search-box {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 16px;
            gap: 10px;
            transition: border 0.2s;
            position: relative;
        }
        .search-box:focus-within { border-color: #FF6B6B; }
        .search-box input {
            flex: 1;
            border: none;
            background: none;
            font-size: 16px;
            outline: none;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        /* Dropdown results */
        .dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0; right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 100;
            overflow: hidden;
            display: none;
        }
        .dropdown.show { display: block; }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.15s;
            border-bottom: 1px solid #f0f0f0;
        }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: #FFF5F2; }
        .dropdown-item.selected { background: #FFF5F2; }

        .avatar {
            width: 44px; height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 18px; font-weight: 800;
            overflow: hidden; flex-shrink: 0;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        .item-info .name { font-weight: 700; color: #1e293b; font-size: 14px; }
        .item-info .meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .hint-text {
            padding: 15px;
            text-align: center;
            color: #cbd5e1;
            font-size: 13px;
        }

        /* Selected child display */
        .selected-child {
            display: none;
            align-items: center;
            gap: 12px;
            background: #FFF5F2;
            border: 2px solid #FF6B6B;
            border-radius: 16px;
            padding: 12px 15px;
            margin-top: 10px;
        }
        .selected-child.show { display: flex; }
        .selected-child .info .name { font-weight: 700; color: #1e293b; }
        .selected-child .info .meta { font-size: 12px; color: #94a3b8; }
        .clear-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            padding: 4px;
        }

        /* Phone section */
        .phone-section {
            display: none;
            margin-top: 20px;
        }
        .phone-section.show { display: block; }

        .phone-box {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 16px;
            gap: 10px;
            transition: border 0.2s;
            margin-bottom: 12px;
        }
        .phone-box:focus-within { border-color: #FF6B6B; }
        .phone-box input {
            flex: 1;
            border: none;
            background: none;
            font-size: 16px;
            outline: none;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        .btn-submit {
            width: 100%;
            border: none;
            padding: 15px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
            color: white;
            box-shadow: 0 4px 15px rgba(255,107,107,0.3);
            transition: all 0.2s;
        }
        .btn-submit:active { transform: translateY(-2px); }
        .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

        .error-msg {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
            display: none;
            text-align: center;
        }
        .error-msg.show { display: block; }

        .footer-note {
            text-align: center;
            padding: 20px;
            color: white;
            font-size: 12px;
            opacity: 0.8;
        }

        .divider {
            border: none;
            border-top: 1px solid #f0f0f0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="logo">
        <h1>🏫 KidsTrack</h1>
        <p>Check in / Check out anak anda</p>
    </div>

    <div class="card">

        {{-- STEP 1: Cari nama anak --}}
        <span class="section-label">👶 Step 1: Cari Nama Anak</span>

        <div class="search-box" id="searchWrap">
            <span style="font-size:20px">🔍</span>
            <input
                type="text"
                id="searchInput"
                placeholder="Taip nama anak..."
                autocomplete="off"
                autofocus
            >
            <span id="loadingIcon" style="display:none">⏳</span>

            <div class="dropdown" id="dropdown"></div>
        </div>

        {{-- Selected child --}}
        <div class="selected-child" id="selectedChild">
            <div class="avatar" id="selectedAvatar"></div>
            <div class="info">
                <div class="name" id="selectedName"></div>
                <div class="meta" id="selectedMeta"></div>
            </div>
            <button class="clear-btn" onclick="clearSelection()">✕</button>
        </div>

        <hr class="divider" id="divider" style="display:none">

        {{-- STEP 2: Masuk no phone --}}
        <div class="phone-section" id="phoneSection">
            <span class="section-label">📱 Step 2: Masukkan No Telefon Ibu Bapa</span>

            <div class="phone-box">
                <span style="font-size:20px">📱</span>
                <input
                    type="tel"
                    id="phoneInput"
                    placeholder="Contoh: 0123456789"
                    autocomplete="tel"
                >
            </div>

            <div class="error-msg" id="errorMsg"></div>

            <button class="btn-submit" id="submitBtn" onclick="submitVerify()">
                🔓 Sahkan & Teruskan
            </button>
        </div>

    </div>

    <div class="footer-note">
        🔒 Sistem check in/out selamat — KidsTrack
    </div>

</div>

<script>
    const searchInput   = document.getElementById('searchInput');
    const dropdown      = document.getElementById('dropdown');
    const loadingIcon   = document.getElementById('loadingIcon');
    const selectedChild = document.getElementById('selectedChild');
    const phoneSection  = document.getElementById('phoneSection');
    const divider       = document.getElementById('divider');
    const errorMsg      = document.getElementById('errorMsg');
    const submitBtn     = document.getElementById('submitBtn');

    const searchUrl = '{{ url("/attendance/search/results") }}';
    const verifyUrl = '{{ url("/attendance/child") }}';
    const csrfToken = '{{ csrf_token() }}';

    let selectedChildId   = null;
    let selectedChildName = null;
    let timer;

    // ── Search ──────────────────────────────────────
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        const q = searchInput.value.trim();

        if (q.length < 2) {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
            return;
        }

        loadingIcon.style.display = 'inline';

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                loadingIcon.style.display = 'none';

                if (data.length === 0) {
                    dropdown.innerHTML = '<div class="hint-text">😕 Tiada anak dijumpai</div>';
                    dropdown.classList.add('show');
                    return;
                }

                dropdown.innerHTML = data.map(child => `
                    <div class="dropdown-item" onclick="selectChild(${child.id}, '${child.name}', '${child.classroom}', '${child.age}', '${child.initial}', '${child.photo || ''}')">
                        <div class="avatar">
                            ${child.photo
                                ? `<img src="${child.photo}" alt="${child.name}">`
                                : child.initial
                            }
                        </div>
                        <div class="item-info">
                            <div class="name">${child.name}</div>
                            <div class="meta">🏫 ${child.classroom} • 👶 ${child.age} thn</div>
                        </div>
                    </div>
                `).join('');

                dropdown.classList.add('show');

            } catch (err) {
                loadingIcon.style.display = 'none';
                dropdown.innerHTML = '<div class="hint-text">⚠️ Ralat. Cuba semula.</div>';
                dropdown.classList.add('show');
            }
        }, 350);
    });

    // Tutup dropdown bila klik luar
    document.addEventListener('click', (e) => {
        if (!document.getElementById('searchWrap').contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // ── Pilih anak ──────────────────────────────────
    function selectChild(id, name, classroom, age, initial, photo) {
        selectedChildId   = id;
        selectedChildName = name;

        // Tunjuk selected child
        document.getElementById('selectedAvatar').innerHTML = photo
            ? `<img src="${photo}" alt="${name}">`
            : initial;
        document.getElementById('selectedName').textContent = name;
        document.getElementById('selectedMeta').textContent = `🏫 ${classroom} • 👶 ${age} thn`;

        selectedChild.classList.add('show');
        searchInput.value = name;
        dropdown.classList.remove('show');

        // Tunjuk phone section
        divider.style.display = 'block';
        phoneSection.classList.add('show');
        document.getElementById('phoneInput').focus();

        hideError();
    }

    // ── Clear selection ──────────────────────────────
    function clearSelection() {
        selectedChildId   = null;
        selectedChildName = null;
        searchInput.value = '';
        selectedChild.classList.remove('show');
        phoneSection.classList.remove('show');
        divider.style.display = 'none';
        document.getElementById('phoneInput').value = '';
        hideError();
        searchInput.focus();
    }

    // ── Submit verify ────────────────────────────────
    async function submitVerify() {
        const phone = document.getElementById('phoneInput').value.trim();

        if (!selectedChildId) {
            showError('Sila pilih anak dahulu.');
            return;
        }

        if (!phone) {
            showError('Sila masukkan no telefon.');
            return;
        }

        submitBtn.disabled    = true;
        submitBtn.textContent = '⏳ Mengesahkan...';
        hideError();

        try {
            const res = await fetch(`${verifyUrl}/${selectedChildId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ phone: phone })
            });

            const data = await res.json();

            if (data.success) {
                // Redirect ke child profile
                window.location.href = `${verifyUrl}/${selectedChildId}`;
            } else {
                showError(data.message || 'No phone tidak sepadan. Cuba semula.');
                submitBtn.disabled    = false;
                submitBtn.textContent = '🔓 Sahkan & Teruskan';
            }

        } catch (err) {
            showError('Ralat sambungan. Cuba semula.');
            submitBtn.disabled    = false;
            submitBtn.textContent = '🔓 Sahkan & Teruskan';
        }
    }

    // Allow enter key on phone input
    document.getElementById('phoneInput')?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') submitVerify();
    });

    function showError(msg) {
        errorMsg.textContent = '⚠️ ' + msg;
        errorMsg.classList.add('show');
    }

    function hideError() {
        errorMsg.classList.remove('show');
    }
</script>
</body>
</html>