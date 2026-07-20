@extends('layouts.template')

@section('title', 'Take Attendance')
@section('page-title', 'Take Attendance')

@section('content')

<style>
    .attendance-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .date-card {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        border-radius: 20px;
        padding: 20px 25px;
        color: white;
        margin-bottom: 24px;
    }

    .date-card h3 {
        color: white;
        margin-bottom: 5px;
    }

    .date-card p {
        color: rgba(255,255,255,0.9);
        margin-bottom: 0;
    }

    .children-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .child-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border: 1px solid #FFE4D6;
        transition: transform .2s;
    }

    .child-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255,107,107,0.12);
    }

    .child-card-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 18px;
        background: #FFF5F2;
        border-bottom: 1px solid #FFE4D6;
    }

    .child-avatar {
        width: 55px;
        height: 55px;
        border-radius: 16px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 20px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .child-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .child-info h4 {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .child-info p {
        font-size: 12px;
        color: #94a3b8;
        margin: 0;
    }

    .child-card-body {
        padding: 18px;
    }

    .status-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 15px;
    }

    .btn-status {
        flex: 1;
        border: none;
        padding: 10px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-checkin {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .btn-checkin:hover, .btn-checkin.active {
        background: #16a34a;
        color: white;
        border-color: #16a34a;
    }

    .btn-checkout {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .btn-checkout:hover, .btn-checkout.active {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
    }

    .btn-absent {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .btn-absent:hover, .btn-absent.active {
        background: #d97706;
        color: white;
        border-color: #d97706;
    }

    .time-info {
        background: #f8fafc;
        border-radius: 14px;
        padding: 12px;
        margin-top: 12px;
        font-size: 12px;
    }

    .time-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .time-label {
        color: #64748b;
        font-weight: 600;
    }

    .time-value {
        font-weight: 700;
        color: #1e293b;
    }

    .parent-input {
        margin-top: 12px;
    }

    .parent-input label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 5px;
        display: block;
    }

    .parent-input input {
        width: 100%;
        border: 1px solid #FFE4D6;
        border-radius: 12px;
        padding: 8px 12px;
        font-size: 12px;
    }

    .save-btn {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 16px;
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255,107,107,0.3);
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
        color: #16a34a;
        font-weight: 700;
        display: none;
    }

    .class-filter {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: white;
        border: 1px solid #FFE4D6;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .filter-btn:hover, .filter-btn.active {
        background: #FF6B6B;
        color: white;
        border-color: #FF6B6B;
    }
</style>

<div class="attendance-container">

    {{-- Date Card --}}
    <div class="date-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>📋 Take Attendance</h3>
                <p id="currentDate"></p>
            </div>
            <div>
                <button onclick="saveAllAttendance()" class="save-btn">
                    <span>💾</span> Save All Changes
                </button>
            </div>
        </div>
    </div>

    {{-- Success Alert --}}
    <div id="successAlert" class="alert-success">
        <i class="fas fa-check-circle"></i> <span id="successMsg">Attendance saved successfully!</span>
    </div>

    {{-- Class Filter --}}
    <div class="class-filter">
        <button class="filter-btn active" onclick="filterByClass('all')">All Classes</button>
        @foreach($classrooms as $classroom)
            <button class="filter-btn" onclick="filterByClass({{ $classroom->id }})">
                🏫 {{ $classroom->name }}
            </button>
        @endforeach
    </div>

    {{-- Children Grid --}}
    <div class="children-grid" id="childrenGrid">
        @foreach($children as $child)
        <div class="child-card" data-classroom="{{ $child->classroom_id }}">
            <div class="child-card-header">
                <div class="child-avatar">
                    @if($child->photo)
                        <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                    @else
                        {{ strtoupper(substr($child->name, 0, 1)) }}
                    @endif
                </div>
                <div class="child-info">
                    <h4>{{ $child->name }}</h4>
                    <p>
                        @if($child->classroom)
                            🏫 {{ $child->classroom->name }}
                        @else
                            No classroom assigned
                        @endif
                        | 👶 {{ $child->age }} years
                    </p>
                </div>
            </div>
            <div class="child-card-body">
                <div class="status-buttons">
                    <button class="btn-status btn-checkin" onclick="setStatus({{ $child->id }}, 'checkin')">
                        <i class="fas fa-check-circle"></i> Check-in
                    </button>
                    <button class="btn-status btn-checkout" onclick="setStatus({{ $child->id }}, 'checkout')">
                        <span>📤</span> Check-out
                    </button>
                    <button class="btn-status btn-absent" onclick="setStatus({{ $child->id }}, 'absent')">
                        <span>❌</span> Absent
                    </button>
                </div>

                <div class="time-info" id="timeInfo-{{ $child->id }}" style="display: none;">
                    <div class="time-info-row">
                        <span class="time-label">⏰ Check-in:</span>
                        <span class="time-value" id="checkinTime-{{ $child->id }}">—</span>
                    </div>
                    <div class="time-info-row">
                        <span class="time-label">📤 Check-out:</span>
                        <span class="time-value" id="checkoutTime-{{ $child->id }}">—</span>
                    </div>
                </div>

                <div class="parent-input" id="parentInput-{{ $child->id }}" style="display: none;">
                    <label>👨‍👩‍👧 Drop off / Pickup by:</label>
                    <input type="text" id="parentName-{{ $child->id }}" placeholder="Parent/Guardian name">
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($children->count() == 0)
        <div class="text-center py-8">
            <div class="empty-icon">👶</div>
            <h5>No children registered yet</h5>
            <p>Please register children first before taking attendance.</p>
            <a href="{{ route('children.create') }}" class="btn-register">➕ Register Child</a>
        </div>
    @endif

</div>

<script>
    // Store attendance data
    let attendanceData = {};

    // Initialize date display
    function initDate() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').innerHTML = now.toLocaleDateString('en-MY', options);
    }

    // Set status for a child
    function setStatus(childId, status) {
        // Remove active class from all buttons for this child
        const childCard = document.querySelector(`.child-card`).parentElement;
        const btns = document.querySelectorAll(`.btn-status`);
        
        // Store status
        attendanceData[childId] = {
            status: status,
            checkin_time: status === 'checkin' ? new Date().toLocaleTimeString() : (attendanceData[childId]?.checkin_time || null),
            checkout_time: status === 'checkout' ? new Date().toLocaleTimeString() : (attendanceData[childId]?.checkout_time || null),
            parent_name: document.getElementById(`parentName-${childId}`)?.value || ''
        };

        // Update UI
        updateChildUI(childId, status);
    }

    function updateChildUI(childId, status) {
        const timeInfo = document.getElementById(`timeInfo-${childId}`);
        const parentInput = document.getElementById(`parentInput-${childId}`);
        const checkinTimeSpan = document.getElementById(`checkinTime-${childId}`);
        const checkoutTimeSpan = document.getElementById(`checkoutTime-${childId}`);

        if (status === 'checkin') {
            timeInfo.style.display = 'block';
            parentInput.style.display = 'block';
            checkinTimeSpan.innerHTML = new Date().toLocaleTimeString();
            document.getElementById(`parentName-${childId}`).focus();
        } else if (status === 'checkout') {
            timeInfo.style.display = 'block';
            parentInput.style.display = 'block';
            checkoutTimeSpan.innerHTML = new Date().toLocaleTimeString();
        } else if (status === 'absent') {
            timeInfo.style.display = 'none';
            parentInput.style.display = 'none';
        }
    }

    // Save all attendance
    async function saveAllAttendance() {
        // Collect parent names
        for (let childId in attendanceData) {
            const parentInput = document.getElementById(`parentName-${childId}`);
            if (parentInput) {
                attendanceData[childId].parent_name = parentInput.value;
            }
        }

        try {
            const response = await fetch('{{ route("attendance.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    attendances: attendanceData,
                    date: new Date().toISOString().split('T')[0]
                })
            });

            const result = await response.json();
            
            if (result.success) {
                showAlert('Attendance saved successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('Error saving attendance', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error saving attendance', 'error');
        }
    }

    function showAlert(msg, type) {
        const alertDiv = document.getElementById('successAlert');
        const msgSpan = document.getElementById('successMsg');
        msgSpan.innerHTML = msg;
        alertDiv.style.display = 'block';
        if (type === 'error') {
            alertDiv.style.background = '#fef2f2';
            alertDiv.style.color = '#dc2626';
            alertDiv.style.borderColor = '#fecaca';
        } else {
            alertDiv.style.background = '#f0fdf4';
            alertDiv.style.color = '#16a34a';
            alertDiv.style.borderColor = '#bbf7d0';
        }
        setTimeout(() => alertDiv.style.display = 'none', 3000);
    }

    // Filter by classroom
    function filterByClass(classroomId) {
        const cards = document.querySelectorAll('.child-card');
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        cards.forEach(card => {
            if (classroomId === 'all') {
                card.style.display = 'block';
            } else {
                if (card.dataset.classroom == classroomId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    }

    // Initialize
    initDate();
</script>

@endsection