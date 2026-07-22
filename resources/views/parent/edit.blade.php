@extends('layouts.template')

@section('title', 'Register Parent')
@section('page-title', 'Register Parent')

@section('content')

<style>
    .rg-wrap * { box-sizing: border-box !important; }

    .rg-wrap input,
    .rg-wrap textarea,
    .rg-wrap select {
        display: block !important;
        width: 100% !important;
        border: 1.5px solid #e8e8e8 !important;
        border-radius: 12px !important;
        padding: 11px 14px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background: white !important;
        outline: none !important;
        box-shadow: none !important;
        font-family: 'Inter', sans-serif !important;
        line-height: 1.5 !important;
        transition: border-color .2s, box-shadow .2s !important;
        -webkit-appearance: auto !important;
        appearance: auto !important;
    }

    .rg-wrap input[type="file"] { display: none !important; }

    .rg-wrap input[type="checkbox"] {
        display: inline-block !important;
        width: 18px !important;
        height: 18px !important;
        padding: 0 !important;
        flex-shrink: 0 !important;
        accent-color: #FF6B6B !important;
        cursor: pointer !important;
        margin-top: 2px !important;
    }

    .rg-wrap input:focus,
    .rg-wrap textarea:focus,
    .rg-wrap select:focus {
        border-color: #FF9E7D !important;
        box-shadow: 0 0 0 3px rgba(255,158,125,0.15) !important;
    }

    .rg-wrap input::placeholder,
    .rg-wrap textarea::placeholder { color: #c8d0db !important; }
    .rg-wrap textarea { resize: vertical !important; min-height: 85px !important; }

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

    .rg-card:last-child { margin-bottom: 0; }

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
        display: flex; align-items: center; justify-content: center;
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

    .rg-2col > div { min-width: 0; }

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

    .check-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px;
        border-radius: 13px;
        border: 1px solid #FFE4D6;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background .2s;
        background: white;
    }

    .check-row:last-child { margin-bottom: 0; }
    .check-row:hover { background: #FFF5F2 !important; }
    .check-row-text p { font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 2px; }
    .check-row-text small { font-size: 11px; color: #94a3b8; }

    .rg-actions { display: flex; gap: 10px; margin-top: 4px; }

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
        box-shadow: none !important;
    }

    .btn-cancel:hover { background: #e2e8f0 !important; color: #334155 !important; }
</style>
<div class="rg-wrap">

    {{-- Breadcrumb --}}
    <div class="rg-breadcrumb">
        <a href="{{ route('parents.index') }}"><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">family_restroom</i> Loving Guardians</a>
        <span class="sep">›</span>
        <strong>Edit Family</strong>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert-error">
        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">warning</i></span> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert-error">
        <strong><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">warning</i> Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ============================================ --}}
    {{-- FAMILY EDIT — All members shown together --}}
    {{-- ============================================ --}}

    {{-- Helper: member card --}}
    @php
        $members = [
            ['role' => 'parent1', 'label' => 'Main Parent', 'icon' => 'family_restroom', 'color' => '#FF6B6B,#FF9E7D', 'data' => $main],
            ['role' => 'parent2', 'label' => 'Second Parent', 'icon' => 'group', 'color' => '#3b82f6,#60a5fa', 'data' => $second],
            ['role' => 'guardian', 'label' => 'Guardian', 'icon' => 'shield', 'color' => '#f59e0b,#fbbf24', 'data' => $guardian],
        ];
    @endphp

    @foreach($members as $m)
    @if($m['data'])
    <div class="rg-card" style="border-left:4px solid {{ explode(',', $m['color'])[0] }};" id="member-{{ $m['role'] }}">
        <div class="rg-section-title">
            <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">{{ $m['icon'] }}</i></span>
            {{ $m['label'] }}
        </div>
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div class="parent-avatar" style="width:60px;height:60px;border-radius:14px;font-size:22px;background:linear-gradient(135deg,{{ $m['color'] }});">
                @if($m['data']->photo)
                    <img src="{{ Storage::url($m['data']->photo) }}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">
                @else
                    {{ strtoupper(substr($m['data']->name, 0, 1)) }}
                @endif
            </div>
            <div style="flex:1;min-width:200px;" class="member-info-{{ $m['role'] }}">
                <div style="font-weight:800;font-size:15px;color:#1e293b;">{{ $m['data']->name }}</div>
                <div style="font-size:12px;color:#94a3b8;"><i class="fas fa-phone"></i> {{ $m['data']->phone_number ?? '-' }} · ✉️ {{ $m['data']->email ?? '-' }}</div>
            </div>
            <button type="button" onclick="toggleMemberEdit('{{ $m['role'] }}')"
                style="font-size:12px;font-weight:700;color:white;background:linear-gradient(135deg,{{ $m['color'] }});border:none;padding:8px 16px;border-radius:10px;cursor:pointer;white-space:nowrap;">
                ✏️ Edit
            </button>
        </div>

        {{-- Inline edit form --}}
        <div id="edit-member-{{ $m['role'] }}" style="display:none;margin-top:14px;padding:14px;background:#FFFAF9;border:1px solid #FFE4D6;border-radius:12px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="mem-name-{{ $m['role'] }}" value="{{ $m['data']->name }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Email <span style="color:#dc2626;">*</span></label>
                    <input type="email" id="mem-email-{{ $m['role'] }}" value="{{ $m['data']->email }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Phone <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="mem-phone-{{ $m['role'] }}" value="{{ $m['data']->phone_number }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Age</label>
                    <input type="text" id="mem-age-{{ $m['role'] }}" value="{{ $m['data']->age }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div style="grid-column:1/-1;"><label style="font-size:10px;font-weight:700;color:#94a3b8;">Address</label>
                    <input type="text" id="mem-address-{{ $m['role'] }}" value="{{ $m['data']->address }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px;justify-content:flex-end;">
                <button type="button" onclick="toggleMemberEdit('{{ $m['role'] }}')" style="background:#f1f5f9;color:#475569;border:none;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="button" onclick="saveMemberEdit('{{ $m['role'] }}', {{ $m['data']->id }})" style="background:linear-gradient(135deg,{{ $m['color'] }});color:white;border:none;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Save</button>
            </div>
            <div id="mem-msg-{{ $m['role'] }}" style="font-size:11px;margin-top:6px;display:none;"></div>
        </div>
    </div>
    @else
    <div class="rg-card" style="border-left:4px solid #e2e8f0;" id="member-{{ $m['role'] }}">
        <div class="rg-section-title">
            <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">{{ $m['icon'] }}</i></span>
            {{ $m['label'] }}
            <small style="color:#94a3b8;">(not registered)</small>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:60px;height:60px;border-radius:14px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:22px;">—</div>
            <div>
                <div style="color:#94a3b8;font-size:13px;">No {{ strtolower($m['label']) }} linked to this family.</div>
                <button type="button" onclick="toggleRegisterMember('{{ $m['role'] }}')"
                    style="font-size:12px;color:white;background:linear-gradient(135deg,{{ $m['color'] }});border:none;padding:7px 16px;border-radius:10px;font-weight:700;cursor:pointer;">
                    + Register {{ $m['label'] }}
                </button>
            </div>
        </div>

        {{-- Inline register form --}}
        <div id="register-member-{{ $m['role'] }}" style="display:none;margin-top:14px;padding:14px;background:#FFFAF9;border:1px solid #FFE4D6;border-radius:12px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="reg-name-{{ $m['role'] }}" placeholder="Full name" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Email <span style="color:#dc2626;">*</span></label>
                    <input type="email" id="reg-email-{{ $m['role'] }}" placeholder="email@example.com" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Phone <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="reg-phone-{{ $m['role'] }}" placeholder="012-XXXXXXX" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Age</label>
                    <input type="text" id="reg-age-{{ $m['role'] }}" placeholder="e.g. 35" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
                <div style="grid-column:1/-1;"><label style="font-size:10px;font-weight:700;color:#94a3b8;">Address</label>
                    <input type="text" id="reg-address-{{ $m['role'] }}" placeholder="Home address" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:7px 10px;font-size:12px;outline:none;"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px;justify-content:flex-end;">
                <button type="button" onclick="toggleRegisterMember('{{ $m['role'] }}')" style="background:#f1f5f9;color:#475569;border:none;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="button" onclick="registerMember('{{ $m['role'] }}', '{{ $m['label'] }}', [{{ implode(',', $allFamilyChildIds) }}])" style="background:linear-gradient(135deg,{{ $m['color'] }});color:white;border:none;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Register & Link</button>
            </div>
            <div id="reg-msg-{{ $m['role'] }}" style="font-size:11px;margin-top:6px;display:none;"></div>
        </div>
    </div>
    @endif
    @endforeach

    {{-- ============================================ --}}
    {{-- SHARED CHILDREN --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title" style="justify-content:space-between;">
            <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">child_care</i></span> Family Children
            <span style="font-size:12px;color:#94a3b8;">{{ $familyChildren->count() }} child(ren)</span>
        </div>

        @if($familyChildren->count() > 0)
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($familyChildren as $child)
            <div id="child-row-{{ $child->id }}" style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:#FFFAF9;border:1px solid #FFE4D6;border-radius:12px;flex-wrap:wrap;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#FF6B6B,#FF9E7D);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">
                    {{ strtoupper(substr($child->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;" class="child-info">
                    <div style="font-weight:700;font-size:13px;color:#1e293b;">{{ $child->name }}</div>
                    <div style="font-size:11px;color:#94a3b8;">{{ $child->classroom->name ?? 'No class' }} · {{ $child->age ?? '?' }} yrs</div>
                </div>
                <button type="button" onclick="editChildInline({{ $child->id }})"
                    style="font-size:11px;color:#3b82f6;font-weight:600;background:none;border:1px solid #bfdbfe;border-radius:8px;padding:4px 10px;cursor:pointer;white-space:nowrap;">✏️ Edit</button>
                <button type="button" onclick="removeChildFromFamily({{ $child->id }}, {{ $currentUser->id }})"
                    style="font-size:11px;color:#dc2626;font-weight:600;background:none;border:1px solid #fecaca;border-radius:8px;padding:4px 10px;cursor:pointer;white-space:nowrap;">🗑 Remove</button>

                {{-- Inline edit form (hidden) --}}
                <div class="inline-edit-form" id="edit-form-{{ $child->id }}" style="display:none;width:100%;padding:10px 0 0 48px;border-top:1px dashed #FFE4D6;margin-top:4px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Name</label><input type="text" id="edit-name-{{ $child->id }}" value="{{ $child->name }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:6px 10px;font-size:12px;outline:none;"></div>
                        <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Classroom</label><select id="edit-classroom-{{ $child->id }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:6px 10px;font-size:12px;outline:none;">@foreach(\App\Models\Classroom::all() as $c)<option value="{{ $c->id }}" {{ $child->classroom_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
                        <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Age</label><input type="number" id="edit-age-{{ $child->id }}" value="{{ $child->age }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:6px 10px;font-size:12px;outline:none;"></div>
                        <div><label style="font-size:10px;font-weight:700;color:#94a3b8;">Address</label><input type="text" id="edit-address-{{ $child->id }}" value="{{ $child->address }}" style="width:100%;border:1.5px solid #FFE4D6;border-radius:8px;padding:6px 10px;font-size:12px;outline:none;"></div>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:8px;justify-content:flex-end;">
                        <button type="button" onclick="cancelEditChild({{ $child->id }})" style="background:#f1f5f9;color:#475569;border:none;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">Cancel</button>
                        <button type="button" onclick="saveChildEdit({{ $child->id }})" style="background:linear-gradient(135deg,#3b82f6,#60a5fa);color:white;border:none;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Save</button>
                    </div>
                    <div id="edit-msg-{{ $child->id }}" style="font-size:11px;margin-top:4px;display:none;"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p style="color:#94a3b8;text-align:center;padding:16px;">No children in this family yet.</p>
        @endif

        {{-- Add Child --}}
        <div style="margin-top:14px;padding-top:14px;border-top:1px dashed #FFE4D6;display:flex;gap:10px;flex-wrap:wrap;">
            <button type="button" onclick="showNewChildForm()"
                style="background:linear-gradient(135deg,#6d28d9,#9333ea);color:white;border:none;padding:10px 18px;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;box-shadow:0 4px 12px rgba(109,40,217,0.2);">
                <i class="fas fa-plus"></i> Register New Child
            </button>
        </div>

        {{-- Inline New Child Form --}}
        <div id="newChildForm" style="display:none;margin-top:14px;padding:16px;background:#faf5ff;border:1.5px solid #ddd6fe;border-radius:14px;">
            <div style="font-weight:700;font-size:13px;color:#4c1d95;margin-bottom:12px;">✨ Quick Register Child</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div><label style="font-size:11px;font-weight:700;color:#7c3aed;">Full Name <span style="color:#dc2626;">*</span></label><input type="text" id="newChildName" placeholder="e.g. Ahmad bin Abdullah" style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;"></div>
                <div><label style="font-size:11px;font-weight:700;color:#7c3aed;">IC / Birth Cert <span style="color:#dc2626;">*</span></label><input type="text" id="newChildIc" placeholder="YYMMDD-BP-####" maxlength="14" style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;"></div>
                <div><label style="font-size:11px;font-weight:700;color:#7c3aed;">Date of Birth</label><input type="date" id="newChildDob" style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;"></div>
                <div><label style="font-size:11px;font-weight:700;color:#7c3aed;">Age</label><input type="number" id="newChildAge" placeholder="Auto from IC" readonly style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;background:#f5f3ff;"></div>
                <div><label style="font-size:11px;font-weight:700;color:#7c3aed;">Classroom</label><select id="newChildClassroom" style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;"><option value="">-- Select --</option>@foreach(\App\Models\Classroom::all() as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div><label style="font-size:11px;font-weight:700;color:#7c3aed;">Address</label><input type="text" id="newChildAddress" placeholder="Home address" style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:12px;justify-content:flex-end;">
                <button type="button" onclick="hideNewChildForm()" style="background:#f1f5f9;color:#475569;border:none;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="button" onclick="registerAndLinkChild({{ $currentUser->id }})" style="background:linear-gradient(135deg,#6d28d9,#9333ea);color:white;border:none;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Register & Link to Family</button>
            </div>
            <div id="newChildMsg" style="margin-top:8px;font-size:12px;display:none;"></div>
        </div>
    </div>

    {{-- Back button --}}
    <div class="rg-actions" style="margin-top:18px;">
        <a href="{{ route('parents.index') }}" class="btn-cancel">
            <span>⬅️</span> Back to Families
        </a>
    </div>

</div>

<script>
    // Toggle member edit form
    function toggleMemberEdit(role) {
        const form = document.getElementById('edit-member-' + role);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Save member via AJAX
    async function saveMemberEdit(role, userId) {
        const name = document.getElementById('mem-name-' + role).value.trim();
        const email = document.getElementById('mem-email-' + role).value.trim();
        const phone = document.getElementById('mem-phone-' + role).value.trim();
        const age = document.getElementById('mem-age-' + role).value.trim();
        const address = document.getElementById('mem-address-' + role).value.trim();
        const msg = document.getElementById('mem-msg-' + role);

        if (!name || !email || !phone) { msg.style.display='block'; msg.style.color='#dc2626'; msg.textContent='Name, email, and phone are required.'; return; }

        msg.style.display = 'block'; msg.style.color = '#3b82f6'; msg.textContent = 'Saving...';

        try {
            await fetch('/parents/' + userId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, phone, age, address, _method: 'PUT' })
            });

            // Update visible info
            const info = document.querySelector('.member-info-' + role);
            if (info) {
                info.innerHTML = '<div style="font-weight:800;font-size:15px;color:#1e293b;">' + name + '</div><div style="font-size:12px;color:#94a3b8;"><i class="fas fa-phone"></i> ' + phone + ' · ✉️ ' + email + '</div>';
            }

            msg.style.color = '#16a34a'; msg.innerHTML = '<i class="fas fa-check-circle"></i> Updated!';
            setTimeout(() => { document.getElementById('edit-member-' + role).style.display = 'none'; msg.style.display = 'none'; }, 800);
        } catch(e) {
            msg.style.color = '#dc2626'; msg.innerHTML = '<i class="fas fa-times-circle"></i> Error.';
        }
    }

    // Toggle register member form
    function toggleRegisterMember(role) {
        const form = document.getElementById('register-member-' + role);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Register new member + link to family children
    async function registerMember(role, label, childIds) {
        const name = document.getElementById('reg-name-' + role).value.trim();
        const email = document.getElementById('reg-email-' + role).value.trim();
        const phone = document.getElementById('reg-phone-' + role).value.trim();
        const age = document.getElementById('reg-age-' + role).value.trim();
        const address = document.getElementById('reg-address-' + role).value.trim();
        const msg = document.getElementById('reg-msg-' + role);

        if (!name || !email || !phone) { msg.style.display='block'; msg.style.color='#dc2626'; msg.textContent='Name, email, and phone are required.'; return; }

        msg.style.display = 'block'; msg.style.color = '#3b82f6'; msg.textContent = 'Registering...';

        try {
            const roleMap = { parent1: 'parent1', parent2: 'parent2', guardian: 'guardian' };
            const res = await fetch('{{ route('parents.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, phone, age, address, password: 'password123', role: roleMap[role], verified: true, child_ids: childIds })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                msg.style.color = '#16a34a'; msg.innerHTML = '<i class="fas fa-check-circle"></i> ' + label + ' registered & linked! Reloading...';
                setTimeout(() => location.reload(), 800);
            } else {
                msg.style.color = '#dc2626'; msg.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'Failed.');
            }
        } catch(e) {
            msg.style.color = '#dc2626'; msg.innerHTML = '<i class="fas fa-times-circle"></i> Network error.';
        }
    }

    // Remove child from this family member via AJAX
    async function removeChildFromFamily(childId, userId) {
        const row = document.getElementById('child-row-' + childId);
        if (!row) return;
        row.style.opacity = '0.35'; row.style.textDecoration = 'line-through'; row.style.pointerEvents = 'none';

        try {
            await fetch('/api/guardianship/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ child_id: childId, user_id: userId })
            });
            setTimeout(() => row.remove(), 500);
        } catch(e) { row.style.opacity = '1'; row.style.textDecoration = ''; row.style.pointerEvents = ''; }
    }

    // Inline Edit
    function editChildInline(childId) {
        document.querySelectorAll('.inline-edit-form').forEach(f => f.style.display = 'none');
        const form = document.getElementById('edit-form-' + childId);
        if (form) form.style.display = 'block';
    }

    function cancelEditChild(childId) {
        document.getElementById('edit-form-' + childId).style.display = 'none';
    }

    async function saveChildEdit(childId) {
        const name = document.getElementById('edit-name-' + childId).value.trim();
        const classroomId = document.getElementById('edit-classroom-' + childId).value;
        const age = document.getElementById('edit-age-' + childId).value;
        const address = document.getElementById('edit-address-' + childId).value;
        const msg = document.getElementById('edit-msg-' + childId);

        if (!name) { msg.style.display='block'; msg.style.color='#dc2626'; msg.textContent='Name required.'; return; }

        msg.style.display = 'block'; msg.style.color = '#3b82f6'; msg.textContent = 'Saving...';

        try {
            const res = await fetch('/children/' + childId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ name, classroom_id: classroomId, age, address, _method: 'PUT' })
            });

            // Update visible info
            const row = document.getElementById('child-row-' + childId);
            if (row) {
                const info = row.querySelector('.child-info');
                if (info) {
                    const cname = document.getElementById('edit-classroom-' + childId);
                    info.innerHTML = '<div style="font-weight:700;font-size:13px;color:#1e293b;">' + name + '</div><div style="font-size:11px;color:#94a3b8;">' + (cname ? cname.options[cname.selectedIndex].text : '') + ' · ' + age + ' yrs</div>';
                }
            }

            msg.style.color = '#16a34a'; msg.innerHTML = '<i class="fas fa-check-circle"></i> Saved!';
            setTimeout(() => { document.getElementById('edit-form-' + childId).style.display = 'none'; msg.style.display = 'none'; }, 1000);
        } catch(e) {
            msg.style.color = '#dc2626'; msg.innerHTML = '<i class="fas fa-times-circle"></i> Error saving.';
        }
    }

    // Show/hide new child form
    function showNewChildForm() { document.getElementById('newChildForm').style.display = 'block'; }
    function hideNewChildForm() { document.getElementById('newChildForm').style.display = 'none'; }

    // IC → DOB → Age auto-fill
    document.getElementById('newChildIc').addEventListener('input', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val.length > 6) val = val.substring(0,6) + '-' + val.substring(6);
        if (val.length > 9) val = val.substring(0,9) + '-' + val.substring(9);
        if (val.length > 14) val = val.substring(0, 14);
        this.value = val;

        const cleaned = val.replace(/[^0-9]/g, '');
        if (cleaned.length >= 6) {
            const yy = parseInt(cleaned.substring(0,2));
            const mm = parseInt(cleaned.substring(2,4));
            const dd = parseInt(cleaned.substring(4,6));
            const now = new Date();
            let fullYear = 2000 + yy;
            if (fullYear > now.getFullYear()) fullYear = 1900 + yy;
            if (mm >= 1 && mm <= 12 && dd >= 1 && dd <= 31) {
                const dob = fullYear + '-' + String(mm).padStart(2,'0') + '-' + String(dd).padStart(2,'0');
                document.getElementById('newChildDob').value = dob;
                const age = now.getFullYear() - fullYear - (now.getMonth()+1 < mm || (now.getMonth()+1 === mm && now.getDate() < dd) ? 1 : 0);
                document.getElementById('newChildAge').value = age;
            }
        }
    });

    // Register & Link new child via AJAX
    async function registerAndLinkChild(parentId) {
        const name = document.getElementById('newChildName').value.trim();
        const ic = document.getElementById('newChildIc').value.trim();
        const dob = document.getElementById('newChildDob').value;
        const age = document.getElementById('newChildAge').value;
        const classroomId = document.getElementById('newChildClassroom').value;
        const address = document.getElementById('newChildAddress').value.trim();
        const msg = document.getElementById('newChildMsg');

        if (!name || !ic) { msg.style.display='block'; msg.style.color='#dc2626'; msg.textContent='Name and IC are required.'; return; }

        msg.style.display = 'block'; msg.style.color = '#7c3aed'; msg.textContent = 'Registering...';

        try {
            const res = await fetch('{{ route('children.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({
                    parent_id: parentId,
                    address: address || 'TBD',
                    children: [{ name, ic_number: ic.replace(/[^0-9]/g,''), dob, age, classroom_id: classroomId }]
                })
            });
            const data = await res.json();
            if (res.ok && data.child_id) {
                // Success — reload to show new child in list
                msg.style.color = '#16a34a'; msg.innerHTML = '<i class="fas fa-check-circle"></i> Child registered & linked! Reloading...';
                setTimeout(() => location.reload(), 800);
            } else {
                msg.style.color = '#dc2626'; msg.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'Registration failed.');
            }
        } catch(e) {
            msg.style.color = '#dc2626'; msg.innerHTML = '<i class="fas fa-times-circle"></i> Network error.';
        }
    }
</script>

@endsection
