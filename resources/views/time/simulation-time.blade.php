<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation Time</title>
</head>
<body>

<h1>Simulation Time Setting</h1>

@if(session('success'))
    <p style="color:green;">
        {{ session('success') }}
    </p>
@endif

<form action="{{ url('/simulation-time') }}" method="POST">
    @csrf

    <table border="1" cellpadding="10">

        <tr>
            <td>Simulation Date & Time</td>
            <td>
                <input
                    type="datetime-local"
                    name="simulation_time"
                    value="{{ $clock ? \Carbon\Carbon::parse($clock->simulation_time)->format('Y-m-d\TH:i') : '' }}"
                    required>
            </td>
        </tr>

        <tr>
            <td>Morning Check In Time</td>
            <td>
                <input
                    type="time"
                    name="morning_checkin"
                    value="{{ $clock ? $clock->morning_checkin : '07:30' }}">
            </td>
        </tr>

        <tr>
            <td>Evening Check Out Time</td>
            <td>
                <input
                    type="time"
                    name="evening_checkout"
                    value="{{ $clock ? $clock->evening_checkout : '16:00' }}">
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <button type="submit">
                    Save Setting
                </button>
            </td>
        </tr>

    </table>

</form>

<hr>

<h2>Current Setting</h2>

@if($clock)

<p>
<b>Simulation Time :</b>

{{ \Carbon\Carbon::parse($clock->simulation_time)->format('d M Y h:i A') }}
</p>

<p>
<b>Morning Check In :</b>

{{ \Carbon\Carbon::parse($clock->morning_checkin)->format('h:i A') }}
</p>

<p>
<b>Evening Check Out :</b>

{{ \Carbon\Carbon::parse($clock->evening_checkout)->format('h:i A') }}
</p>

@php

$hour = \Carbon\Carbon::parse($clock->simulation_time)->hour;

@endphp

@if($hour < 12)

<h2>
☀️ Good Morning Admin
</h2>

@else

<h2>
🌤 Good Afternoon Admin
</h2>

@endif

@endif

<hr>

<a href="{{ url('/dashboard') }}">
Back To Dashboard
</a>

</body>
</html>