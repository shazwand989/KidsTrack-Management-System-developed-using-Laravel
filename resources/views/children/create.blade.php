@extends('layouts.template')

@section('title', 'Register Child')
@section('page-title', 'Register Child')

@section('content')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .rg-wrap * { box-sizing: border-box !important; }

    .rg-wrap input,
    .rg-wrap textarea,
    .rg-wrap select.classroom-select {
        display: block !important;
        width: 100% !important;
        border: 1.5px solid #e8e8e8 !important;
        border-radius: 12px !important;
        padding: 11px 14px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background: white !important;
        outline: none !important;
        font-family: 'Inter', sans-serif !important;
        cursor: pointer !important;
    }
    .rg-wrap select.classroom-select:focus {
        border-color: #FF9E7D !important;
        box-shadow: 0 0 0 3px rgba(255,158,125,0.15) !important;
    }

    .rg-wrap input[type="file"] { display: none !important; }
    .rg-wrap input[type="radio"] { display: none !important; }

    .rg-wrap input:focus,
    .rg-wrap textarea:focus,
    .rg-wrap select:focus {
        border-color: #FF9E7D !important;
        box-shadow: 0 0 0 3px rgba(255,158,125,0.15) !important;
    }

    .rg-breadcrumb {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .rg-breadcrumb a { color: #FF6B6B; text-decoration: none; font-weight: 600; }
    .rg-breadcrumb a:hover { text-decoration: underline; }
    .rg-breadcrumb .sep { font-size: 10px; color: #cbd5e1; }
    .rg-breadcrumb strong { font-weight: 700; color: #475569; }

    .rg-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        border: 1px solid #FFF0EC;
        margin-bottom: 18px;
    }

    .rg-section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .rg-section-title span { color: #FF9E7D; font-size: 16px; }

    .card-inner {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 24px;
        align-items: start;
    }

    .card-photo-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .photo-circle {
        width: 90px; height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(255,107,107,0.22);
        overflow: hidden;
        cursor: pointer;
        flex-shrink: 0;
    }

    .photo-circle span { font-size: 36px; color: white; }
    .photo-circle img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

    .upload-zone {
        border: 2px dashed #FFE4D6;
        border-radius: 12px;
        padding: 10px 8px;
        text-align: center;
        cursor: pointer;
        transition: .2s;
        width: 100%;
    }

    .upload-zone:hover { border-color: #FF9E7D; background: #fffcfb; }
    .upload-zone span { font-size: 16px; color: #FF9E7D; display: block; margin-bottom: 4px; }
    .upload-zone p { font-size: 11px; font-weight: 600; color: #475569; margin: 0 0 1px; }
    .upload-zone small { font-size: 10px; color: #94a3b8; }

    .rg-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        margin-bottom: 7px;
    }

    .rg-label .req { color: #FF6B6B; margin-left: 2px; }

    .rg-group { margin-bottom: 16px; }
    .rg-group:last-of-type { margin-bottom: 0; }

    .rg-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
    }

    .rg-3col {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 16px;
    }

    .invalid-msg {
        font-size: 12px;
        color: #ef4444;
        font-weight: 700;
        margin-top: 5px;
        display: block;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #dc2626;
        font-weight: 600;
    }

    .alert-error strong { display: block; margin-bottom: 6px; font-weight: 800; }
    .alert-error ul { margin: 0; padding-left: 18px; }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #16a34a;
        font-weight: 700;
    }

    .rg-actions { display: flex; gap: 10px; margin-top: 4px; flex-wrap: wrap; }

    .btn-save {
        background: linear-gradient(to right, #FF6B6B, #FF9E7D) !important;
        color: white !important;
        border: none !important;
        padding: 12px 26px !important;
        border-radius: 14px !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        box-shadow: 0 6px 16px rgba(255,107,107,0.28) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: opacity .2s, transform .2s !important;
        text-decoration: none !important;
    }

    .btn-save:hover { opacity: .9 !important; transform: translateY(-1px) !important; }

    .btn-cancel {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: none !important;
        padding: 12px 22px !important;
        border-radius: 14px !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        transition: background .2s !important;
    }

    .btn-cancel:hover { background: #e2e8f0 !important; color: #334155 !important; }

    .classroom-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 5px;
    }

    .classroom-option {
        border: 1.5px solid #FFE4D6;
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: all .2s;
        background: white;
        position: relative;
    }

    .classroom-option:hover {
        border-color: #FF9E7D;
        background: #FFF5F2;
    }

    .classroom-option.selected {
        border-color: #FF6B6B;
        background: #FFF5F2;
        box-shadow: 0 4px 12px rgba(255,107,107,0.15);
    }

    .classroom-option.selected::before {
        content: "✓";
        position: absolute;
        top: -8px;
        right: -8px;
        background: #FF6B6B;
        color: white;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .classroom-icon { font-size: 28px; display: block; margin-bottom: 8px; }
    .classroom-name { font-size: 13px; font-weight: 800; color: #1e293b; margin-bottom: 3px; }
    .classroom-age { font-size: 10px; color: #94a3b8; }

    .guardian-select {
        border: 1.5px solid #FFE4D6;
        border-radius: 14px;
        overflow: hidden;
        background: white;
    }

    .guardian-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        cursor: pointer;
        background: white;
    }

    .guardian-preview:hover { background: #FFF5F2; }

    .guardian-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .guardian-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .guardian-avatar span { font-size: 18px; }

    .guardian-info { flex: 1; }
    .guardian-name { font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 3px; }
    .guardian-detail { font-size: 11px; color: #94a3b8; display: flex; gap: 10px; flex-wrap: wrap; }
    .guardian-badge {
        font-size: 9px;
        padding: 2px 8px;
        border-radius: 10px;
        background: #FFF5F2;
        color: #FF6B6B;
        font-weight: 700;
    }
    .dropdown-arrow { color: #FF6B6B; font-size: 12px; margin-left: 5px; }

    .guardian-dropdown-list {
        display: none;
        border-top: 1px solid #FFE4D6;
        max-height: 300px;
        overflow-y: auto;
    }

    .guardian-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #FFF0EC;
        transition: background .2s;
    }

    .guardian-option:last-child { border-bottom: none; }
    .guardian-option:hover { background: #FFF5F2; }

    .option-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .option-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .option-info { flex: 1; }
    .option-name { font-size: 13px; font-weight: 700; color: #1e293b; }
    .option-detail { font-size: 11px; color: #94a3b8; }
    .option-badge {
        font-size: 9px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #FFF5F2;
        color: #FF6B6B;
        font-weight: 700;
        margin-left: 8px;
    }

    .guardian-select.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .guardian-select.disabled .guardian-preview {
        cursor: not-allowed;
    }

    .text-muted {
        color: #94a3b8;
        font-size: 12px;
        margin-top: 5px;
    }

    .add-another-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6d28d9;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 16px;
        background: #f3f0ff;
        border-radius: 10px;
        border: 1.5px solid #ddd6fe;
        transition: all .2s;
    }

    .add-another-link:hover {
        background: #ede9fe;
        border-color: #6d28d9;
        transform: translateY(-1px);
    }

    .add-another-link .icon {
        font-size: 18px;
    }

    .add-another-container {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #e5e7eb;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .add-another-container .info-text {
        font-size: 12px;
        color: #94a3b8;
    }

    /* IC AJAX feedback */
    .ic-input-wrap { position: relative; }
    .ic-input-wrap input { padding-right: 36px !important; }
    .ic-feedback {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        display: none;
        pointer-events: none;
    }
    .ic-feedback.checking { display: block; color: #94a3b8; animation: icSpin 0.8s linear infinite; }
    .ic-feedback.available { display: block; color: #16a34a; }
    .ic-feedback.taken { display: block; color: #dc2626; }
    .ic-feedback-msg {
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
        display: none;
    }
    .ic-feedback-msg.available { display: block; color: #16a34a; }
    .ic-feedback-msg.taken { display: block; color: #dc2626; }
    @keyframes icSpin { from { transform: translateY(-50%) rotate(0deg); } to { transform: translateY(-50%) rotate(360deg); } }

    /* Select2 Custom Styles */
    .select2-container--default .select2-selection--single {
        height: 50px !important;
        border: 1.5px solid #FFE4D6 !important;
        border-radius: 14px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px !important;
        padding-left: 16px !important;
        padding-right: 36px !important;
        font-size: 14px !important;
        color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-results__option {
        padding: 12px 16px !important;
        font-size: 13px !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .select2-container--default .select2-results__option--highlighted {
        background: #FFF5F2 !important;
        color: #1e293b !important;
    }
    .select2-dropdown {
        border: 1.5px solid #FFE4D6 !important;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #FFE4D6 !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        font-size: 13px !important;
        outline: none !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #FF9E7D !important;
    }
</style>

<div class="rg-wrap">

    @if(session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="rg-breadcrumb">
        <a href="{{ route('children.index') }}">👶 Children</a>
        <span class="sep">›</span>
        <strong>Register New Child</strong>
    </div>

    @if($errors->any())
    <div class="alert-error">
        <strong>⚠️ Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('children.store') }}" method="POST" enctype="multipart/form-data" id="childForm">
    @csrf

    {{-- ============================================ --}}
    {{-- SHARED INFO: Parent, Address --}}
    {{-- ============================================ --}}

    {{-- Parent --}}
    <div class="rg-card">
        <div class="rg-section-title"><span>👨‍👩‍👧‍👦</span> Parent</div>

        <div class="rg-group">
            <label class="rg-label">Main Parent <span class="req">*</span></label>
            <select name="parent_id" id="parent_id" style="width:100%;" required>
                <option value="">-- Search parent by IC, name or phone --</option>
                @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                    data-name="{{ addslashes($parent->name) }}"
                    data-ic="{{ $parent->email }}"
                    data-phone="{{ $parent->phone_number }}"
                    data-photo="{{ $parent->photo }}"
                    {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->email }} | {{ $parent->name }} | {{ $parent->phone_number }}
                </option>
                @endforeach
            </select>
            @error('parent_id')<span class="invalid-msg">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- Shared Address --}}
    <div class="rg-card">
        <div class="rg-section-title"><span>📍</span> Home Address</div>
        <div class="rg-group">
            <label class="rg-label">Address <span class="req">*</span></label>
            <textarea name="address" rows="2" placeholder="e.g. No. 12, Jalan Mawar, Taman Sentosa...">{{ old('address') }}</textarea>
            @error('address')<span class="invalid-msg">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DYNAMIC CHILDREN --}}
    {{-- ============================================ --}}
    <div id="childrenContainer">
        <div class="rg-card child-card" data-index="0">
            <div class="rg-section-title" style="justify-content:space-between;">
                <span><span>👶</span> Child #1</span>
                <span class="remove-child" style="display:none;cursor:pointer;color:#dc2626;font-size:12px;" onclick="removeChild(this)">✕ Remove</span>
            </div>
            <div class="card-inner">
                <div class="card-photo-col">
                    <div class="photo-circle" onclick="this.nextElementSibling.nextElementSibling.click()">
                        <span>👶</span>
                    </div>
                    <div class="upload-zone" onclick="this.previousElementSibling.click()">
                        <span>📸</span><p>Upload Photo</p><small>JPG/PNG · 2MB</small>
                    </div>
                    <input type="file" name="children[0][photo]" accept="image/*" onchange="previewChildPhoto(this,0)">
                </div>
                <div>
                    <div class="rg-group">
                        <label class="rg-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="children[0][name]" placeholder="e.g. Ahmad bin Abdullah">
                    </div>
                    <div class="rg-group">
                        <label class="rg-label">🏫 Classroom</label>
                        <select name="children[0][classroom_id]" class="classroom-select" style="width:100%;">
                            <option value="">-- Select Classroom --</option>
                            @foreach($classrooms as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->min_age }}-{{ $c->max_age }} yrs)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rg-3col">
                        <div>
                            <label class="rg-label">IC / Birth Cert <span class="req">*</span></label>
                            <div class="ic-input-wrap">
                                <input type="text" name="children[0][ic_number]" class="ic-check" data-index="0"
                                    placeholder="YYMMDD-BP-####" maxlength="14">
                                <span class="ic-feedback" id="ic_fb_0"></span>
                            </div>
                            <span class="ic-feedback-msg" id="ic_msg_0"></span>
                        </div>
                        <div>
                            <label class="rg-label">Date of Birth <small style="color:#FF9E7D;">(auto)</small></label>
                            <input type="date" name="children[0][dob]" readonly style="background:#f9fafb;cursor:default;">
                        </div>
                        <div>
                            <label class="rg-label">Age <small style="color:#FF9E7D;">(auto)</small></label>
                            <input type="number" name="children[0][age]" readonly style="background:#f9fafb;cursor:default;" min="0" max="17">
                        </div>
                    </div>
                    <div class="rg-2col">
                        <div>
                            <label class="rg-label">Medical Notes</label>
                            <textarea name="children[0][medical_notes]" rows="1" placeholder="e.g. Allergies, asthma..."></textarea>
                        </div>
                        <div>
                            <label class="rg-label">Dietary</label>
                            <textarea name="children[0][dietary]" rows="1" placeholder="e.g. Vegetarian, halal..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Child Button --}}
    <div style="text-align:center;margin:18px 0;">
        <button type="button" onclick="addChild()" class="add-another-link" style="font-size:14px;padding:10px 24px;">
            <span class="icon">➕</span> Add Another Child
        </button>
    </div>

    {{-- Submit --}}
    <div class="rg-actions">
        <button type="submit" class="btn-save"><span>💾</span> Register All Children</button>
        <a href="{{ route('children.index') }}" class="btn-cancel"><span>✖️</span> Cancel</a>
    </div>

    </form>

</div>

<script>
    // ============================================
    // DYNAMIC CHILD COUNTER
    // ============================================
    let childCount = 1;

    function addChild() {
        const container = document.getElementById('childrenContainer');
        const template = container.querySelector('.child-card').cloneNode(true);
        const idx = childCount;
        template.setAttribute('data-index', idx);
        template.querySelector('.rg-section-title span:first-child').innerHTML = '<span>👶</span> Child #' + (idx + 1);
        template.querySelector('.remove-child').style.display = 'inline';
        template.querySelectorAll('input, textarea').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, '[' + idx + ']'));
                el.value = '';
                if (el.type === 'file') el.setAttribute('onchange', el.getAttribute('onchange').replace(/,\d+\)/, ',' + idx + ')'));
            }
            if (el.classList.contains('ic-check')) {
                el.setAttribute('data-index', idx);
                el.removeAttribute('data-ic-bound');
            }
        });
        template.querySelectorAll('.photo-circle').forEach(c => {
            c.innerHTML = '<span>👶</span>';
            c.previousElementSibling && (c.previousElementSibling.innerHTML = '');
        });
        template.querySelectorAll('.ic-feedback').forEach(f => { f.className = 'ic-feedback'; f.innerHTML = ''; f.id = 'ic_fb_' + idx; });
        template.querySelectorAll('.ic-feedback-msg').forEach(m => { m.className = 'ic-feedback-msg'; m.textContent = ''; m.id = 'ic_msg_' + idx; });
        container.appendChild(template);
        setupIcCheck(idx);
        childCount++;
        updateRemoveButtons();
    }

    function removeChild(btn) {
        const card = btn.closest('.child-card');
        if (document.querySelectorAll('.child-card').length > 1) {
            card.remove();
            updateRemoveButtons();
        }
    }

    function updateRemoveButtons() {
        const cards = document.querySelectorAll('.child-card');
        cards.forEach(c => {
            const btn = c.querySelector('.remove-child');
            btn.style.display = cards.length > 1 ? 'inline' : 'none';
        });
    }

    function previewChildPhoto(input, idx) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const circle = input.closest('.card-inner').querySelector('.photo-circle');
                circle.innerHTML = '<img src="' + e.target.result + '">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ============================================
    // FORM VALIDATION
    // ============================================
    const form = document.getElementById('childForm');
    form.addEventListener('submit', function(e) {
        let hasError = false;
        form.querySelectorAll('.client-err').forEach(el => el.remove());
        form.querySelectorAll('input:not([type="radio"]):not([type="hidden"]), textarea').forEach(el => el.style.borderColor = '');

        // Check parent
        if (!$('#parent_id').val()) {
            showErr(document.getElementById('parent_id'), 'Please select a Main Parent');
            hasError = true;
        }
        // Check address
        const addr = form.querySelector('[name="address"]');
        if (!addr.value.trim()) { showErr(addr, 'Address is required'); hasError = true; }
        // Check each child
        document.querySelectorAll('.child-card').forEach(card => {
            const idx = card.getAttribute('data-index');
            const nameEl = card.querySelector('[name$="[name]"]');
            const ageEl = card.querySelector('[name$="[age]"]');
            const icEl = card.querySelector('[name$="[ic_number]"]');
            if (!nameEl.value.trim()) { showErr(nameEl, 'Name is required'); hasError = true; }
            if (!ageEl.value.trim() || isNaN(ageEl.value) || ageEl.value < 0 || ageEl.value > 17) {
                showErr(ageEl, 'Age must be 0-17'); hasError = true;
            }
            if (!icEl.value.trim()) { showErr(icEl, 'IC is required'); hasError = true; }
            const icFb = document.getElementById('ic_fb_' + idx);
            if (icFb && icFb.classList.contains('taken')) {
                showErr(icEl, 'IC already registered'); hasError = true;
            }
        });

        if (hasError) {
            e.preventDefault();
            const firstErr = form.querySelector('.client-err');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function showErr(el, msg) {
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.style.borderColor = '#ef4444';
            el.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        }
        const err = document.createElement('span');
        err.className = 'client-err';
        err.style.cssText = 'font-size:12px;color:#ef4444;font-weight:700;margin-top:4px;display:block;';
        err.textContent = msg;
        el.parentElement.appendChild(err);
    }

    // ============================================
    // IC ↔ DOB ↔ AGE AUTO-FILL
    // ============================================
    function parseIcToDob(ic) {
        // Accept: 200704010123, 200704-01-0123, 070401-01-0123
        const cleaned = ic.replace(/[^0-9]/g, '');
        if (cleaned.length < 6) return null;
        const yy = parseInt(cleaned.substring(0, 2));
        const mm = parseInt(cleaned.substring(2, 4));
        const dd = parseInt(cleaned.substring(4, 6));
        const currentFullYear = new Date().getFullYear();
        let fullYear = 2000 + yy;
        if (fullYear > currentFullYear) fullYear = 1900 + yy;
        if (mm < 1 || mm > 12 || dd < 1 || dd > 31) return null;
        return fullYear + '-' + String(mm).padStart(2,'0') + '-' + String(dd).padStart(2,'0');
    }

    function calcAgeFromDob(dobStr) {
        if (!dobStr) return null;
        const dob = new Date(dobStr);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        return age;
    }

    function autoFormatIc(input) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (val.length > 6) val = val.substring(0,6) + '-' + val.substring(6);
        if (val.length > 9) val = val.substring(0,9) + '-' + val.substring(9);
        if (val.length > 14) val = val.substring(0, 14);
        input.value = val;
        return val;
    }

    // ============================================
    // AJAX IC CHECK + AUTO DOB/AGE (per child)
    // ============================================
    const checkIcRoute = "{{ route('children.check-ic') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const icTimers = {};

    function setupIcCheck(idx) {
        const icInput = document.querySelector('[name="children[' + idx + '][ic_number]"]');
        const dobInput = document.querySelector('[name="children[' + idx + '][dob]"]');
        const ageInput = document.querySelector('[name="children[' + idx + '][age]"]');
        if (!icInput || icInput.dataset.icBound) return;
        icInput.dataset.icBound = '1';
        icInput.maxLength = 14;

        icInput.addEventListener('input', function() {
            const raw = autoFormatIc(this);
            const fb = document.getElementById('ic_fb_' + idx);
            const msg = document.getElementById('ic_msg_' + idx);

            // If cleared, reset DOB, Age, and feedback
            if (!raw) {
                if (dobInput) dobInput.value = '';
                if (ageInput) ageInput.value = '';
                if (fb) { fb.className = 'ic-feedback'; fb.innerHTML = ''; }
                if (msg) { msg.className = 'ic-feedback-msg'; msg.textContent = ''; }
                return;
            }

            // Try parse DOB from IC
            const dob = parseIcToDob(raw);
            if (dob && dobInput) {
                dobInput.value = dob;
                if (ageInput) {
                    const age = calcAgeFromDob(dob);
                    if (age !== null) ageInput.value = age;
                }
            }

            // AJAX check — only when 12 digits complete
            const cleaned = raw.replace(/[^0-9]/g, '');
            if (!fb || !msg) return;

            if (cleaned.length < 12) {
                fb.innerHTML = '&#9888;';
                fb.className = 'ic-feedback taken';
                msg.textContent = '⚠ Need ' + (12 - cleaned.length) + ' more digit(s)';
                msg.className = 'ic-feedback-msg taken';
                return;
            }

            if (cleaned.length > 12) {
                fb.innerHTML = '&#10007;';
                fb.className = 'ic-feedback taken';
                msg.textContent = '✗ Too many digits';
                msg.className = 'ic-feedback-msg taken';
                return;
            }

            fb.innerHTML = '&#8635;'; fb.className = 'ic-feedback checking'; msg.className = 'ic-feedback-msg';
            clearTimeout(icTimers[idx]);
            icTimers[idx] = setTimeout(() => {
                fetch(checkIcRoute, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ ic: raw }) })
                .then(res => res.json())
                .then(data => {
                    if (data.available) { fb.innerHTML = '&#10003;'; fb.className = 'ic-feedback available'; msg.textContent = '✓ IC available'; msg.className = 'ic-feedback-msg available'; }
                    else { fb.innerHTML = '&#10007;'; fb.className = 'ic-feedback taken'; msg.textContent = '✗ ' + data.message; msg.className = 'ic-feedback-msg taken'; }
                })
                .catch(() => { fb.className = 'ic-feedback'; msg.className = 'ic-feedback-msg'; });
            }, 500);
        });
    }
    setupIcCheck(0);
    // ============================================
    // DATA DARI PHP
    // ============================================

    // ============================================
    // AUTO SELECT PARENT IF URL HAS parent_id
    // ============================================
    const preSelectedParentId = '{{ $preSelectedParentId ?? '' }}';
    // Select2 will handle pre-selection via the 'selected' attribute on the <option>
    // No manual click needed

    // ============================================
    // PHOTO PREVIEW
    // ============================================
    document.getElementById('childPhoto').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const circle = document.getElementById('photoCircle');
                circle.innerHTML = `<img src="${e.target.result}">`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // ============================================
    // CLASSROOM SELECTION
    // ============================================
    document.querySelectorAll('.classroom-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.classroom-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
        if (option.querySelector('input[type="radio"]').checked) {
            option.classList.add('selected');
        }
    });

    // ============================================
    // PRE-SELECT OLD VALUES (Select2 handles this via selected attribute)
    // ============================================
</script>

<!-- jQuery + Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#parent_id').select2({
        placeholder: '🔍 Search by IC, name or phone number...',
        allowClear: true,
        width: '100%',
        templateResult: formatParentOption,
        templateSelection: formatParentSelection,
        matcher: function(params, data) {
            // Default matcher + custom search
            if ($.trim(params.term) === '') return data;
            var term = $.trim(params.term).toLowerCase();
            var text = (data.text || '').toLowerCase();
            var ic = ($(data.element).data('ic') || '').toLowerCase();
            var phone = ($(data.element).data('phone') || '').toLowerCase();
            if (text.indexOf(term) > -1 || ic.indexOf(term) > -1 || phone.indexOf(term) > -1) {
                return data;
            }
            return null;
        }
    });

    function formatParentOption(option) {
        if (!option.id) return option.text;
        var $el = $(option.element);
        var name = $el.data('name') || option.text.split('|')[1] || option.text;
        var ic = $el.data('ic') || option.text.split('|')[0] || '';
        var phone = $el.data('phone') || option.text.split('|')[2] || '';
        var photo = $el.data('photo');
        var avatar = photo
            ? '<img src="/storage/' + photo + '" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">'
            : '<span style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#FF6B6B,#FF9E7D);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:16px;">' + (name.trim().charAt(0).toUpperCase()) + '</span>';
        return $(
            '<div style="display:flex;align-items:center;gap:12px;">' +
            avatar +
            '<div style="flex:1;line-height:1.3;">' +
            '<div style="font-weight:700;font-size:14px;">' + name.trim() + '</div>' +
            '<div style="font-size:11px;color:#94a3b8;">' + ic.trim() + ' · 📞 ' + phone.trim() + '</div>' +
            '</div></div>'
        );
    }

    function formatParentSelection(option) {
        if (!option.id) return option.text;
        var $el = $(option.element);
        var name = $el.data('name') || option.text.split('|')[1] || option.text;
        var phone = $el.data('phone') || option.text.split('|')[2] || '';
        return name.trim() + ' (📞 ' + phone.trim() + ')';
    }
});
</script>

@endsection
