<?php
$files = [
    'app/Http/Controllers/AttendanceController.php',
    'app/Http/Controllers/CheckinController.php',
    'app/Http/Controllers/AddAnotherChildController.php',
    'app/Http/Controllers/QRScanController.php',
    'app/Http/Controllers/ChildController.php',
    'app/Http/Controllers/ParentController.php',
    'app/Http/Controllers/Parent/AttendanceController.php',
    'app/Http/Controllers/Parent/ChildrenController.php',
    'app/Http/Controllers/Parent/DashboardController.php',
    'app/Http/Controllers/Parent/ProfileController.php',
    'app/Http/Controllers/Auth/AuthenticatedSessionController.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    $content = str_replace('ParentModel::', 'User::', $content);
    $content = str_replace('SecondParent::', 'User::', $content);
    $content = str_replace('Guardian::', 'User::', $content);
    // Fix: User::where('user_id' → User::where('id'
    $content = preg_replace("/User::where\('user_id'/", "User::where('id'", $content);
    file_put_contents($file, $content);
    echo "Fixed: $file\n";
}

echo "All done!\n";
