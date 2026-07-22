@extends('layouts.template')

@section('title', 'Edit Teacher')
@section('page-title', 'Edit Teacher')

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

    .current-photo {
        font-size: 10px;
        color: #94a3b8;
        text-align: center;
        margin-top: 5px;
    }

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

    .status-options {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }

    .status-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 20px;
        border: 1.5px solid #FFE4D6;
        transition: all .2s;
    }

    .status-option:hover { background: #FFF5F2; border-color: #FF9E7D; }
    .status-option.selected { background: #FFF5F2; border-color: #FF6B6B; }

    .status-option input { width: 16px !important; height: 16px !important; display: inline-block !important; margin: 0 !important; }
    .status-option span { font-size: 13px; font-weight: 600; color: #475569; }
</style>

<div class="rg-wrap">

    {{-- Breadcrumb --}}
    <div class="rg-breadcrumb">
        <a href="{{ route('teachers.index') }}">👩‍<i class="fas fa-school"></i> Teachers</a>
        <span class="sep">›</span>
        <a href="{{ route('teachers.show', $teacher->id) }}">{{ $teacher->name }}</a>
        <span class="sep">›</span>
        <strong>Edit Teacher</strong>
    </div>

    {{-- Error Alerts --}}
    @if($errors->any())
    <div class="alert-error">
        <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Teacher Information --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>👩‍<i class="fas fa-school"></i></span> Teacher Information
        </div>

        <div class="card-inner">
            <div class="card-photo-col">
                <div class="photo-circle" id="photoCircle" onclick="document.getElementById('teacherPhoto').click()">
                    @if($teacher->photo)
                        <img src="{{ asset('storage/'.$teacher->photo) }}" alt="">
                    @else
                        <span>👩‍<i class="fas fa-school"></i></span>
                    @endif
                </div>
                <div class="upload-zone" onclick="document.getElementById('teacherPhoto').click()">
                    <span><i class="fas fa-camera"></i></span>
                    <p>Change Photo</p>
                    <small>JPG/PNG · 2MB</small>
                </div>
                <input type="file" id="teacherPhoto" name="photo" accept="image/*">
                @if($teacher->photo)
                    <div class="current-photo">Current: {{ basename($teacher->photo) }}</div>
                @endif
            </div>

            <div>
                <div class="rg-group">
                    <label class="rg-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}"
                        placeholder="e.g. Mrs. Sarah binti Ahmad">
                    @error('name')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Position / Jawatan <span class="req">*</span></label>
                        <select name="position" style="width:100%;">
                            <option value="">-- Select Position --</option>
                            <option value="Head Teacher" {{ old('position', $teacher->position) == 'Head Teacher' ? 'selected' : '' }}>👩‍<i class="fas fa-school"></i> Head Teacher</option>
                            <option value="Senior Teacher" {{ old('position', $teacher->position) == 'Senior Teacher' ? 'selected' : '' }}>👩‍<i class="fas fa-school"></i> Senior Teacher</option>
                            <option value="Class Teacher" {{ old('position', $teacher->position) == 'Class Teacher' ? 'selected' : '' }}>📚 Class Teacher</option>
                            <option value="Assistant Teacher" {{ old('position', $teacher->position) == 'Assistant Teacher' ? 'selected' : '' }}>📖 Assistant Teacher</option>
                            <option value="Nursery Teacher" {{ old('position', $teacher->position) == 'Nursery Teacher' ? 'selected' : '' }}>🍼 Nursery Teacher</option>
                            <option value="Kindergarten Teacher" {{ old('position', $teacher->position) == 'Kindergarten Teacher' ? 'selected' : '' }}>🎨 Kindergarten Teacher</option>
                            <option value="Special Needs Teacher" {{ old('position', $teacher->position) == 'Special Needs Teacher' ? 'selected' : '' }}>💙 Special Needs Teacher</option>
                            <option value="Trainee Teacher" {{ old('position', $teacher->position) == 'Trainee Teacher' ? 'selected' : '' }}><i class="fas fa-edit"></i> Trainee Teacher</option>
                        </select>
                        @error('position')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="rg-label">Age <span class="req">*</span></label>
                        <input type="number" name="age" value="{{ old('age', $teacher->age) }}"
                            placeholder="e.g. 30" min="18" max="70">
                        @error('age')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}"
                            placeholder="012-XXXXXXX">
                    </div>
                    <div>
                        <label class="rg-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}"
                            placeholder="teacher@example.com">
                        @error('email')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="rg-group">
                    <label class="rg-label">Address</label>
                    <textarea name="address" rows="2"
                        placeholder="e.g. No. 12, Jalan Mawar, Taman Sentosa...">{{ old('address', $teacher->address) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Teaching Assignment --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span><i class="fas fa-school"></i></span> Teaching Assignment
        </div>

        <div class="rg-2col">
            <div>
                <label class="rg-label">Assign to Classroom</label>
                <select name="classroom_id" style="width:100%;">
                    <option value="">-- Select Classroom --</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ old('classroom_id', $teacher->classroom_id) == $classroom->id ? 'selected' : '' }}>
                            <i class="fas fa-school"></i> {{ $classroom->name }} ({{ $classroom->code }}) - Age: {{ $classroom->min_age }}-{{ $classroom->max_age }} yrs
                        </option>
                    @endforeach
                </select>
                @error('classroom_id')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="rg-label">Status <span class="req">*</span></label>
                <div class="status-options">
                    <label class="status-option {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}">
                        <input type="radio" name="status" value="active" {{ old('status', $teacher->status) == 'active' ? 'checked' : '' }}>
                        <span><i class="fas fa-check-circle"></i> Active</span>
                    </label>
                    <label class="status-option {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}">
                        <input type="radio" name="status" value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'checked' : '' }}>
                        <span><i class="fas fa-times-circle"></i> Inactive</span>
                    </label>
                    <label class="status-option {{ old('status', $teacher->status) == 'on_leave' ? 'selected' : '' }}">
                        <input type="radio" name="status" value="on_leave" {{ old('status', $teacher->status) == 'on_leave' ? 'checked' : '' }}>
                        <span>⏳ On Leave</span>
                    </label>
                </div>
                @error('status')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    {{-- Qualifications & Additional Info --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>🎓</span> Qualifications & Additional Info
        </div>

        <div class="rg-2col">
            <div>
                <label class="rg-label">Qualifications</label>
                <textarea name="qualifications" rows="3"
                    placeholder="e.g. Diploma in Early Childhood Education, Bachelor of Education...">{{ old('qualifications', $teacher->qualifications) }}</textarea>
            </div>
            <div>
                <label class="rg-label">Join Date</label>
                <input type="date" name="join_date" value="{{ old('join_date', $teacher->join_date ? $teacher->join_date->format('Y-m-d') : '') }}">
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="rg-actions">
        <button type="submit" class="btn-update">
            <span><i class="fas fa-save"></i></span> Update Teacher
        </button>
        <a href="{{ route('teachers.show', $teacher->id) }}" class="btn-cancel">
            <span><i class="fas fa-times"></i></span> Cancel
        </a>
        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete(this, 'Are you sure you want to delete {{ addslashes($teacher->name) }}? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">
                <i class="fas fa-trash-alt"></i> Delete Teacher
            </button>
        </form>
    </div>

    </form>

</div>

<script>
    // Photo preview
    document.getElementById('teacherPhoto').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const circle = document.getElementById('photoCircle');
                circle.innerHTML = `<img src="${e.target.result}">`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Status option selection styling
    document.querySelectorAll('.status-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.status-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
</script>

@endsection