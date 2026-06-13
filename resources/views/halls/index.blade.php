@extends('layouts.template')

@section('title', 'Hall List')
@section('page-title', 'Hall List')

@section('content')

<style>
    .hall-page {
        padding-top: 5px;
    }

    .hall-header-card {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .hall-header-card::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .hall-header-card h3,
    .hall-header-card p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .hall-header-icon {
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

    .hall-header-icon i {
        color: white;
        font-size: 38px;
    }

    .btn-add-hall {
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

    .btn-add-hall:hover {
        background: #fce7f3;
        color: #9d174d !important;
    }

    .hall-table-card {
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .hall-table-card .card-header {
        background: white;
        padding: 24px 26px 18px 26px;
        border-bottom: 1px solid #fce7f3;
    }

    .hall-table-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .hall-table-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .hall-badge {
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

    .hall-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .hall-table thead th {
        background: #fce7f3;
        color: #9d174d;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 900;
        border: none;
        padding: 16px 18px;
        white-space: nowrap;
    }

    .hall-table tbody tr {
        border-bottom: 1px solid #fce7f3;
        transition: 0.2s ease;
    }

    .hall-table tbody tr:hover {
        background: #fff7fb;
    }

    .hall-table tbody td {
        padding: 16px 18px;
        vertical-align: middle;
        color: #4b5563;
        border: none;
        background: transparent;
    }

    .hall-no {
        font-weight: 700;
        color: #be185d;
    }

    .hall-avatar {
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

    .hall-name {
        color: #111827;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .hall-id {
        color: #9ca3af;
        font-size: 12px;
    }

    .hall-place {
        background: #fce7f3;
        color: #be185d;
        padding: 8px 12px;
        border-radius: 14px;
        font-weight: 700;
        display: inline-block;
    }

    .empty-text {
        color: #9ca3af;
        font-style: italic;
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

    @media (max-width: 768px) {
        .hall-table thead th,
        .hall-table tbody td {
            padding: 14px 12px;
            font-size: 13px;
        }

        .action-btn {
            width: 34px;
            height: 34px;
        }
    }
</style>

<div class="hall-page">

    @if (session('success'))
        <div class="success-alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="hall-header-card">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="hall-header-icon">
                        <i class="material-symbols-rounded">apartment</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Lecture Hall Records</h3>
                        <p class="mb-0">View, update, and manage lecture hall names and locations.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-end">
                <a href="{{ route('halls.create') }}" class="btn-add-hall">
                    <i class="material-symbols-rounded" style="font-size:20px;">add_business</i>
                    Add New Hall
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card hall-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="hall-table-title">Manage Lecture Halls</h4>
                <p class="hall-table-subtitle">List of all lecture hall records stored in the system.</p>
            </div>

            <span class="hall-badge">
                Total: {{ $halls->count() }} Halls
            </span>
        </div>

        <div class="table-responsive">
            <table class="table hall-table align-items-center">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Hall Name</th>
                        <th>Hall Place</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($halls as $hall)
                        <tr>
                            <td>
                                <span class="hall-no">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="hall-avatar">
                                        {{ strtoupper(substr($hall->lecture_hall_name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="hall-name">{{ $hall->lecture_hall_name }}</div>
                                        <div class="hall-id">Hall ID: {{ $hall->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if(!empty($hall->lecture_hall_place))
                                    <span class="hall-place">{{ $hall->lecture_hall_place }}</span>
                                @else
                                    <span class="empty-text">Not provided</span>
                                @endif
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('halls.show', $hall->id) }}"
                                       class="action-btn btn-view"
                                       title="View Hall">
                                        <i class="material-symbols-rounded">visibility</i>
                                    </a>

                                    <a href="{{ route('halls.edit', $hall->id) }}"
                                       class="action-btn btn-edit"
                                       title="Edit Hall">
                                        <i class="material-symbols-rounded">edit</i>
                                    </a>

                                    <form action="{{ route('halls.destroy', $hall->id) }}"
                                          method="POST"
                                          class="delete-form"
                                          onsubmit="return confirm('Delete this hall?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="action-btn btn-delete"
                                                title="Delete Hall">
                                            <i class="material-symbols-rounded">delete</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <span class="empty-text">No hall records found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection