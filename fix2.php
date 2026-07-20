<?php
// Fix: ParentModel::where('user_id' → ParentModel::where('id'
// and $child->parent_id → uses child->parent() now

$files = glob('app/Http/Controllers/*.php');
$files = array_merge($files, glob('app/Http/Controllers/*/*.php'));

foreach ($files as $file) {
    $c = file_get_contents($file);
    $orig = $c;
    // ParentModel::where('user_id' → ParentModel::where('id'
    $c = str_replace("where('user_id'", "where('id'", $c);
    $c = str_replace('where("user_id"', 'where("id"', $c);
    if ($c !== $orig) {
        file_put_contents($file, $c);
        echo "Fixed: $file\n";
    }
}
echo "Done!\n";
