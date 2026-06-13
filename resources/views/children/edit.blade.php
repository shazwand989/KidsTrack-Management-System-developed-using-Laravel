@extends('layouts.template')

@section('title', 'Edit Child')
@section('page-title', 'Edit Child')

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

    .rg-actions { display: flex; gap: 10px; margin-top: 4px; }

    .btn-update {
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

    .btn-update:hover { opacity: .9 !important; transform: translateY(-1px) !important; }

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

    .btn-delete {
        background: #fef2f2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
        border-radius: 14px !important;
        padding: 12px 22px !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        transition: background .2s !important;
        margin-left: auto;
    }

    .btn-delete:hover { background: #fee2e2 !important; color: #b91c1c !important; }

    /* Classroom Grid Styles */
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

    /* Dropdown Guardian Styles */
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

    .current-photo {
        font-size: 10px;
        color: #94a3b8;
        text-align: center;
        margin-top: 5px;
    }
</style>

<div class="rg-wrap">

    {{-- Breadcrumb --}}
    <div class="rg-breadcrumb">
        <a href="{{ route('children.index') }}">👶 Children</a>
        <span class="sep">›</span>
        <a href="{{ route('children.show', $child->id) }}">{{ $child->name }}</a>
        <span class="sep">›</span>
        <strong>Edit Child</strong>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div class="alert-success">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif

    {{-- Error Alerts --}}
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

    <form action="{{ route('children.update', $child->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- CHILD INFORMATION --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>👶</span> Child Information
        </div>

        <div class="card-inner">
            <div class="card-photo-col">
                <div class="photo-circle" id="photoCircle" onclick="document.getElementById('childPhoto').click()">
                    @if($child->photo)
                        <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                    @else
                        <span>👶</span>
                    @endif
                </div>
                <div class="upload-zone" onclick="document.getElementById('childPhoto').click()">
                    <span>📸</span>
                    <p>Change Photo</p>
                    <small>JPG/PNG · 2MB</small>
                </div>
                <input type="file" id="childPhoto" name="photo" accept="image/*">
                @if($child->photo)
                    <div class="current-photo">Current: {{ basename($child->photo) }}</div>
                @endif
            </div>

            <div>
                <div class="rg-group">
                    <label class="rg-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $child->name) }}"
                        placeholder="e.g. Ahmad bin Abdullah">
                    @error('name')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                <div class="rg-3col">
                    <div>
                        <label class="rg-label">Age <span class="req">*</span></label>
                        <input type="number" name="age" value="{{ old('age', $child->age) }}"
                            placeholder="e.g. 4" min="0" max="17">
                        @error('age')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="rg-label">IC / Birth Cert <span class="req">*</span></label>
                        <input type="text" name="ic_number" value="{{ old('ic_number', $child->ic_number) }}"
                            placeholder="e.g. 200120-01-1234">
                        @error('ic_number')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="rg-label">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob', $child->dob ? $child->dob->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="rg-group">
                    <label class="rg-label">Home Address <span class="req">*</span></label>
                    <textarea name="address" rows="2"
                        placeholder="e.g. No. 12, Jalan Mawar, Taman Sentosa...">{{ old('address', $child->address) }}</textarea>
                    @error('address')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- CLASSROOM ASSIGNMENT (fetch from classrooms table) --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>🏫</span> Classroom Assignment
        </div>

        <div class="classroom-grid" id="classroomGrid">
            @foreach($classrooms as $classroom)
            <label class="classroom-option {{ old('classroom_id', $child->classroom_id) == $classroom->id ? 'selected' : '' }}">
                <input type="radio" name="classroom_id" value="{{ $classroom->id }}" {{ old('classroom_id', $child->classroom_id) == $classroom->id ? 'checked' : '' }}>
                <span class="classroom-icon">🏫</span>
                <div class="classroom-name">{{ $classroom->name }}</div>
                <div class="classroom-age">Age: {{ $classroom->min_age }}-{{ $classroom->max_age }} yrs</div>
                <div class="classroom-age">Code: {{ $classroom->code }}</div>
            </label>
            @endforeach
        </div>
        @error('classroom_id')<span class="invalid-msg">{{ $message }}</span>@enderror
    </div>

    {{-- PARENT & GUARDIAN ASSIGNMENT --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>👨‍👩‍👧‍👦</span> Parent & Guardian Assignment
        </div>

        {{-- Main Parent --}}
        <div class="rg-group">
            <label class="rg-label">Main Parent <span class="req">*</span></label>
            <div class="guardian-select" id="parentSelect">
                <div class="guardian-preview" onclick="toggleDropdown('parentDropdown')">
                    <div class="guardian-avatar" id="parentAvatar">
                        @if($child->parent && $child->parent->photo)
                            <img src="{{ asset('storage/'.$child->parent->photo) }}" alt="">
                        @elseif($child->parent)
                            <span>{{ strtoupper(substr($child->parent->name, 0, 1)) }}</span>
                        @else
                            <span>👤</span>
                        @endif
                    </div>
                    <div class="guardian-info">
                        <div class="guardian-name" id="parentName">
                            {{ $child->parent->name ?? '-- Select Main Parent --' }}
                        </div>
                        <div class="guardian-detail" id="parentDetail">
                            @if($child->parent)
                                <span>📞 {{ $child->parent->phone }}</span>
                                <span class="guardian-badge">Main Parent</span>
                            @else
                                <span>📞 Click to select</span>
                            @endif
                        </div>
                    </div>
                    <span class="dropdown-arrow">▼</span>
                </div>
                <div class="guardian-dropdown-list" id="parentDropdown">
                    @foreach($parents as $parent)
                    <div class="guardian-option" onclick="selectParent({{ $parent->id }}, '{{ addslashes($parent->name) }}', '{{ addslashes($parent->phone) }}', '{{ $parent->photo }}')">
                        <div class="option-avatar">
                            @if($parent->photo)
                                <img src="{{ asset('storage/'.$parent->photo) }}" alt="">
                            @else
                                <span>{{ strtoupper(substr($parent->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="option-info">
                            <div class="option-name">{{ $parent->name }}</div>
                            <div class="option-detail">
                                📞 {{ $parent->phone }}
                                @if($parent->verified)
                                <span class="option-badge">✅ Verified</span>
                                @endif
                                @if($parent->emergency)
                                <span class="option-badge">⚠️ Emergency</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="parent_id" id="parent_id" value="{{ old('parent_id', $child->parent_id) }}">
            @error('parent_id')<span class="invalid-msg">{{ $message }}</span>@enderror
        </div>

        {{-- Second Parent (Optional) --}}
        <div class="rg-group" style="margin-top: 16px;">
            <label class="rg-label">Second Parent (Optional)</label>
            <div class="guardian-select" id="secondParentSelect">
                <div class="guardian-preview" onclick="toggleDropdown('secondParentDropdown')">
                    <div class="guardian-avatar" id="secondParentAvatar">
                        @if($child->secondParent && $child->secondParent->photo)
                            <img src="{{ asset('storage/'.$child->secondParent->photo) }}" alt="">
                        @elseif($child->secondParent)
                            <span>{{ strtoupper(substr($child->secondParent->name, 0, 1)) }}</span>
                        @else
                            <span>👤</span>
                        @endif
                    </div>
                    <div class="guardian-info">
                        <div class="guardian-name" id="secondParentName">
                            {{ $child->secondParent->name ?? '-- Optional --' }}
                        </div>
                        <div class="guardian-detail" id="secondParentDetail">
                            @if($child->secondParent)
                                <span>📞 {{ $child->secondParent->phone }}</span>
                                <span class="guardian-badge">Second Parent</span>
                            @else
                                <span>📞 Select second parent if any</span>
                            @endif
                        </div>
                    </div>
                    <span class="dropdown-arrow">▼</span>
                </div>
                <div class="guardian-dropdown-list" id="secondParentDropdown">
                    <div class="guardian-option" onclick="selectSecondParent('', '-- None --', '', '')">
                        <div class="option-avatar"><span>➖</span></div>
                        <div class="option-info">
                            <div class="option-name">-- None / Skip --</div>
                        </div>
                    </div>
                    @foreach($parents as $parent)
                    <div class="guardian-option" onclick="selectSecondParent({{ $parent->id }}, '{{ addslashes($parent->name) }}', '{{ addslashes($parent->phone) }}', '{{ $parent->photo }}')">
                        <div class="option-avatar">
                            @if($parent->photo)
                                <img src="{{ asset('storage/'.$parent->photo) }}" alt="">
                            @else
                                <span>{{ strtoupper(substr($parent->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="option-info">
                            <div class="option-name">{{ $parent->name }}</div>
                            <div class="option-detail">📞 {{ $parent->phone }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="second_parent_id" id="second_parent_id" value="{{ old('second_parent_id', $child->second_parent_id) }}">
        </div>

        {{-- Guardian (Optional) --}}
        <div class="rg-group" style="margin-top: 16px;">
            <label class="rg-label">Guardian (Optional)</label>
            <div class="guardian-select" id="guardianSelect">
                <div class="guardian-preview" onclick="toggleDropdown('guardianDropdown')">
                    <div class="guardian-avatar" id="guardianAvatar">
                        @if($child->guardian && $child->guardian->photo)
                            <img src="{{ asset('storage/'.$child->guardian->photo) }}" alt="">
                        @elseif($child->guardian)
                            <span>{{ strtoupper(substr($child->guardian->name, 0, 1)) }}</span>
                        @else
                            <span>🛡️</span>
                        @endif
                    </div>
                    <div class="guardian-info">
                        <div class="guardian-name" id="guardianName">
                            {{ $child->guardian->name ?? '-- Optional --' }}
                        </div>
                        <div class="guardian-detail" id="guardianDetail">
                            @if($child->guardian)
                                <span>📞 {{ $child->guardian->phone }}</span>
                                <span class="guardian-badge">Guardian</span>
                            @else
                                <span>📞 Select guardian if any</span>
                            @endif
                        </div>
                    </div>
                    <span class="dropdown-arrow">▼</span>
                </div>
                <div class="guardian-dropdown-list" id="guardianDropdown">
                    <div class="guardian-option" onclick="selectGuardian('', '-- None --', '', '')">
                        <div class="option-avatar"><span>➖</span></div>
                        <div class="option-info">
                            <div class="option-name">-- None / Skip --</div>
                        </div>
                    </div>
                    @foreach($guardians as $guardian)
                    <div class="guardian-option" onclick="selectGuardian({{ $guardian->id }}, '{{ addslashes($guardian->name) }}', '{{ addslashes($guardian->phone) }}', '{{ $guardian->photo }}')">
                        <div class="option-avatar">
                            @if($guardian->photo)
                                <img src="{{ asset('storage/'.$guardian->photo) }}" alt="">
                            @else
                                <span>{{ strtoupper(substr($guardian->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="option-info">
                            <div class="option-name">{{ $guardian->name }}</div>
                            <div class="option-detail">📞 {{ $guardian->phone }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="guardian_id" id="guardian_id" value="{{ old('guardian_id', $child->guardian_id) }}">
        </div>
    </div>

    {{-- ADDITIONAL INFORMATION --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>📝</span> Additional Information
        </div>

        <div class="rg-group">
            <label class="rg-label">Medical Notes / Allergies</label>
            <textarea name="medical_notes" rows="2"
                placeholder="e.g. Allergic to peanuts, asthma, takes medication...">{{ old('medical_notes', $child->medical_notes) }}</textarea>
        </div>

        <div class="rg-group">
            <label class="rg-label">Dietary Requirements</label>
            <textarea name="dietary" rows="2"
                placeholder="e.g. Vegetarian, no pork, halal only, lactose intolerant">{{ old('dietary', $child->dietary) }}</textarea>
        </div>

        <div class="rg-group">
            <label class="rg-label">Status</label>
            <div style="display: flex; gap: 20px; align-items: center;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" style="width: 18px; height: 18px; display: inline-block !important;" {{ old('is_active', $child->is_active) ? 'checked' : '' }}>
                    <span style="font-size: 13px; font-weight: 600; color: #475569;">Active Enrollment</span>
                </label>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="rg-actions">
        <button type="submit" class="btn-update">
            <span>💾</span> Update Child
        </button>
        <a href="{{ route('children.show', $child->id) }}" class="btn-cancel">
            <span>✖️</span> Cancel
        </a>
        <form action="{{ route('children.destroy', $child->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete {{ $child->name }}? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">
                <span>🗑️</span> Delete Child
            </button>
        </form>
    </div>

    </form>

</div>

<script>
    // Photo preview
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

    // Classroom selection
    document.querySelectorAll('.classroom-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.classroom-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    // Dropdown toggle function
    function toggleDropdown(dropdownId) {
        document.querySelectorAll('.guardian-dropdown-list').forEach(dropdown => {
            if (dropdown.id !== dropdownId) {
                dropdown.style.display = 'none';
            }
        });
        const dropdown = document.getElementById(dropdownId);
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.guardian-select')) {
            document.querySelectorAll('.guardian-dropdown-list').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    // Select Main Parent
    function selectParent(id, name, phone, photo) {
        document.getElementById('parent_id').value = id;
        document.getElementById('parentName').innerHTML = name;
        document.getElementById('parentDetail').innerHTML = `<span>📞 ${phone}</span><span class="guardian-badge">Main Parent</span>`;
        
        const avatarDiv = document.getElementById('parentAvatar');
        if (photo) {
            avatarDiv.innerHTML = `<img src="/storage/${photo}" alt="">`;
        } else {
            avatarDiv.innerHTML = `<span>${name.charAt(0).toUpperCase()}</span>`;
        }
        
        document.getElementById('parentDropdown').style.display = 'none';
    }

    // Select Second Parent
    function selectSecondParent(id, name, phone, photo) {
        document.getElementById('second_parent_id').value = id;
        
        if (id === '') {
            document.getElementById('secondParentName').innerHTML = '-- Optional --';
            document.getElementById('secondParentDetail').innerHTML = '<span>📞 Select second parent if any</span>';
            document.getElementById('secondParentAvatar').innerHTML = '<span>👤</span>';
        } else {
            document.getElementById('secondParentName').innerHTML = name;
            document.getElementById('secondParentDetail').innerHTML = `<span>📞 ${phone}</span><span class="guardian-badge">Second Parent</span>`;
            const avatarDiv = document.getElementById('secondParentAvatar');
            if (photo) {
                avatarDiv.innerHTML = `<img src="/storage/${photo}" alt="">`;
            } else {
                avatarDiv.innerHTML = `<span>${name.charAt(0).toUpperCase()}</span>`;
            }
        }
        
        document.getElementById('secondParentDropdown').style.display = 'none';
    }

    // Select Guardian
    function selectGuardian(id, name, phone, photo) {
        document.getElementById('guardian_id').value = id;
        
        if (id === '') {
            document.getElementById('guardianName').innerHTML = '-- Optional --';
            document.getElementById('guardianDetail').innerHTML = '<span>📞 Select guardian if any</span>';
            document.getElementById('guardianAvatar').innerHTML = '<span>🛡️</span>';
        } else {
            document.getElementById('guardianName').innerHTML = name;
            document.getElementById('guardianDetail').innerHTML = `<span>📞 ${phone}</span><span class="guardian-badge">Guardian</span>`;
            const avatarDiv = document.getElementById('guardianAvatar');
            if (photo) {
                avatarDiv.innerHTML = `<img src="/storage/${photo}" alt="">`;
            } else {
                avatarDiv.innerHTML = `<span>${name.charAt(0).toUpperCase()}</span>`;
            }
        }
        
        document.getElementById('guardianDropdown').style.display = 'none';
    }
</script>

@endsection