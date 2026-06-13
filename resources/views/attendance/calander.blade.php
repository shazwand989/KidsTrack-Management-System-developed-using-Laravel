@extends('layouts.template')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')

<style>
    .calendar-day {
        transition: all 0.15s;
        cursor: pointer;
        border-radius: 18px;
        background: white;
        min-height: 100px;
        padding: 12px;
        border: 1px solid #FFE4D6;
    }
    .calendar-day:hover:not(.empty-cell) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(255,107,107,0.12);
    }
    .present-day { background: #f0fdf9; border-left: 5px solid #22c55e; }
    .absent-day { background: #fff5f5; border-left: 5px solid #ef4444; }
    .holiday-day { background: #eff6ff; border-left: 5px solid #3b82f6; }
    .non-op-day { background: #f8fafc; border-left: 5px solid #94a3b8; opacity: 0.85; }
    .empty-cell { background: transparent !important; cursor: default; min-height: auto; border: none; }
    .today-ring { outline: 2px solid #FF6B6B; outline-offset: 2px; background: #fff3f0; }
    .btn-gradient { background: linear-gradient(135deg, #FF6B6B 0%, #FF9E7D 100%); }
    
    .toast-message {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #22c55e;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        z-index: 1000;
        display: none;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>

<div id="toast" class="toast-message">
    <span>✅</span> <span id="toastMsg"></span>
</div>

<div id="dayModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 w-full max-w-lg max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between border-b pb-3 mb-4">
            <h3 class="text-xl font-extrabold text-slate-800" id="modalTitle">—</h3>
            <button onclick="closeDayModal()" class="text-3xl text-slate-400 hover:text-red-500">&times;</button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                <span>📅</span> Attendance Calendar
            </h2>
            <p class="text-sm text-slate-400 mt-1" id="apiStatus">🌐 Loading holiday data...</p>
        </div>
        <div class="flex gap-3">
            <button onclick="refreshData()"
                class="bg-white border border-orange-200 text-[#FF6B6B] px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-50 transition">
                <span>🔄</span> Refresh
            </button>
            <div class="flex gap-1 bg-white rounded-xl shadow-md p-1.5">
                <button onclick="prevMonth()" class="w-9 h-9 rounded-lg hover:bg-orange-100"><span>◀</span></button>
                <span id="monthLabel" class="text-base font-extrabold px-3 min-w-[140px] text-center"></span>
                <button onclick="nextMonth()" class="w-9 h-9 rounded-lg hover:bg-orange-100"><span>▶</span></button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border-l-4 border-[#FF6B6B]">
            <div class="text-2xl mb-1">📅</div>
            <p class="text-2xl font-bold" id="statOpDays">—</p>
            <p class="text-xs text-slate-400">Operational Days</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border-l-4 border-green-400">
            <div class="text-2xl mb-1">👶</div>
            <p class="text-2xl font-bold text-green-600" id="statPresent">—</p>
            <p class="text-xs text-slate-400">Total Check-ins</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border-l-4 border-red-400">
            <div class="text-2xl mb-1">⏰</div>
            <p class="text-2xl font-bold text-red-500" id="statAbsent">—</p>
            <p class="text-xs text-slate-400">Absent</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border-l-4 border-amber-400">
            <div class="text-2xl mb-1">📊</div>
            <p class="text-2xl font-bold text-[#FF6B6B]" id="statRate">—</p>
            <p class="text-xs text-slate-400">Attendance Rate</p>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-2xl shadow-sm p-4 md:p-6">
        <div class="grid grid-cols-7 gap-1 md:gap-2 mb-3 border-b pb-2 text-center text-xs font-bold text-slate-400">
            <div>SUN</div><div>MON</div><div>TUE</div><div>WED</div><div>THU</div><div>FRI</div><div>SAT</div>
        </div>
        <div class="grid grid-cols-7 gap-1 md:gap-2" id="calendarGrid"></div>
    </div>

    <div class="flex flex-wrap gap-4 mt-4 text-xs text-slate-500 bg-white p-3 rounded-xl shadow-sm">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-100 border-l-2 border-green-500"></span> Has Check-ins</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-100 border-l-2 border-red-500"></span> No Check-ins</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-100 border-l-2 border-blue-500"></span> Holiday (API)</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-100 border-l-2 border-slate-500"></span> Weekend / Closed</span>
    </div>
</div>

<script>
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let allChildren = [];
    let allClassrooms = [];
    let attendanceMap = {};
    let holidays = {};

    const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    async function fetchData() {
        try {
            const response = await fetch(`/attendance/data?year=${currentYear}&month=${currentMonth+1}`);
            const data = await response.json();
            
            allChildren = data.children || [];
            allClassrooms = data.classrooms || [];
            
            attendanceMap = {};
            if (data.attendances) {
                data.attendances.forEach(att => {
                    const date = att.date;
                    if (!attendanceMap[date]) {
                        attendanceMap[date] = { present: 0, checkedInChildren: new Set() };
                    }
                    if (att.status === 'checkin') {
                        attendanceMap[date].checkedInChildren.add(att.child_id);
                        attendanceMap[date].present = attendanceMap[date].checkedInChildren.size;
                    }
                });
            }
            
            renderCalendar();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function getEnrolledChildren() {
        const classroomIds = new Set(allClassrooms.map(c => c.id));
        return allChildren.filter(child => child.classroom_id && classroomIds.has(child.classroom_id));
    }

    async function loadHolidays() {
        const statusEl = document.getElementById('apiStatus');
        statusEl.innerHTML = '<span>🔄</span> Loading holiday data...';
        
        try {
            const response = await fetch(`/api/holidays/${currentYear}`);
            const data = await response.json();
            if (data.success && data.holidays) {
                holidays = data.holidays;
                statusEl.innerHTML = `<span>✅</span> ${Object.keys(holidays).length} holidays loaded`;
            } else {
                holidays = {};
                statusEl.innerHTML = `<span>⚠️</span> Using manual holidays`;
            }
            renderCalendar();
        } catch (error) {
            holidays = {};
            statusEl.innerHTML = `<span>❌</span> Failed to load holidays`;
            renderCalendar();
        }
    }

    function renderCalendar() {
        const monthLabel = document.getElementById('monthLabel');
        if (monthLabel) monthLabel.innerHTML = `${MONTHS[currentMonth]} ${currentYear}`;
        
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();
        const grid = document.getElementById('calendarGrid');
        if (!grid) return;
        
        grid.innerHTML = '';
        
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'empty-cell';
            grid.appendChild(emptyCell);
        }
        
        let operationalDays = 0;
        let totalPresent = 0;
        let totalAbsent = 0;
        const enrolledChildren = getEnrolledChildren();
        const totalEnrolled = enrolledChildren.length;
        
        for (let day = 1; day <= daysInMonth; day++) {
            const dateKey = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const attendance = attendanceMap[dateKey] || { present: 0, checkedInChildren: new Set() };
            const isHoliday = !!holidays[dateKey];
            const dayOfWeek = new Date(currentYear, currentMonth, day).getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            const isOperational = !isHoliday && !isWeekend;
            
            let presentCount = 0;
            if (isOperational && totalEnrolled > 0) {
                for (const child of enrolledChildren) {
                    if (attendance.checkedInChildren.has(child.id)) presentCount++;
                }
                operationalDays++;
                totalPresent += presentCount;
                totalAbsent += totalEnrolled - presentCount;
            }
            
            const cell = document.createElement('div');
            cell.className = `calendar-day p-2 md:p-3 flex flex-col`;
            
            const isToday = (today.getDate() === day && today.getMonth() === currentMonth && today.getFullYear() === currentYear);
            if (isToday) cell.classList.add('today-ring');
            if (isHoliday) cell.classList.add('holiday-day');
            else if (isWeekend) cell.classList.add('non-op-day');
            else if (presentCount > 0) cell.classList.add('present-day');
            else if (isOperational && presentCount === 0) cell.classList.add('absent-day');
            
            let cellHTML = `<span class="text-sm font-black">${day}</span>`;
            if (isHoliday && holidays[dateKey]) {
                const holidayName = typeof holidays[dateKey] === 'object' ? holidays[dateKey].name : holidays[dateKey];
                cellHTML += `<div class="text-[10px] text-blue-600 font-bold mt-1">🎉 ${holidayName}</div>`;
            }
            if (isWeekend && !isHoliday) {
                cellHTML += `<div class="text-[10px] text-slate-400 mt-1">Weekend</div>`;
            }
            if (isOperational && totalEnrolled > 0) {
                const absentCount = totalEnrolled - presentCount;
                cellHTML += `<div class="mt-auto text-[10px] md:text-[11px] mt-1 md:mt-2">
                    <span class="text-green-600">✓ ${presentCount}</span>
                    <span class="text-red-400 ml-1 md:ml-2">✗ ${absentCount}</span>
                </div>`;
            }
            cell.innerHTML = cellHTML;
            cell.onclick = () => showDetailedAttendance(day, dateKey, isOperational, attendance, isHoliday, holidays[dateKey], isWeekend, totalEnrolled);
            grid.appendChild(cell);
        }
        
        document.getElementById('statOpDays').innerText = operationalDays;
        document.getElementById('statPresent').innerText = totalPresent;
        document.getElementById('statAbsent').innerText = totalAbsent;
        const rate = (totalPresent + totalAbsent) > 0 ? Math.round((totalPresent / (totalPresent + totalAbsent)) * 100) : 0;
        document.getElementById('statRate').innerText = `${rate}%`;
    }

    function showDetailedAttendance(day, dateKey, isOperational, attendance, isHoliday, holidayName, isWeekend, totalEnrolled) {
        const dateObj = new Date(currentYear, currentMonth, day);
        const title = dateObj.toLocaleDateString('en-MY', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('modalTitle').innerHTML = title;
        let content = '';
        
        if (isHoliday) {
            const holidayText = typeof holidayName === 'object' ? holidayName.name : holidayName;
            content += `<div class="bg-blue-100 p-3 rounded-xl mb-3">🎉 Public Holiday: ${holidayText}</div>`;
        }
        if (isWeekend && !isHoliday) {
            content += `<div class="bg-gray-100 p-3 rounded-xl mb-3">📅 Weekend - No operations</div>`;
        }
        
        if (isOperational && totalEnrolled > 0) {
            const enrolledChildren = getEnrolledChildren();
            const checkedInIds = attendance.checkedInChildren || new Set();
            
            const classroomMap = new Map();
            for (const classroom of allClassrooms) {
                classroomMap.set(classroom.id, {
                    name: classroom.name,
                    present: [],
                    absent: []
                });
            }
            
            for (const child of enrolledChildren) {
                const classroomId = child.classroom_id;
                const classData = classroomMap.get(classroomId);
                if (classData) {
                    if (checkedInIds.has(child.id)) {
                        classData.present.push(child);
                    } else {
                        classData.absent.push(child);
                    }
                }
            }
            
            let totalPresent = 0;
            for (const classData of classroomMap.values()) {
                totalPresent += classData.present.length;
            }
            
            content += `<div class="mb-4">
                <div class="text-center mb-3 pb-3 border-b">
                    <span class="text-2xl font-bold text-green-600">${totalPresent}</span>
                    <span class="text-slate-500"> / ${totalEnrolled} children present</span>
                </div>`;
            
            for (const [classroomId, classData] of classroomMap) {
                if (classData.present.length === 0 && classData.absent.length === 0) continue;
                content += `<div class="bg-gray-50 rounded-xl p-3 mb-3">
                    <div class="font-bold text-slate-700 mb-2"><span>🏫</span> ${classData.name}</div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="text-green-600"><span>✅</span> Present: ${classData.present.length}</div>
                        <div class="text-red-500"><span>❌</span> Absent: ${classData.absent.length}</div>
                    </div>`;
                if (classData.absent.length > 0) {
                    content += `<div class="mt-2 text-xs text-slate-500 border-t pt-2">
                        <span class="font-semibold">Absent:</span> ${classData.absent.map(c => c.name).join(', ')}
                    </div>`;
                }
                content += `</div>`;
            }
            content += `<button onclick="window.location.href='{{ route('attendance.create') }}'" 
                class="btn-gradient text-white w-full py-2.5 rounded-xl font-bold mt-2">
                <span>📝</span> Take Attendance
            </button>`;
        } else {
            content += `<p class="text-center text-slate-500 py-6">📅 Non-operational day. No attendance recorded.</p>`;
        }
        
        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('dayModal').classList.remove('hidden');
    }

    window.prevMonth = () => {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        fetchData();
        loadHolidays();
    };
    
    window.nextMonth = () => {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        fetchData();
        loadHolidays();
    };
    
    window.refreshData = () => { 
        fetchData(); 
        loadHolidays(); 
    };
    
    window.closeDayModal = () => { 
        document.getElementById('dayModal').classList.add('hidden'); 
    };

    // Initialize
    fetchData();
    loadHolidays();
</script>

@endsection