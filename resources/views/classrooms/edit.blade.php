@extends('layouts.template')

@section('title', 'Edit Classroom')
@section('page-title', 'Edit Classroom')

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

    .rg-actions {
        display: flex;
        gap: 10px;
        margin-top: 4px;
        flex-wrap: wrap;
    }

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

    .color-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 5px;
    }

    .color-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all .2s;
    }

    .color-option.selected {
        border-color: #1e293b;
        transform: scale(1.1);
        box-shadow: 0 0 0 2px white, 0 0 0 4px #1e293b;
    }

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

    .info-note {
        background: #FFF5F2;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
        font-size: 12px;
        color: #FF6B6B;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="rg-wrap">

    {{-- Breadcrumb --}}
    <div class="rg-breadcrumb">
        <a href="{{ route('classrooms.index') }}"><i class="fas fa-school"></i> Classrooms</a>
        <span class="sep">›</span>
        <a href="{{ route('classrooms.show', $classroom->id) }}">{{ $classroom->name }}</a>
        <span class="sep">›</span>
        <strong>Edit Classroom</strong>
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

    {{-- Info Note --}}
    <div class="info-note">
        <span>ℹ️</span>
        Editing classroom information. Changes will affect all children in this class.
    </div>

    <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Basic Information --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span><i class="fas fa-school"></i></span> Basic Information
        </div>

        <div class="rg-2col">
            <div>
                <label class="rg-label">Classroom Name <span class="req">*</span></label>
                <input type="text" name="name" value="{{ old('name', $classroom->name) }}"
                    placeholder="e.g. Little Stars, Sunshine, Rainbow">
                @error('name')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="rg-label">Classroom Code <span class="req">*</span></label>
                <input type="text" name="code" value="{{ old('code', $classroom->code) }}"
                    placeholder="e.g. LS-01, SUN-01, RB-01">
                @error('code')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="rg-2col">
            <div>
                <label class="rg-label">Age Group <span class="req">*</span></label>
                <input type="text" name="age_group" value="{{ old('age_group', $classroom->age_group) }}"
                    placeholder="e.g. Toddler, Preschool, Kindergarten">
                @error('age_group')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="rg-label">Capacity <span class="req">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', $classroom->capacity) }}"
                    placeholder="Max number of children" min="1">
                @error('capacity')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="rg-2col">
            <div>
                <label class="rg-label">Minimum Age (years) <span class="req">*</span></label>
                <input type="number" name="min_age" value="{{ old('min_age', $classroom->min_age) }}"
                    placeholder="e.g. 2" min="0" max="12">
                @error('min_age')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="rg-label">Maximum Age (years) <span class="req">*</span></label>
                <input type="number" name="max_age" value="{{ old('max_age', $classroom->max_age) }}"
                    placeholder="e.g. 4" min="0" max="12">
                @error('max_age')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    {{-- Teacher Assignment --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>👩‍<i class="fas fa-school"></i></span> Teacher Assignment
        </div>

        <div>
            <label class="rg-label">Class Teacher</label>
            <select name="teacher_id" style="width:100%;">
                <option value="">-- Select Teacher --</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" 
                        {{ old('teacher_id', $classroom->teacher_id) == $teacher->id ? 'selected' : '' }}>
                        👩‍<i class="fas fa-school"></i> {{ $teacher->name }} - {{ $teacher->position }}
                    </option>
                @endforeach
            </select>
            @error('teacher_id')<span class="invalid-msg">{{ $message }}</span>@enderror
        </div>
        
        @if($classroom->teacher)
        <div style="margin-top: 10px; font-size: 11px; color: #94a3b8;">
            Current Teacher: 👩‍<i class="fas fa-school"></i> {{ $classroom->teacher->name }} ({{ $classroom->teacher->position }})
        </div>
        @endif
    </div>

    {{-- Schedule --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>⏰</span> Class Schedule
        </div>

        <div class="rg-2col">
            <div>
                <label class="rg-label">Start Time <span class="req">*</span></label>
                <input type="time" name="start_time" value="{{ old('start_time', $classroom->start_time ? date('H:i', strtotime($classroom->start_time)) : '08:00') }}">
                @error('start_time')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="rg-label">End Time <span class="req">*</span></label>
                <input type="time" name="end_time" value="{{ old('end_time', $classroom->end_time ? date('H:i', strtotime($classroom->end_time)) : '17:00') }}">
                @error('end_time')<span class="invalid-msg">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    {{-- Appearance & Status --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>🎨</span> Appearance & Status
        </div>

        <div class="rg-group">
            <label class="rg-label">Class Color</label>
            <div class="color-options" id="colorOptions">
                <div class="color-option" style="background-color: #FF6B6B;" data-color="#FF6B6B"></div>
                <div class="color-option" style="background-color: #4ECDC4;" data-color="#4ECDC4"></div>
                <div class="color-option" style="background-color: #45B7D1;" data-color="#45B7D1"></div>
                <div class="color-option" style="background-color: #96CEB4;" data-color="#96CEB4"></div>
                <div class="color-option" style="background-color: #FFEAA7;" data-color="#FFEAA7"></div>
                <div class="color-option" style="background-color: #DDA0DD;" data-color="#DDA0DD"></div>
                <div class="color-option" style="background-color: #98D8C8;" data-color="#98D8C8"></div>
                <div class="color-option" style="background-color: #F7D794;" data-color="#F7D794"></div>
                <div class="color-option" style="background-color: #786FA6;" data-color="#786FA6"></div>
                <div class="color-option" style="background-color: #F3A683;" data-color="#F3A683"></div>
            </div>
            <input type="hidden" name="color" id="selectedColor" value="{{ old('color', $classroom->color ?? '#FF6B6B') }}">
        </div>

        <div class="rg-group">
            <label class="rg-label">Status <span class="req">*</span></label>
            <div class="status-options">
                <label class="status-option {{ old('status', $classroom->status) == 'active' ? 'selected' : '' }}">
                    <input type="radio" name="status" value="active" {{ old('status', $classroom->status) == 'active' ? 'checked' : '' }}>
                    <span><i class="fas fa-check-circle"></i> Active</span>
                </label>
                <label class="status-option {{ old('status', $classroom->status) == 'inactive' ? 'selected' : '' }}">
                    <input type="radio" name="status" value="inactive" {{ old('status', $classroom->status) == 'inactive' ? 'checked' : '' }}>
                    <span><i class="fas fa-times-circle"></i> Inactive</span>
                </label>
            </div>
            @error('status')<span class="invalid-msg">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- Description --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <span>📝</span> Description
        </div>

        <div>
            <label class="rg-label">Class Description</label>
            <textarea name="description" rows="3"
                placeholder="Describe the classroom, curriculum, activities, etc...">{{ old('description', $classroom->description) }}</textarea>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="rg-actions">
        <button type="submit" class="btn-update">
            <span><i class="fas fa-save"></i></span> Update Classroom
        </button>
        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn-cancel">
            <span><i class="fas fa-times"></i></span> Cancel
        </a>
    </div>

    </form>

    <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST" style="display: inline;"
        onsubmit="return confirm('<i class="fas fa-exclamation-triangle"></i> Are you sure you want to delete {{ $classroom->name }}?\n\nThis action cannot be undone and will affect all children in this class.')">
        @csrf
        @method('DELETE')
        <div class="rg-actions">
            <button type="submit" class="btn-delete">
                <i class="fas fa-trash-alt"></i> Delete Classroom
            </button>
        </div>
    </form>

</div>

<script>
    // Color selection
    const colorOptions = document.querySelectorAll('.color-option');
    const selectedColorInput = document.getElementById('selectedColor');

    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            colorOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedColorInput.value = this.dataset.color;
        });
        
        // Check if this color is selected
        if (option.dataset.color === selectedColorInput.value) {
            option.classList.add('selected');
        }
    });

    // Status option styling
    document.querySelectorAll('.status-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.status-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
</script>

@endsection