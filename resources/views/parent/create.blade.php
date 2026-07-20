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

    .rg-section-title i { color: #FF9E7D; }

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

    .photo-circle i { font-size: 36px; color: white; }
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
    .upload-zone i { font-size: 16px; color: #FF9E7D; margin-bottom: 4px; display: block; }
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

    /* Email AJAX check feedback */
    .email-input-wrap { position: relative; }
    .email-input-wrap input { padding-right: 36px !important; }
    .email-feedback {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        display: none;
        pointer-events: none;
    }
    .email-feedback.checking { display: block; color: #94a3b8; animation: spin 0.8s linear infinite; }
    .email-feedback.available { display: block; color: #16a34a; }
    .email-feedback.taken { display: block; color: #dc2626; }
    .email-feedback-msg {
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
        display: none;
    }
    .email-feedback-msg.available { display: block; color: #16a34a; }
    .email-feedback-msg.taken { display: block; color: #dc2626; }
    @keyframes spin { from { transform: translateY(-50%) rotate(0deg); } to { transform: translateY(-50%) rotate(360deg); } }
</style>

<div class="rg-wrap">

    {{-- Breadcrumb --}}
    <div class="rg-breadcrumb">
        <a href="{{ route('parents.index') }}">Loving Guardians</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <strong>Register New Guardian</strong>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert-error">
        <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert-error">
        <strong><i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('parents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ============================================ --}}
    {{-- MAIN PARENT --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <i class="fas fa-user"></i> Main Parent <span class="req">*</span>
        </div>

        <div class="card-inner">
            <div class="card-photo-col">
                <div class="photo-circle" id="photoCircle1"
                    onclick="document.getElementById('photoFile1').click()">
                    <i class="fas fa-user"></i>
                </div>
                <div class="upload-zone"
                    onclick="document.getElementById('photoFile1').click()">
                    <i class="fas fa-camera"></i>
                    <p>Upload Photo</p>
                    <small>JPG/PNG · 2MB</small>
                </div>
                <input type="file" id="photoFile1" name="photo" accept="image/*">
            </div>

            <div>
                <div class="rg-group">
                    <label class="rg-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Mrs. Sarah bt Ali">
                    @error('name')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                {{-- EMAIL MAIN PARENT --}}
                <div class="rg-group">
                    <label class="rg-label">Email Address <span class="req">*</span></label>
                    <div class="email-input-wrap">
                        <input type="email" name="email" id="email_main" value="{{ old('email') }}"
                            placeholder="sarah@example.com" data-email-check="true">
                        <span class="email-feedback" id="email_main_fb"></span>
                    </div>
                    <span class="email-feedback-msg" id="email_main_msg"></span>
                    @error('email')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                {{-- PASSWORD MAIN PARENT --}}
                <div class="rg-group">
                    <label class="rg-label">Password <span class="req">*</span></label>
                    <input type="password" name="password"
                        placeholder="Min 8 characters">
                    @error('password')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Age</label>
                        <input type="text" name="age" value="{{ old('age') }}"
                            placeholder="e.g. 35">
                    </div>
                    <div>
                        <label class="rg-label">Phone Number <span class="req">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            placeholder="012-XXXXXXX">
                        @error('phone')<span class="invalid-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="rg-group">
                    <label class="rg-label">Home Address <span class="req">*</span></label>
                    <textarea name="address"
                        placeholder="e.g. No. 12, Jalan Mawar...">{{ old('address') }}</textarea>
                    @error('address')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECOND PARENT --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <i class="fas fa-user-friends"></i> Second Parent (Optional)
        </div>

        <div class="card-inner">
            <div class="card-photo-col">
                <div class="photo-circle" id="photoCircle2"
                    onclick="document.getElementById('photoFile2').click()">
                    <i class="fas fa-user"></i>
                </div>
                <div class="upload-zone"
                    onclick="document.getElementById('photoFile2').click()">
                    <i class="fas fa-camera"></i>
                    <p>Upload Photo</p>
                    <small>JPG/PNG · 2MB</small>
                </div>
                <input type="file" id="photoFile2" name="second_photo" accept="image/*">
            </div>

            <div>
                {{-- EMAIL SECOND PARENT --}}
                <div class="rg-group">
                    <label class="rg-label">Email Address</label>
                    <div class="email-input-wrap">
                        <input type="email" name="second_email" id="email_second" value="{{ old('second_email') }}"
                            placeholder="second@example.com" data-email-check="true">
                        <span class="email-feedback" id="email_second_fb"></span>
                    </div>
                    <span class="email-feedback-msg" id="email_second_msg"></span>
                    @error('second_email')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                {{-- PASSWORD SECOND PARENT --}}
                <div class="rg-group">
                    <label class="rg-label">Password</label>
                    <input type="password" name="second_password"
                        placeholder="Min 8 characters">
                    @error('second_password')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Full Name</label>
                        <input type="text" name="second_name" value="{{ old('second_name') }}"
                            placeholder="Full name">
                    </div>
                    <div>
                        <label class="rg-label">Age</label>
                        <input type="text" name="second_age" value="{{ old('second_age') }}"
                            placeholder="e.g. 33">
                    </div>
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Phone Number</label>
                        <input type="text" name="second_phone" value="{{ old('second_phone') }}"
                            placeholder="013-XXXXXXX">
                    </div>
                    <div>
                        <label class="rg-label">Address</label>
                        <input type="text" name="second_address" value="{{ old('second_address') }}"
                            placeholder="Address">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GUARDIAN --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <i class="fas fa-shield-alt"></i> Guardian (Optional)
        </div>

        <div class="card-inner">
            <div class="card-photo-col">
                <div class="photo-circle" id="photoCircle3"
                    onclick="document.getElementById('photoFile3').click()">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="upload-zone"
                    onclick="document.getElementById('photoFile3').click()">
                    <i class="fas fa-camera"></i>
                    <p>Upload Photo</p>
                    <small>JPG/PNG · 2MB</small>
                </div>
                <input type="file" id="photoFile3" name="guardian_photo" accept="image/*">
            </div>

            <div>
                {{-- EMAIL GUARDIAN --}}
                <div class="rg-group">
                    <label class="rg-label">Email Address</label>
                    <div class="email-input-wrap">
                        <input type="email" name="guardian_email" id="email_guardian" value="{{ old('guardian_email') }}"
                            placeholder="guardian@example.com" data-email-check="true">
                        <span class="email-feedback" id="email_guardian_fb"></span>
                    </div>
                    <span class="email-feedback-msg" id="email_guardian_msg"></span>
                    @error('guardian_email')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                {{-- PASSWORD GUARDIAN --}}
                <div class="rg-group">
                    <label class="rg-label">Password</label>
                    <input type="password" name="guardian_password"
                        placeholder="Min 8 characters">
                    @error('guardian_password')<span class="invalid-msg">{{ $message }}</span>@enderror
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Full Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}"
                            placeholder="Guardian name">
                    </div>
                    <div>
                        <label class="rg-label">Age</label>
                        <input type="text" name="guardian_age" value="{{ old('guardian_age') }}"
                            placeholder="e.g. 45">
                    </div>
                </div>

                <div class="rg-2col">
                    <div>
                        <label class="rg-label">Phone Number</label>
                        <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}"
                            placeholder="011-XXXXXXX">
                    </div>
                    <div>
                        <label class="rg-label">Address</label>
                        <input type="text" name="guardian_address" value="{{ old('guardian_address') }}"
                            placeholder="Address">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SPECIAL SETTINGS --}}
    {{-- ============================================ --}}
    <div class="rg-card">
        <div class="rg-section-title">
            <i class="fas fa-sliders-h"></i> Special Settings
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <label class="check-row">
                {{-- HIDDEN INPUT UNTUK FALSE VALUE --}}
                <input type="hidden" name="verified" value="0">
                <input type="checkbox" name="verified" value="1" {{ old('verified') ? 'checked' : '' }}>
                <div class="check-row-text">
                    <p><i class="fas fa-check-circle" style="font-size:10px;"></i> Verified</p>
                    <small>Identity has been confirmed</small>
                </div>
            </label>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div class="rg-actions">
        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Save Parent
        </button>
        <a href="{{ route('parents.index') }}" class="btn-cancel">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>

    </form>

</div>

<script>
    // 🔥 CLIENT-SIDE VALIDATION BEFORE SUBMIT
    const form = document.querySelector('form[action*="parents"]');
    const allInputs = form.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([type="checkbox"]), textarea');

    // Clear validation styling on input
    allInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            const errEl = this.parentElement.querySelector('.client-err');
            if (errEl) errEl.remove();
        });
    });

    form.addEventListener('submit', function(e) {
        let hasError = false;
        const requiredFields = [
            { name: 'name', label: 'Full Name' },
            { name: 'email', label: 'Email Address' },
            { name: 'password', label: 'Password' },
            { name: 'phone', label: 'Phone Number' },
            { name: 'address', label: 'Home Address' }
        ];

        // Clear previous errors
        form.querySelectorAll('.client-err').forEach(el => el.remove());
        form.querySelectorAll('input, textarea').forEach(el => el.style.borderColor = '');

        // Check required fields
        requiredFields.forEach(field => {
            const input = form.querySelector(`[name="${field.name}"]`);
            if (!input) return;
            const val = input.value.trim();

            if (!val) {
                showError(input, `${field.label} is required`);
                hasError = true;
            } else if (field.name === 'password' && val.length < 8) {
                showError(input, 'Password must be at least 8 characters');
                hasError = true;
            } else if (field.name === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                showError(input, 'Please enter a valid email');
                hasError = true;
            }
        });

        // Check email availability feedback
        ['email_main', 'email_second', 'email_guardian'].forEach(id => {
            const fb = document.getElementById(id + '_fb');
            if (fb && fb.classList.contains('taken')) {
                const input = document.getElementById(id);
                showError(input, 'This email is already taken');
                hasError = true;
            }
        });

        // Optional: validate second parent email if filled
        const secondEmail = form.querySelector('[name="second_email"]');
        if (secondEmail && secondEmail.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(secondEmail.value.trim())) {
            showError(secondEmail, 'Please enter a valid email');
            hasError = true;
        }

        // Optional: validate guardian email if filled
        const guardianEmail = form.querySelector('[name="guardian_email"]');
        if (guardianEmail && guardianEmail.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(guardianEmail.value.trim())) {
            showError(guardianEmail, 'Please enter a valid email');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            // Scroll to first error
            const firstErr = form.querySelector('.client-err');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function showError(input, message) {
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        const err = document.createElement('span');
        err.className = 'client-err';
        err.style.cssText = 'font-size:12px;color:#ef4444;font-weight:700;margin-top:4px;display:block;';
        err.textContent = message;
        input.parentElement.appendChild(err);
    }

    // 🔥 AJAX EMAIL AVAILABILITY CHECK (with debounce)
    const checkEmailRoute = "{{ route('parents.check-email') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const debounceTimers = {};

    document.querySelectorAll('input[data-email-check]').forEach(input => {
        input.addEventListener('input', function() {
            const email = this.value.trim();
            const id = this.id;
            const fb = document.getElementById(id + '_fb');
            const msg = document.getElementById(id + '_msg');

            if (!email || !email.includes('@')) {
                fb.className = 'email-feedback';
                msg.className = 'email-feedback-msg';
                msg.textContent = '';
                return;
            }

            fb.innerHTML = '&#8635;';
            fb.className = 'email-feedback checking';
            msg.className = 'email-feedback-msg';

            clearTimeout(debounceTimers[id]);
            debounceTimers[id] = setTimeout(() => {
                fetch(checkEmailRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.available) {
                        fb.innerHTML = '&#10003;';
                        fb.className = 'email-feedback available';
                        msg.textContent = '✓ Email available';
                        msg.className = 'email-feedback-msg available';
                    } else {
                        fb.innerHTML = '&#10007;';
                        fb.className = 'email-feedback taken';
                        msg.textContent = '✗ ' + data.message;
                        msg.className = 'email-feedback-msg taken';
                    }
                })
                .catch(() => {
                    fb.className = 'email-feedback';
                    msg.className = 'email-feedback-msg';
                });
            }, 500);
        });
    });

    function setupPhotoPreview(inputId, circleId) {
        document.getElementById(inputId).addEventListener('change', function () {
            if (!this.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById(circleId).innerHTML =
                    `<img src="${e.target.result}">`;
            };
            reader.readAsDataURL(this.files[0]);
        });
    }

    setupPhotoPreview('photoFile1', 'photoCircle1');
    setupPhotoPreview('photoFile2', 'photoCircle2');
    setupPhotoPreview('photoFile3', 'photoCircle3');
</script>

@endsection
