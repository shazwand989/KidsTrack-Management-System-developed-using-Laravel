@extends('layouts.template')

@section('title', 'Day List')
@section('page-title', 'Day List')

@section('content')

<style>
    .day-page {
        padding-top: 5px;
    }

    .day-header-card {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .day-header-card::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .day-header-card h3,
    .day-header-card p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .day-header-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 18px;
        position: relative;
        z-index: 2;
    }

    .day-header-icon i {
        color: white;
        font-size: 38px;
    }

    .btn-add-day {
        background: white;
        color: #be185d !important;
        border: none;
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-add-day:hover {
        background: #fce7f3;
        color: #9d174d !important;
    }

    .day-table-card {
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .day-table-card .card-header {
        background: white;
        padding: 24px 26px 18px 26px;
        border-bottom: 1px solid #fce7f3;
    }

    .day-table-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .day-table-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .day-badge {
        background: #fce7f3;
        color: #be185d;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 800;
        display: inline-block;
    }

    .success-alert {
        background: #dcfce7;
        color: #166534;
        border: none;
        border-radius: 14px;
        padding: 14px 18px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .table-responsive {
        padding: 0;
    }

    .day-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .day-table thead th {
        background: #fce7f3;
        color: #9d174d;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 900;
        border: none;
        padding: 16px 18px;
        white-space: nowrap;
    }

    .day-table tbody tr {
        border-bottom: 1px solid #fce7f3;
        transition: 0.2s ease;
    }

    .day-table tbody tr:hover {
        background: #fff7fb;
    }

    .day-table tbody td {
        padding: 16px 18px;
        vertical-align: middle;
        color: #4b5563;
        border: none;
        background: transparent;
    }

    .day-no {
        font-weight: 700;
        color: #be185d;
    }

    .day-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #c026d3, #ec4899);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        margin-right: 12px;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .day-name {
        color: #111827;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .day-id {
        color: #9ca3af;
        font-size: 12px;
    }

    .action-group {
        display: flex;
        gap: 7px;
        align-items: center;
        flex-wrap: nowrap;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.2s ease;
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.10);
    }

    .action-btn i {
        color: white !important;
        font-size: 19px;
    }

    .btn-view {
        background: linear-gradient(135deg, #2563eb, #38bdf8);
    }

    .btn-edit {
        background: linear-gradient(135deg, #f97316, #f59e0b);
    }

    .btn-delete {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    .delete-form {
        margin: 0;
        display: inline;
    }

    .empty-text {
        color: #9ca3af;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .day-table thead th,
        .day-table tbody td {
            padding: 14px 12px;
            font-size: 13px;
        }

        .action-btn {
            width: 34px;
            height: 34px;
        }
    }
</style>

<div class="day-page">

    @if (session('success'))
        <div class="success-alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="day-header-card">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="day-header-icon">
                        <i class="material-symbols-rounded">today</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Academic Day Records</h3>
                        <p class="mb-0">View, update, and manage day records used for timetable scheduling.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-end">
                <a href="{{ route('days.create') }}" class="btn-add-day">
                    <i class="material-symbols-rounded" style="font-size:20px;">add</i>
                    Add New Day
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card day-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="day-table-title">Manage Days</h4>
                <p class="day-table-subtitle">List of all academic day records stored in the system.</p>
            </div>

            <span class="day-badge">
                Total: {{ $days->count() }} Days
            </span>
        </div>

        <div class="table-responsive">
            <table class="table day-table align-items-center">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Day Name</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($days as $day)
                        <tr>
                            <td>
                                <span class="day-no">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="day-avatar">
                                        {{ strtoupper(substr($day->day_name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="day-name">{{ $day->day_name }}</div>
                                        <div class="day-id">Day ID: {{ $day->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('days.show', $day->id) }}"
                                       class="action-btn btn-view"
                                       title="View Day">
                                        <i class="material-symbols-rounded">visibility</i>
                                    </a>

                                    <a href="{{ route('days.edit', $day->id) }}"
                                       class="action-btn btn-edit"
                                       title="Edit Day">
                                        <i class="material-symbols-rounded">edit</i>
                                    </a>

                                    <form action="{{ route('days.destroy', $day->id) }}"
                                          method="POST"
                                          class="delete-form"
                                          onsubmit="return confirmDelete(this, 'Delete this day?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="action-btn btn-delete"
                                                title="Delete Day">
                                            <i class="material-symbols-rounded">delete</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <span class="empty-text">No day records found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection