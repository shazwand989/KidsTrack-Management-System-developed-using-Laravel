<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

@php

$simulationTime = strtotime($clock->simulation_time);

$hour = date('H', $simulationTime);
$currentTime = date('H:i', $simulationTime);

$morningStart = date('H:i', strtotime($clock->morning_checkin));
$morningEnd   = '09:00';

$eveningStart = date('H:i', strtotime($clock->evening_checkout));
$eveningEnd   = '18:00';

if ($hour >= 5 && $hour < 12) {
    $greeting = "☀️ Good Morning";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "🌤️ Good Afternoon";
} else {
    $greeting = "🌙 Good Evening";
}

// Attendance Session
if ($currentTime >= $morningStart && $currentTime <= $morningEnd) {

    $status = "Morning Check In";
    $color = "green";

} elseif ($currentTime >= $eveningStart && $currentTime <= $eveningEnd) {

    $status = "Evening Check Out";
    $color = "blue";

} else {

    $status = "Outside Operating Hours";
    $color = "red";

}

@endphp

<h1>Dashboard</h1>

<hr>

<h2>{{ $greeting }}</h2>

<p>
<strong>Simulation Date :</strong><br>
{{ date('d F Y', $simulationTime) }}
</p>

<p>
<strong>Simulation Time :</strong><br>
{{ date('h:i:s A', $simulationTime) }}
</p>

<hr>

<h3>Attendance Session</h3>

<p style="color: {{ $color }}; font-weight:bold;">
{{ $status }}
</p>




<hr>

<h3>Current Schedule</h3>

<table border="1" cellpadding="10">

<tr>
    <td>Morning Check In</td>
    <td>{{ date('h:i A', strtotime($clock->morning_checkin)) }} - 09:00 AM</td>
</tr>

<tr>
    <td>Evening Check Out</td>
    <td>{{ date('h:i A', strtotime($clock->evening_checkout)) }} - 06:00 PM</td>
</tr>

</table>

<hr>

<h3>Simulation Clock</h3>

<table border="1" cellpadding="10">

<tr>
    <td>Current Date</td>
    <td>{{ date('d/m/Y', $simulationTime) }}</td>
</tr>

<tr>
    <td>Current Time</td>
    <td>{{ date('h:i:s A', $simulationTime) }}</td>
</tr>

<tr>
    <td>Current Rule</td>
    <td>{{ $status }}</td>
</tr>

</table>

<hr>

<a href="{{ url('/simulation-time') }}">
<button>
Change Simulation Time
</button>
</a>

</body>
</html>