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
        <a href="{{ route('parents.show', $parent->id) }}">{{ $parent->name }}</a>
        <span class="sep">›</span>
        <strong>Edit {{ $parent->role === 'parent1' ? 'Parent' : ($parent->role === 'parent2' ? 'Second Parent' : ($parent->role === 'guardian' ? 'Guardian' : 'User')) }}</strong>
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

    <form action="{{ route('parents.update', $parent->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ============================================ --}}
    {{-- MAIN PARENT --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">person</i></span> {{ $parent->role === 'parent1' ? 'Main Parent' : ($parent->role === 'parent2' ? 'Second Parent' : ($parent->role === 'guardian' ? 'Guardian' : 'User')) }}
        </div>

        <div class="card-inner">
            <div class="card-photo-col">
                <div class="photo-circle" id="photoCircle1"
                    onclick="document.getElementById('photoFile1').click()">
                    @if($parent->photo)
                        <img src="{{ Storage::url($parent->photo) }}">
                    @else
                        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">person</i></span>
                    @endif
                </div>
                <div class="upload-zone"
                    onclick="document.getElementById('photoFile1').click()">
                    <span>📸</span>
                    <p>Upload Photo</p>
                    <small>JPG/PNG · 2MB</small>
                </div>
                <input type="file" id="photoFile1" name="photo" accept="image/*">
                @if($parent->photo)
                    <small style="display:block; margin-top:6px; color:#888;">
                        Current: {{ basename($parent->photo) }}
                    </small>
                @endif
            </div>

            <div>
                <div class="rg-group">
                    <label class="rg-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $parent->name) }}"
                        placeholder="e.g. Mrs. Sarah bt Ali">
                    @error('name')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                {{-- EMAIL MAIN PARENT --}}
                <div class="rg-group">
                    <label class="rg-label">Email Address <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $parent->email ?? '') }}"
                        placeholder="sarah@example.com">
                    @error('email')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                {{-- PASSWORD MAIN PARENT --}}
                <div class="rg-group">
                    <label class="rg-label">Password</label>
                    <input type="password" name="password"
                        placeholder="Leave blank to keep current password">
                    <small style="color:#888; font-size:12px;">Leave blank to keep current password</small>
                    @error('password')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Age</label>
                        <input type="text" name="age" value="{{ old('age', $parent->age) }}"
                            placeholder="e.g. 35">
                    </div>
                    <div>
                        <label class="rg-label">Phone Number <span class="req">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $parent->phone) }}"
                            placeholder="012-XXXXXXX">
                        @error('phone')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="rg-group">
                    <label class="rg-label">Home Address <span class="req">*</span></label>
                    <textarea name="address"
                        placeholder="e.g. No. 12, Jalan Mawar...">{{ old('address', $parent->address) }}</textarea>
                    @error('address')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
{{-- SPECIAL SETTINGS --}}
{{-- ============================================ --}}
<div class="rg-card">
    <div class="rg-section-title">
        <span>⚙️</span> Special Settings
    </div>

    <div style="display:grid; grid-template-columns:1fr; gap:12px;">
        <label class="check-row">
            <input type="hidden" name="verified" value="0">
            <input type="checkbox" name="verified" value="1"
                {{ old('verified', $parent->verified) ? 'checked' : '' }}>
            <div class="check-row-text">
                <p><i class="fas fa-check-circle" style="font-size:10px;"></i> Verified</p>
                <small>Identity has been confirmed</small>
            </div>
        </label>
    </div>
</div>

    {{-- ============================================ --}}
    {{-- LINKED CHILDREN --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title" style="justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">child_care</i></span> Linked Children
            <span style="font-size:12px;color:#94a3b8;font-weight:400;">{{ count($assignedChildIds) }} child(ren)</span>
        </div>

        @if(count($assignedChildIds) > 0)
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($parent->children as $child)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:#FFFAF9;border:1px solid #FFE4D6;border-radius:12px;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#FF6B6B,#FF9E7D);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">
                    {{ strtoupper(substr($child->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:13px;color:#1e293b;">{{ $child->name }}</div>
                    <div style="font-size:11px;color:#94a3b8;">{{ $child->classroom->name ?? 'No class' }} · {{ $child->age ?? '?' }} yrs</div>
                </div>
                <a href="{{ route('children.edit', $child->id) }}" style="font-size:11px;color:#3b82f6;font-weight:600;text-decoration:none;padding:4px 10px;border:1px solid #bfdbfe;border-radius:8px;white-space:nowrap;">✏️ Edit</a>
                <button type="button" onclick="removeChild({{ $child->id }}, '{{ addslashes($child->name) }}')"
                    style="font-size:11px;color:#dc2626;font-weight:600;background:none;border:1px solid #fecaca;border-radius:8px;padding:4px 10px;cursor:pointer;white-space:nowrap;">🗑 Remove</button>
            </div>
            @endforeach
        </div>
        @else
        <p style="color:#94a3b8;text-align:center;padding:16px;">No children linked yet.</p>
        @endif

        {{-- Add Child: dropdown for existing + button for new --}}
        <div style="margin-top:14px;padding-top:14px;border-top:1px dashed #FFE4D6;display:flex;gap:10px;flex-wrap:wrap;">
            <select id="addChildSelect" style="flex:1;min-width:200px;border:1.5px solid #FFE4D6;border-radius:12px;padding:10px 14px;font-size:13px;color:#1e293b;outline:none;cursor:pointer;">
                <option value="">+ Link existing child...</option>
                @foreach($allChildren->whereNotIn('id', $assignedChildIds) as $child)
                <option value="{{ $child->id }}">{{ $child->name }} ({{ $child->classroom->name ?? 'No class' }})</option>
                @endforeach
            </select>
            <button type="button" onclick="showNewChildForm()"
                style="background:linear-gradient(135deg,#6d28d9,#9333ea);color:white;border:none;padding:10px 18px;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;box-shadow:0 4px 12px rgba(109,40,217,0.2);">
                ➕ Register New Child
            </button>
        </div>

        {{-- Inline New Child Form --}}
        <div id="newChildForm" style="display:none;margin-top:14px;padding:16px;background:#faf5ff;border:1.5px solid #ddd6fe;border-radius:14px;">
            <div style="font-weight:700;font-size:13px;color:#4c1d95;margin-bottom:12px;">✨ Quick Register Child</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:11px;font-weight:700;color:#7c3aed;">Full Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="newChildName" placeholder="e.g. Ahmad bin Abdullah"
                        style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#7c3aed;">IC / Birth Cert <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="newChildIc" placeholder="YYMMDD-BP-####" maxlength="14"
                        style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#7c3aed;">Date of Birth</label>
                    <input type="date" id="newChildDob"
                        style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#7c3aed;">Age</label>
                    <input type="number" id="newChildAge" placeholder="Auto from IC" readonly
                        style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;background:#f5f3ff;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#7c3aed;">Classroom</label>
                    <select id="newChildClassroom" style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;">
                        <option value="">-- Select --</option>
                        @foreach(\App\Models\Classroom::all() as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#7c3aed;">Address</label>
                    <input type="text" id="newChildAddress" placeholder="Home address"
                        style="width:100%;border:1.5px solid #ddd6fe;border-radius:10px;padding:8px 12px;font-size:13px;outline:none;">
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:12px;justify-content:flex-end;">
                <button type="button" onclick="hideNewChildForm()"
                    style="background:#f1f5f9;color:#475569;border:none;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="button" onclick="registerAndLinkChild({{ $parent->id }})"
                    style="background:linear-gradient(135deg,#6d28d9,#9333ea);color:white;border:none;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;">💾 Register & Link</button>
            </div>
            <div id="newChildMsg" style="margin-top:8px;font-size:12px;display:none;"></div>
        </div>
    </div>

    {{-- Hidden checkboxes container for form submission --}}
    <div id="childIdsContainer" style="display:none;">
        @foreach($assignedChildIds as $cid)
            <input type="checkbox" name="child_ids[]" value="{{ $cid }}" checked data-child="{{ $cid }}">
        @endforeach
        <input type="checkbox" name="child_ids[]" value="_marker" data-marker="1" style="display:none;">
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div class="rg-actions">
        <button type="submit" class="btn-save">
            <span>💾</span> Update Parent
        </button>
        <a href="{{ route('parents.index') }}" class="btn-cancel">
            <span>✖️</span> Cancel
        </a>
    </div>

    </form>

</div>

<script>
    // Add Child via dropdown
    document.getElementById('addChildSelect').addEventListener('change', function() {
        const childId = this.value;
        if (!childId) return;
        const name = this.options[this.selectedIndex].text;

        // Add hidden checkbox
        const cb = document.createElement('input');
        cb.type = 'checkbox'; cb.name = 'child_ids[]'; cb.value = childId;
        cb.checked = true; cb.dataset.child = childId;
        cb.style.display = 'none';
        document.getElementById('childIdsContainer').appendChild(cb);

        // Add visual row
        const row = document.createElement('div');
        row.id = 'child-row-' + childId;
        row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;margin-top:8px;';
        row.innerHTML = `
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#FF6B6B,#FF9E7D);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">${name.charAt(0).toUpperCase()}</div>
            <div style="flex:1;min-width:0;"><div style="font-weight:700;font-size:13px;color:#1e293b;">${name}</div><div style="font-size:11px;color:#16a34a;">🆕 Will be added</div></div>
            <button type="button" onclick="undoAdd(${childId})" style="font-size:11px;color:#dc2626;font-weight:600;background:none;border:1px solid #fecaca;border-radius:8px;padding:4px 10px;cursor:pointer;">✕ Undo</button>
        `;
        document.getElementById('childIdsContainer').previousElementSibling.appendChild(row);
        this.value = '';
    });

    // Remove Child
    function removeChild(childId, name) {
        if (!confirm('Remove ' + name + ' from this family?')) return;

        // Uncheck hidden checkbox
        document.querySelectorAll('#childIdsContainer input[data-child="' + childId + '"]').forEach(cb => cb.checked = false);

        // Hide visual row
        const row = document.getElementById('child-row-' + childId) || event.target.closest('[id^="child-row-"]')?.previousElementSibling?.querySelector('[id="child-row-' + childId + '"]');
        const parentRow = event.target.closest('div[style*="display:flex"]');
        if (parentRow && parentRow.parentElement) {
            parentRow.style.opacity = '0.3';
            parentRow.style.textDecoration = 'line-through';
            parentRow.querySelector('button').textContent = 'Will be removed';
            parentRow.querySelector('button').style.color = '#94a3b8';
        }
    }

    // Undo Add
    function undoAdd(childId) {
        document.querySelectorAll('#childIdsContainer input[data-child="' + childId + '"]').forEach(cb => cb.remove());
        const row = document.getElementById('child-row-' + childId);
        if (row) row.remove();
    }

    // Clean up marker before submit
    document.querySelector('form').addEventListener('submit', function() {
        document.querySelectorAll('#childIdsContainer input[data-marker]').forEach(m => m.remove());
    });

    // Photo preview
    ['photoFile1','photoFile2','photoFile3'].forEach((id, i) => {
        const input = document.getElementById(id);
        if (input) input.addEventListener('change', function() {
            if (!this.files[0]) return;
            const r = new FileReader();
            r.onload = e => document.getElementById('photoCircle' + (i+1)).innerHTML = `<img src="${e.target.result}">`;
            r.readAsDataURL(this.files[0]);
        });
    });
</script>

@endsection
