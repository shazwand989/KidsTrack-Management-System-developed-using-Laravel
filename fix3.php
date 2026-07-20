<?php
$f = 'app/Http/Controllers/AttendanceController.php';
$c = file_get_contents($f);
$c = str_replace("\$child->parent_id", "\$child->parent?->id", $c);
file_put_contents($f, $c);
echo "Fixed AttendanceController\n";
