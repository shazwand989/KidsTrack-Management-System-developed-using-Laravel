<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KidsTrack - Confirm Child</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Canvas Confetti Library -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: #f1f5f9;
            transition: background 0.5s ease;
        }

        /* ================================================ */
        /* CHECK-IN MODE (DEFAULT)                         */
        /* ================================================ */
        body.checkin-mode {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        }

        /* ================================================ */
        /* CHECK-OUT MODE (BERBEZA)                        */
        /* ================================================ */
        body.checkout-mode {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .confirm-card {
            background: white;
            border-radius: 30px;
            padding: 0;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            transition: all 0.5s ease;
        }

        /* ================================================ */
        /* CHECK-IN HEADER - HIJAU / CERAH                 */
        /* ================================================ */
        .card-header {
            padding: 30px 30px 25px;
            text-align: center;
            color: white;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .card-header.main-parent {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .card-header.second-parent {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-header.guardian-mode {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }
        .card-header.admin-mode {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .card-header.birthday-mode {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            color: #4a1942;
        }
        .card-header.weekend-mode {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-header.weekday-morning {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .card-header.weekday-evening {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .card-header.outside-hours {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        }

        /* ================================================ */
        /* CHECK-OUT HEADER - GELAP / MERAH / BERBEZA      */
        /* ================================================ */
        .card-header.checkout-mode {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .card-header.checkout-mode .badge-role {
            background: rgba(255,255,255,0.15);
            color: #facc15;
        }
        .card-header.checkout-mode h2 {
            color: #facc15;
        }
        .card-header.checkout-mode .sub-text {
            color: #94a3b8;
        }
        .card-header.checkout-mode .checkout-welcome {
            background: rgba(250, 204, 21, 0.15);
            color: #facc15;
            border: 1px solid rgba(250, 204, 21, 0.3);
        }

        .card-header.checkout-birthday {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            color: #4a1942;
        }
        .card-header.checkout-birthday .badge-role {
            background: rgba(255,255,255,0.3);
            color: #4a1942;
        }
        .card-header.checkout-birthday .checkout-welcome {
            background: rgba(255,255,255,0.3);
            color: #4a1942;
        }

        .card-header.unauthorized-mode {
            background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%);
        }

        .confetti-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            opacity: 0.7;
            animation: confettiFall 3s linear infinite;
        }

        @keyframes confettiFall {
            0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(200px) rotate(720deg); opacity: 0; }
        }

        .card-header .icon-big { font-size: 48px; margin-bottom: 8px; }
        .card-header .badge-role {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(4px);
            margin-bottom: 10px;
        }
        .card-header h2 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .card-header .sub-text { font-size: 14px; opacity: 0.9; font-weight: 500; }

        .birthday-text {
            font-size: 16px;
            font-weight: 700;
            margin-top: 8px;
            padding: 10px 16px;
            background: rgba(255,255,255,0.4);
            border-radius: 12px;
            backdrop-filter: blur(4px);
            animation: pulseGlow 1.5s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); box-shadow: 0 0 30px rgba(255,255,255,0.3); }
        }

        .card-body { padding: 25px 30px 30px; }

        /* ================================================ */
        /* CHECK-IN CHILD PROFILE - HIJAU / CERAH          */
        /* ================================================ */
        .child-profile {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .child-profile.birthday-profile {
            border-color: #f472b6;
            background: #fdf2f8;
        }

        /* ================================================ */
        /* CHECK-OUT CHILD PROFILE - GELAP / BERBEZA       */
        /* ================================================ */
        .child-profile.checkout-profile {
            border-color: #facc15;
            background: #1e293b;
        }
        .child-profile.checkout-profile .child-name {
            color: #facc15;
        }
        .child-profile.checkout-profile .child-class {
            color: #94a3b8;
        }
        .child-profile.checkout-profile .status-badge.checked-in {
            background: rgba(250, 204, 21, 0.2);
            color: #facc15;
        }

        .child-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #6d28d9, #9333ea);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .child-avatar.birthday-avatar {
            background: linear-gradient(135deg, #ec4899, #f472b6);
            animation: avatarPulse 2s ease-in-out infinite;
            box-shadow: 0 0 40px rgba(236, 72, 153, 0.4);
        }

        /* ================================================ */
        /* CHECK-OUT AVATAR - GELAP / BERBEZA              */
        /* ================================================ */
        .child-avatar.checkout-avatar {
            background: linear-gradient(135deg, #facc15, #f59e0b);
            color: #1e293b;
        }

        @keyframes avatarPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .child-name { font-size: 22px; font-weight: 700; color: #1f2937; }
        .child-class { font-size: 14px; color: #6b7280; margin-top: 4px; }

        .status-badge {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            margin-top: 8px;
        }

        .status-badge.checked-in {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.checked-out {
            background: #e2e8f0;
            color: #475569;
        }

        /* ================================================ */
        /* CHECK-IN TIMER BOX - HIJAU / CERAH              */
        /* ================================================ */
        .timer-display-box {
            margin-top: 15px;
            padding: 12px 16px;
            background: #f0fdf4;
            border-radius: 12px;
            border: 2px solid #86efac;
        }

        .timer-display-box .timer-title {
            font-weight: 700;
            font-size: 13px;
            color: #065f46;
            margin-bottom: 6px;
        }

        .timer-display-box .timer-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #1f2937;
            padding: 2px 0;
        }

        .timer-display-box .timer-row .label { font-weight: 500; }
        .timer-display-box .timer-row .time { font-weight: 700; color: #065f46; }

        .timer-display-box .current-slot {
            margin-top: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .timer-display-box .current-slot.morning {
            background: #dbeafe;
            color: #1e40af;
        }

        .timer-display-box .current-slot.evening {
            background: #fce7f3;
            color: #9d174d;
        }

        .timer-display-box .current-slot.outside {
            background: #fef3c7;
            color: #92400e;
        }

        /* ================================================ */
        /* CHECK-OUT TIMER BOX - GELAP / BERBEZA           */
        /* ================================================ */
        .timer-display-box.checkout-mode {
            background: #1e293b;
            border-color: #facc15;
        }
        .timer-display-box.checkout-mode .timer-title {
            color: #facc15;
        }
        .timer-display-box.checkout-mode .timer-row {
            color: #94a3b8;
        }
        .timer-display-box.checkout-mode .timer-row .time {
            color: #facc15;
        }
        .timer-display-box.checkout-mode .current-slot.evening {
            background: rgba(250, 204, 21, 0.15);
            color: #facc15;
        }

        .fee-banner {
            margin-top: 15px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            display: none;
            border: 2px solid transparent;
        }

        .fee-banner.unpaid {
            display: block;
            background: #fdf2f8;
            border-color: #f472b6;
            color: #9d174d;
        }
        .fee-banner.unpaid i { margin-right: 8px; color: #db2777; }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        /* ================================================ */
        /* CHECK-IN BUTTON - HIJAU                         */
        /* ================================================ */
        .btn-yes {
            flex: 1;
            padding: 14px;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 120px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-yes:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
        }

        .btn-yes.birthday-btn {
            background: linear-gradient(135deg, #ec4899, #f472b6);
        }
        .btn-yes.birthday-btn:hover {
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
        }

        /* ================================================ */
        /* CHECK-OUT BUTTON - GELAP / MERAH / BERBEZA      */
        /* ================================================ */
        .btn-yes.checkout-btn {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }
        .btn-yes.checkout-btn:hover {
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }

        .btn-yes.second-parent-btn {
            background: linear-gradient(135deg, #6d28d9, #8b5cf6);
        }
        .btn-yes.second-parent-btn:hover {
            box-shadow: 0 8px 25px rgba(109, 40, 217, 0.4);
        }

        .btn-yes.guardian-btn {
            background: linear-gradient(135deg, #d97706, #f59e0b);
        }
        .btn-yes.guardian-btn:hover {
            box-shadow: 0 8px 25px rgba(217, 119, 6, 0.4);
        }

        .btn-no {
            flex: 1;
            padding: 14px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 120px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-no:hover { background: #e5e7eb; }

        /* ================================================ */
        /* CHECK-OUT BUTTON NO - GELAP VERSION             */
        /* ================================================ */
        .btn-no.checkout-no {
            background: #334155;
            color: #94a3b8;
        }
        .btn-no.checkout-no:hover {
            background: #475569;
            color: #e2e8f0;
        }

        .unauthorized-content {
            text-align: center;
            padding: 10px 0;
        }
        .unauthorized-content .icon {
            font-size: 64px;
            color: #ef4444;
            margin-bottom: 12px;
        }
        .unauthorized-content .log-id {
            margin-top: 15px;
            padding: 10px;
            background: #fef2f2;
            border-radius: 10px;
            font-family: monospace;
            font-size: 13px;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ================================================ */
        /* CHECK-OUT WELCOME - BERBEZA                     */
        /* ================================================ */
        .checkout-welcome {
            margin-top: 12px;
            padding: 12px 16px;
            background: #dbeafe;
            border-radius: 12px;
            color: #1e40af;
            font-weight: 600;
            font-size: 15px;
            text-align: center;
        }
        .checkout-welcome i { margin-right: 8px; }

        .role-badge-main { background: rgba(16, 185, 129, 0.3); color: #10b981; }
        .role-badge-second { background: rgba(139, 92, 246, 0.3); color: #8b5cf6; }
        .role-badge-guardian { background: rgba(245, 158, 11, 0.3); color: #f59e0b; }
        .role-badge-admin { background: rgba(71, 85, 105, 0.3); color: #e2e8f0; }

        /* ================================================ */
        /* CHECK-OUT BADGE ROLE - GELAP                    */
        /* ================================================ */
        .role-badge-main.checkout-role { background: rgba(250, 204, 21, 0.2); color: #facc15; }
        .role-badge-second.checkout-role { background: rgba(250, 204, 21, 0.2); color: #facc15; }
        .role-badge-guardian.checkout-role { background: rgba(250, 204, 21, 0.2); color: #facc15; }
        .role-badge-admin.checkout-role { background: rgba(250, 204, 21, 0.2); color: #facc15; }

        @media (max-width: 480px) {
            .card-header { padding: 25px 20px 20px; }
            .card-header h2 { font-size: 18px; }
            .card-body { padding: 20px; }
            .btn-yes, .btn-no { min-width: 100px; font-size: 14px; padding: 12px; }
            .birthday-text { font-size: 14px; padding: 8px 12px; }
            .timer-display-box .timer-row { font-size: 12px; flex-wrap: wrap; }
        }
    </style>
</head>

<!-- ============================================================ -->
<!-- BODY CLASS - CHECK-IN MODE ATAU CHECK-OUT MODE               -->
<!-- ============================================================ -->
<body class="{{ $isCheckoutMode ? 'checkout-mode' : 'checkin-mode' }}">

    @php
        use Carbon\Carbon;
        
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentHour = (int) $currentTime->format('H');
        
        $timerSetting = \App\Models\TimerSetting::where('day_name', $currentTime->format('l'))->first();
        
        $isMorningSlot = false;
        $isEveningSlot = false;
        $isOutsideSlot = false;
        $slotLabel = 'Morning (Check-in)';
        $slotType = 'checkin';
        $morningStart = '--:--';
        $morningEnd = '--:--';
        $eveningStart = '--:--';
        $eveningEnd = '--:--';
        
        if ($timerSetting) {
            $morningStart = date('H:i', strtotime($timerSetting->morning_start));
            $morningEnd = date('H:i', strtotime($timerSetting->morning_end));
            $eveningStart = date('H:i', strtotime($timerSetting->evening_start));
            $eveningEnd = date('H:i', strtotime($timerSetting->evening_end));
            
            $morningStartInt = (int) str_replace(':', '', $timerSetting->morning_start);
            $morningEndInt = (int) str_replace(':', '', $timerSetting->morning_end);
            $eveningStartInt = (int) str_replace(':', '', $timerSetting->evening_start);
            $eveningEndInt = (int) str_replace(':', '', $timerSetting->evening_end);
            $currentTimeInt = (int) $currentTime->format('Hi');
            
            if ($currentTimeInt >= $morningStartInt && $currentTimeInt <= $morningEndInt) {
                $isMorningSlot = true;
                $slotLabel = 'Morning (Check-in)';
                $slotType = 'checkin';
            } elseif ($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt) {
                $isEveningSlot = true;
                $slotLabel = 'Evening (Check-out)';
                $slotType = 'checkout';
            } else {
                $isOutsideSlot = true;
                $slotLabel = 'Outside Hours';
                $slotType = 'closed';
            }
        }
        
        $attendance = \App\Models\Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();
        
        $hasCheckin = $attendance && $attendance->checkin_time;
        $hasCheckout = $attendance && $attendance->checkout_time;
        $isCheckedIn = $hasCheckin && !$hasCheckout;
        $isCheckedOut = $hasCheckout;
        $checkinTime = $attendance ? Carbon::parse($attendance->checkin_time)->format('h:i A') : null;
        $checkoutTime = $attendance ? Carbon::parse($attendance->checkout_time)->format('h:i A') : null;
        
        $isMainParent = isset($isMainParent) ? $isMainParent : false;
        $isSecondParent = isset($isSecondParent) ? $isSecondParent : false;
        $isGuardian = isset($isGuardian) ? $isGuardian : false;
        $userRole = isset($userRole) ? $userRole : 'unknown';
        
        // ============================================================
        // CHECK-OUT MODE DETECTION
        // ============================================================
        $isCheckoutMode = ($isCheckedIn && !$isCheckedOut);
        
        $headerClass = 'weekday-morning';
        $iconBig = '☀️';
        $badgeText = 'Check-In';
        $greetingText = "Welcome, {$parentName}!";
        $subText = 'Please confirm your child.';
        $buttonClass = '';
        $roleBadgeClass = 'role-badge-main';
        $roleLabel = '';
        $timerBoxClass = '';
        $btnNoClass = '';
        $bodyClass = 'checkin-mode';
        
        // ============================================================
        // ROLE LABEL
        // ============================================================
        if ($isMainParent) {
            $roleLabel = '👨‍👩‍👦 Main Parent';
            $roleBadgeClass = 'role-badge-main';
        } elseif ($isSecondParent) {
            $roleLabel = '👫 Second Parent';
            $roleBadgeClass = 'role-badge-second';
        } elseif ($isGuardian) {
            $roleLabel = '🛡️ Guardian';
            $roleBadgeClass = 'role-badge-guardian';
        } elseif (in_array($userRole, ['admin', 'teacher'])) {
            $roleLabel = '👑 Admin/Teacher';
            $roleBadgeClass = 'role-badge-admin';
        } else {
            $roleLabel = '👤 User';
            $roleBadgeClass = 'role-badge-main';
        }
        
        // ============================================================
        // CHECK-OUT MODE - INTERFACE BERBEZA
        // ============================================================
        if ($isCheckoutMode) {
            // === CHECK-OUT MODE ===
            $bodyClass = 'checkout-mode';
            $iconBig = '👋';
            $badgeText = 'Check-Out';
            $greetingText = "Time to Pick Up, {$parentName}!";
            $subText = 'Please confirm to pick up your child.';
            $buttonClass = 'checkout-btn';
            $timerBoxClass = 'checkout-mode';
            $btnNoClass = 'checkout-no';
            
            // Role badge checkout version
            $roleBadgeClass .= ' checkout-role';
            
            if ($isBirthday) {
                $headerClass = 'checkout-birthday';
                $iconBig = '🎉';
                $badgeText = '🎂 Birthday Check-Out!';
                $greetingText = "Happy Birthday, {$child->name}!";
                $subText = "Time to go home and celebrate! 🎂";
                $buttonClass = 'birthday-btn';
            } else {
                $headerClass = 'checkout-mode';
            }
            
        } else {
            // === CHECK-IN MODE ===
            $bodyClass = 'checkin-mode';
            
            if ($userRole == 'unknown') {
                $headerClass = 'unauthorized-mode';
                $iconBig = '⚠️';
                $badgeText = 'Access Denied';
                $greetingText = 'Access Denied!';
                $subText = 'Invalid QR code or unauthorized device.';
                
            } elseif ($isBirthday) {
                $headerClass = 'birthday-mode';
                $iconBig = '🎉';
                $badgeText = '🎂 Birthday!';
                $greetingText = "Happy Birthday, {$child->name}!";
                $subText = $birthdayMessage;
                $buttonClass = 'birthday-btn';
                
            } elseif ($isWeekend) {
                $headerClass = 'weekend-mode';
                $iconBig = '🎨';
                $badgeText = 'Weekend';
                $greetingText = 'Welcome to Weekend Activities / Extra Classes!';
                $subText = 'Have a great day! ✨';
                
            } elseif ($isMorningSlot) {
                if ($isMainParent) {
                    $headerClass = 'main-parent';
                    $iconBig = '☀️';
                    $badgeText = $roleLabel . ' • Morning';
                    $greetingText = "Welcome, {$parentName}!";
                    $subText = "Morning session • Please confirm your child.";
                } elseif ($isSecondParent) {
                    $headerClass = 'second-parent';
                    $iconBig = '☀️';
                    $badgeText = $roleLabel . ' • Morning';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Second Parent • Morning session";
                    $buttonClass = 'second-parent-btn';
                } elseif ($isGuardian) {
                    $headerClass = 'guardian-mode';
                    $iconBig = '☀️';
                    $badgeText = $roleLabel . ' • Morning';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Guardian • Morning session";
                    $buttonClass = 'guardian-btn';
                } elseif (in_array($userRole, ['admin', 'teacher'])) {
                    $headerClass = 'admin-mode';
                    $iconBig = '☀️';
                    $badgeText = $roleLabel . ' • Morning';
                    $greetingText = "Welcome, {$parentName}!";
                    $subText = "Admin • Morning session";
                } else {
                    $headerClass = 'weekday-morning';
                    $iconBig = '☀️';
                    $badgeText = $slotLabel;
                    $greetingText = "Welcome!";
                    $subText = "Morning session • Please confirm.";
                }
                
            } elseif ($isEveningSlot) {
                if ($isMainParent) {
                    $headerClass = 'main-parent';
                    $iconBig = '🌙';
                    $badgeText = $roleLabel . ' • Evening';
                    $greetingText = "Welcome, {$parentName}!";
                    $subText = "Evening session • Please confirm your child.";
                } elseif ($isSecondParent) {
                    $headerClass = 'second-parent';
                    $iconBig = '🌙';
                    $badgeText = $roleLabel . ' • Evening';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Second Parent • Evening session";
                    $buttonClass = 'second-parent-btn';
                } elseif ($isGuardian) {
                    $headerClass = 'guardian-mode';
                    $iconBig = '🌙';
                    $badgeText = $roleLabel . ' • Evening';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Guardian • Evening session";
                    $buttonClass = 'guardian-btn';
                } elseif (in_array($userRole, ['admin', 'teacher'])) {
                    $headerClass = 'admin-mode';
                    $iconBig = '🌙';
                    $badgeText = $roleLabel . ' • Evening';
                    $greetingText = "Welcome, {$parentName}!";
                    $subText = "Admin • Evening session";
                } else {
                    $headerClass = 'weekday-evening';
                    $iconBig = '🌙';
                    $badgeText = $slotLabel;
                    $greetingText = "Welcome!";
                    $subText = "Evening session • Please confirm.";
                }
                
            } elseif ($isOutsideSlot) {
                if ($isMainParent) {
                    $headerClass = 'main-parent';
                    $iconBig = '⏰';
                    $badgeText = $roleLabel . ' • Outside Hours';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Outside hours • Still allowed";
                } elseif ($isSecondParent) {
                    $headerClass = 'second-parent';
                    $iconBig = '⏰';
                    $badgeText = $roleLabel . ' • Outside Hours';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Second Parent • Outside hours";
                    $buttonClass = 'second-parent-btn';
                } elseif ($isGuardian) {
                    $headerClass = 'guardian-mode';
                    $iconBig = '⏰';
                    $badgeText = $roleLabel . ' • Outside Hours';
                    $greetingText = "Hi, {$parentName}!";
                    $subText = "Guardian • Outside hours";
                    $buttonClass = 'guardian-btn';
                } elseif (in_array($userRole, ['admin', 'teacher'])) {
                    $headerClass = 'admin-mode';
                    $iconBig = '⏰';
                    $badgeText = $roleLabel . ' • Outside Hours';
                    $greetingText = "Welcome, {$parentName}!";
                    $subText = "Admin • Outside hours";
                } else {
                    $headerClass = 'outside-hours';
                    $iconBig = '⏰';
                    $badgeText = 'Outside Hours';
                    $greetingText = "Hi!";
                    $subText = "Outside operation hours • Still allowed";
                }
                
            } elseif ($isMainParent) {
                $headerClass = 'main-parent';
                $iconBig = '👨‍👩‍👦';
                $badgeText = $roleLabel;
                $greetingText = "Welcome, {$parentName}!";
                $subText = 'Please confirm your child.';
                
            } elseif ($isSecondParent) {
                $headerClass = 'second-parent';
                $iconBig = '👫';
                $badgeText = $roleLabel;
                $greetingText = "Hi, {$parentName}!";
                $subText = 'Status: Registered Second Parent';
                $buttonClass = 'second-parent-btn';
                
            } elseif ($isGuardian) {
                $headerClass = 'guardian-mode';
                $iconBig = '🛡️';
                $badgeText = $roleLabel;
                $greetingText = "Hi, {$parentName}!";
                $subText = 'Status: Registered Guardian';
                $buttonClass = 'guardian-btn';
                
            } elseif (in_array($userRole, ['admin', 'teacher'])) {
                $headerClass = 'admin-mode';
                $iconBig = '👑';
                $badgeText = $roleLabel;
                $greetingText = "Welcome, {$parentName}!";
                $subText = 'Status: Administrator Access';
                
            } else {
                $headerClass = 'weekday-morning';
                $iconBig = '☀️';
                $badgeText = 'Check-In';
                $greetingText = "Welcome!";
                $subText = 'Please confirm your child.';
            }
        }
        
        if ($isCheckedOut) {
            $badgeText = '✅ Already Checked Out';
            $subText = 'Your child has safely returned home.';
            $iconBig = '✅';
            $bodyClass = 'checkin-mode';
        }
    @endphp

    <div class="confirm-card">
        
        <!-- ============================================================ -->
        <!-- CARD HEADER - CHECK-IN / CHECK-OUT VERSION                   -->
        <!-- ============================================================ -->
        <div class="card-header {{ $headerClass }}">
            
            @if($isBirthday && !$isCheckoutMode)
                <div class="confetti-container" id="confettiContainer"></div>
            @endif
            
            @if($isBirthday && $isCheckoutMode)
                <div class="confetti-container" id="confettiContainerCheckout"></div>
            @endif
            
            <div class="icon-big">{{ $iconBig }}</div>
            <div class="badge-role {{ $roleBadgeClass }}">{{ $badgeText }}</div>
            <h2>{{ $greetingText }}</h2>
            <p class="sub-text">{{ $subText }}</p>
            
            @if($isBirthday && !$isCheckoutMode)
                <div class="birthday-text">🎂 {{ $birthdayMessage }}</div>
            @endif
            
            @if($isBirthday && $isCheckoutMode)
                <div class="birthday-text">🎂 Happy Birthday, {{ $child->name }}! Have a wonderful celebration at home! 🎉</div>
            @endif
            
            @if($isCheckoutMode && !$isBirthday)
                <div class="checkout-welcome">
                    <i class="fas fa-clock"></i> Checked in at {{ $checkinTime }} • Time to go home! 🏠
                </div>
            @endif
        </div>
        
        <div class="card-body">
            
            @if($userRole == 'unknown')
                <div class="unauthorized-content">
                    <div class="icon"><i class="fas fa-user-slash"></i></div>
                    <p style="color: #6b7280; font-size: 15px; margin-bottom: 8px;">
                        Security log updated. Unauthorized access detected.
                    </p>
                    <div class="log-id">
                        <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
                        LOG_ID: {{ rand(10000, 99999) }}
                    </div>
                </div>
                
            @elseif($isCheckedOut)
                <div class="child-profile">
                    <div class="child-avatar">
                        {{ strtoupper(substr($child->name, 0, 1)) }}
                    </div>
                    <div class="child-name">{{ $child->name }}</div>
                    <div class="child-class">🏫 {{ $child->classroom->name ?? 'No class' }}</div>
                    <div style="margin-top: 8px;">
                        <span class="status-badge checked-out">✅ Checked Out</span>
                    </div>
                    <div style="margin-top: 12px; padding: 10px; background: #f0fdf4; border-radius: 8px; color: #166534; font-weight: 600;">
                        🏠 Safe & Sound • {{ $checkoutTime }}
                    </div>
                </div>
                
                @if($timerSetting)
                <div class="timer-display-box">
                    <div class="timer-title">⏱️ Waktu Operasi Hari Ini ({{ $timerSetting->day_name }})</div>
                    <div class="timer-row">
                        <span class="label">🌅 Morning</span>
                        <span class="time">{{ $morningStart }} - {{ $morningEnd }}</span>
                    </div>
                    <div class="timer-row">
                        <span class="label">🌙 Evening</span>
                        <span class="time">{{ $eveningStart }} - {{ $eveningEnd }}</span>
                    </div>
                    <div class="current-slot outside">
                        ⚠️ Current: Already Checked Out
                    </div>
                </div>
                @endif
                
                <div class="btn-group">
                    <a href="{{ route('kiosk.index') }}" class="btn-yes">
                        ✅ Back to Kiosk
                    </a>
                </div>
                
            @else
                <!-- ============================================================ -->
                <!-- CHILD PROFILE - CHECK-IN / CHECK-OUT VERSION                 -->
                <!-- ============================================================ -->
                <div class="child-profile {{ $isBirthday ? 'birthday-profile' : '' }} {{ $isCheckoutMode ? 'checkout-profile' : '' }}">
                    <div class="child-avatar {{ $isBirthday ? 'birthday-avatar' : '' }} {{ $isCheckoutMode ? 'checkout-avatar' : '' }}">
                        {{ strtoupper(substr($child->name, 0, 1)) }}
                    </div>
                    <div class="child-name">{{ $child->name }}</div>
                    <div class="child-class">🏫 {{ $child->classroom->name ?? 'No class' }}</div>
                    
                    @if($isCheckoutMode)
                        <div style="margin-top: 8px;">
                            <span class="status-badge checked-in">✅ Checked In • {{ $checkinTime }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- ============================================================ -->
                <!-- TIMER BOX - CHECK-IN / CHECK-OUT VERSION                     -->
                <!-- ============================================================ -->
                @if($timerSetting)
                <div class="timer-display-box {{ $timerBoxClass }}">
                    <div class="timer-title">⏱️ Waktu Operasi Hari Ini ({{ $timerSetting->day_name }})</div>
                    <div class="timer-row">
                        <span class="label">🌅 Morning (Check-in)</span>
                        <span class="time">{{ $morningStart }} - {{ $morningEnd }}</span>
                    </div>
                    <div class="timer-row">
                        <span class="label">🌙 Evening (Check-out)</span>
                        <span class="time">{{ $eveningStart }} - {{ $eveningEnd }}</span>
                    </div>
                    <div class="current-slot 
                        @if($isMorningSlot) morning
                        @elseif($isEveningSlot) evening
                        @else outside
                        @endif">
                        @if($isMorningSlot)
                            🟢 Current: <strong>Morning Slot</strong> (Check-in)
                        @elseif($isEveningSlot)
                            🟢 Current: <strong>Evening Slot</strong> (Check-out)
                        @else
                            ⚠️ Current: <strong>Outside Operation Hours</strong> (Still allowed)
                        @endif
                    </div>
                </div>
                @endif
                
                @if($hasUnpaidFee)
                    <div class="fee-banner unpaid">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $feeMessage }}
                    </div>
                @endif
                
                <!-- ============================================================ -->
                <!-- BUTTONS - CHECK-IN / CHECK-OUT VERSION                       -->
                <!-- ============================================================ -->
                <div class="btn-group">
                    @if($isCheckoutMode)
                        <a href="{{ route('kiosk.add.another', $child->id) }}" 
                           class="btn-yes {{ $buttonClass }}">
                            👋 YES, Pick Up {{ $child->name }}
                        </a>
                    @else
                        <a href="{{ route('kiosk.add.another', $child->id) }}" 
                           class="btn-yes {{ $buttonClass }}">
                            ✅ YES, Check-in {{ $child->name }}
                        </a>
                    @endif
                    
                    <a href="{{ route('kiosk.index') }}" class="btn-no {{ $btnNoClass }}">
                        ❌ NO, Not me
                    </a>
                </div>
            @endif
            
        </div>
    </div>

    @if($isBirthday && !$isCheckoutMode)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#f472b6', '#ec4899', '#fbc2eb', '#a6c1ee', '#fbbf24', '#34d399']
            });
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    spread: 100,
                    origin: { y: 0.5, x: 0.3 }
                });
            }, 500);
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    spread: 100,
                    origin: { y: 0.5, x: 0.7 }
                });
            }, 1000);
            
            const container = document.getElementById('confettiContainer');
            if (container) {
                const colors = ['#f472b6', '#ec4899', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa'];
                for (let i = 0; i < 30; i++) {
                    const piece = document.createElement('div');
                    piece.className = 'confetti-piece';
                    piece.style.left = Math.random() * 100 + '%';
                    piece.style.top = '-' + (Math.random() * 20) + '%';
                    piece.style.width = (Math.random() * 8 + 4) + 'px';
                    piece.style.height = (Math.random() * 8 + 4) + 'px';
                    piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                    piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                    piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    piece.style.animationDelay = (Math.random() * 2) + 's';
                    container.appendChild(piece);
                }
            }
        });
    </script>
    @endif

    @if($isBirthday && $isCheckoutMode)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            confetti({
                particleCount: 80,
                spread: 60,
                origin: { y: 0.6 },
                colors: ['#f472b6', '#ec4899', '#fbc2eb', '#a6c1ee', '#fbbf24', '#34d399']
            });
            
            setTimeout(() => {
                confetti({
                    particleCount: 40,
                    spread: 80,
                    origin: { y: 0.5, x: 0.2 }
                });
            }, 400);
            
            setTimeout(() => {
                confetti({
                    particleCount: 40,
                    spread: 80,
                    origin: { y: 0.5, x: 0.8 }
                });
            }, 800);
            
            const container = document.getElementById('confettiContainerCheckout');
            if (container) {
                const colors = ['#f472b6', '#ec4899', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa'];
                for (let i = 0; i < 25; i++) {
                    const piece = document.createElement('div');
                    piece.className = 'confetti-piece';
                    piece.style.left = Math.random() * 100 + '%';
                    piece.style.top = '-' + (Math.random() * 20) + '%';
                    piece.style.width = (Math.random() * 8 + 4) + 'px';
                    piece.style.height = (Math.random() * 8 + 4) + 'px';
                    piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                    piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                    piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    piece.style.animationDelay = (Math.random() * 2) + 's';
                    container.appendChild(piece);
                }
            }
        });
    </script>
    @endif

</body>
</html>