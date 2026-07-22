@extends('layouts.template')

@section('title', 'Subject List')
@section('page-title', 'Subject List')

@section('content')

<style>
    .subject-page {
        padding-top: 5px;
    }

    .subject-header-card {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .subject-header-card::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .subject-header-card h3,
    .subject-header-card p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .subject-header-icon {
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

    .subject-header-icon i {
        color: white;
        font-size: 38px;
    }

    .btn-add-subject {
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

    .btn-add-subject:hover {
        background: #fce7f3;
        color: #9d174d !important;
    }

    .subject-table-card {
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 12px 30px rgba(91, 33, 182, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .subject-table-card .card-header {
        background: white;
        padding: 24px 26px 18px 26px;
        border-bottom: 1px solid #fce7f3;
    }

    .subject-table-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .subject-table-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .subject-badge {
        background: #fce7f3;
        color: #be185d;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 800;
        display: inline-block;
    }

    .table-responsive {
        padding: 0;
    }

    .subject-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .subject-table thead th {
        background: #fce7f3;
        color: #9d174d;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 900;
        border: none;
        padding: 16px 18px;
        white-space: nowrap;
    }

    .subject-table tbody tr {
        border-bottom: 1px solid #fce7f3;
        transition: 0.2s ease;
    }

    .subject-table tbody tr:hover {
        background: #fff7fb;
    }

    .subject-table tbody td {
        padding: 16px 18px;
        vertical-align: middle;
        color: #4b5563;
        border: none;
        background: transparent;
    }

    .subject-no {
        font-weight: 700;
        color: #be185d;
    }

    .subject-code-badge {
        background: #fce7f3;
        color: #be185d;
        padding: 8px 12px;
        border-radius: 14px;
        font-weight: 900;
        display: inline-block;
        min-width: 70px;
        text-align: center;
    }

    .subject-avatar {
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

    .subject-name {
        color: #111827;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .subject-label {
        color: #9ca3af;
        font-size: 12px;
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

    .success-alert {
        background: #dcfce7;
        color: #166534;
        border: none;
        border-radius: 14px;
        padding: 14px 18px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .subject-table thead th,
        .subject-table tbody td {
            padding: 14px 12px;
            font-size: 13px;
        }

        .action-btn {
            width: 34px;
            height: 34px;
        }
    }
</style>

<div class="subject-page">

    @if (session('success'))
        <div class="success-alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="subject-header-card">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="subject-header-icon">
                        <i class="material-symbols-rounded">menu_book</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Subject Records</h3>
                        <p class="mb-0">View, update, and manage subject information and assigned lecturers.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-end">
                <a href="{{ route('subjects.create') }}" class="btn-add-subject">
                    <i class="material-symbols-rounded" style="font-size:20px;">add</i>
                    Add New Subject
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card subject-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="subject-table-title">Manage Subjects</h4>
                <p class="subject-table-subtitle">List of all subject records stored in the system.</p>
            </div>

            <span class="subject-badge">
                Total: {{ $subjects->count() }} Subjects
            </span>
        </div>

        <div class="table-responsive">
            <table class="table subject-table align-items-center">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Lecturer Name</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($subjects as $subject)
                        <tr>
                            <td>
                                <span class="subject-no">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <span class="subject-code-badge">
                                    {{ $subject->subject_code }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="subject-avatar">
                                        {{ strtoupper(substr($subject->subject_name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="subject-name">{{ $subject->subject_name }}</div>
                                        <div class="subject-label">Subject ID: {{ $subject->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if(!empty($subject->lecturer_name))
                                    {{ $subject->lecturer_name }}
                                @else
                                    <span class="empty-text">Not assigned</span>
                                @endif
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('subjects.show', $subject->id) }}"
                                       class="action-btn btn-view"
                                       title="View Subject">
                                        <i class="material-symbols-rounded">visibility</i>
                                    </a>

                                    <a href="{{ route('subjects.edit', $subject->id) }}"
                                       class="action-btn btn-edit"
                                       title="Edit Subject">
                                        <i class="material-symbols-rounded">edit</i>
                                    </a>

                                    <form action="{{ route('subjects.destroy', $subject->id) }}"
                                          method="POST"
                                          class="delete-form"
                                          onsubmit="return confirmDelete(this, 'Delete this subject?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="action-btn btn-delete"
                                                title="Delete Subject">
                                            <i class="material-symbols-rounded">delete</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <span class="empty-text">No subject records found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection