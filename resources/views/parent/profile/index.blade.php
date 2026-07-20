@extends('layouts.parent-template')

@section('content')
<div class="card" style="border-radius:20px;padding:20px;">
    <h4><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">person</i> My Profile</h4>
    <div style="margin-top:16px;">
        <p><strong>Name:</strong> {{ $parent->name ?? Auth::user()->name }}</p>
        <p><strong>Phone:</strong> {{ $parent->phone ?? Auth::user()->phone_number ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>Address:</strong> {{ $parent->address ?? Auth::user()->address ?? 'N/A' }}</p>
        @if($secondParent)
        <hr>
        <h5>Second Parent</h5>
        <p><strong>Name:</strong> {{ $secondParent->name }}</p>
        <p><strong>Phone:</strong> {{ $secondParent->phone ?? 'N/A' }}</p>
        @endif
        @if($guardian)
        <hr>
        <h5>Guardian</h5>
        <p><strong>Name:</strong> {{ $guardian->name }}</p>
        <p><strong>Phone:</strong> {{ $guardian->phone ?? 'N/A' }}</p>
        @endif
    </div>
</div>
@endsection
