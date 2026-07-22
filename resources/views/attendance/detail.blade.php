@extends('layouts.template')

@section('title', 'Attendance Detail')
@section('page-title', 'Attendance Detail')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Attendance Detail - {{ $date }}</h3>
            <a href="{{ route('attendance.calendar') }}" class="btn btn-back">⬅️ Back to Calendar</a>
        </div>

        <div class="stats">
            <div class="stat"><span class="num" style="color:#16a34a;">{{ $present }}</span> <i class="fas fa-check-circle"></i> Present</div>
            <div class="stat"><span class="num" style="color:#dc2626;">{{ $late }}</span> ⏰ Late</div>
            <div class="stat"><span class="num" style="color:#2563eb;">{{ $checkout }}</span> <i class="fas fa-hand-wave"></i> Checkout</div>
            <div class="stat"><span class="num" style="color:#d97706;">{{ $absent }}</span> <i class="fas fa-times-circle"></i> Absent</div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Classroom</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $index => $att)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $att->child->name ?? 'Unknown' }}</td>
                        <td>{{ $att->child->classroom->name ?? '-' }}</td>
                        <td>
                            @if($att->status == 'present' || $att->status == 'checkin')
                                <span style="color:#16a34a;"><i class="fas fa-check-circle"></i> Present</span>
                            @elseif($att->status == 'late')
                                <span style="color:#dc2626;">⏰ Late</span>
                            @elseif($att->status == 'checkout')
                                <span style="color:#2563eb;"><i class="fas fa-hand-wave"></i> Checkout</span>
                            @else
                                <span style="color:#d97706;"><i class="fas fa-times-circle"></i> Absent</span>
                            @endif
                        </td>
                        <td>{{ $att->checkin_time ?? $att->checkout_time ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No attendance records for this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
    .card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 14px rgba(0,0,0,0.05); }
    .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
    .card-header h3 { font-size: 20px; font-weight: 700; color: #1f2937; }
    .btn-back { padding: 8px 20px; background: #6b7280; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; }
    .btn-back:hover { background: #4b5563; }
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-bottom: 20px; }
    .stat { background: #f8fafc; padding: 12px; border-radius: 10px; text-align: center; border: 1px solid #e5e7eb; }
    .stat .num { font-size: 24px; font-weight: 800; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .table th { background: #f8fafc; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 12px; }
    .table tr:hover { background: #f9fafb; }
</style>

@endsection