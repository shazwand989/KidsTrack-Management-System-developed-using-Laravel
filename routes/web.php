<?php

// ============================================
// KIDSTRACK / SAFECARE — ROUTE ENTRY POINT
// ============================================
// Routes are organized into dedicated files based on access level:
//   routes/public.php  — Public routes (no authentication required)
//   routes/admin.php   — Admin/Teacher routes
//   routes/parent.php  — Parent/Guardian routes
//   routes/auth.php    — Authentication routes (login, register, etc.)
// ============================================

// Load route modules in the correct order.
// Public routes first, then auth (Breeze/Fortify), then role-based.
require __DIR__ . "/public.php";
require __DIR__ . "/auth.php";
require __DIR__ . "/admin.php";
require __DIR__ . "/parent.php";
