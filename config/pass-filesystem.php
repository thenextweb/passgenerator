<?php
/**
 * If there's no specific filesystem defined for us, let's just copy the default one and change
 * the root.
 */
$fs_config = config('filesystems');

// Check whether there is a specific filesystem defined for passgenerator
// or we should use the default one.
if (isset($fs_config['disks']['passgenerator'])) {
    $passgenerator_fs = 'passgenerator';
} else {
    $passgenerator_fs = $fs_config['default'];
}

if (isset($fs_config['disks'][$passgenerator_fs])) {
    $fs_config['disks']['passgenerator'] = $fs_config['disks'][$passgenerator_fs];
    $fs_config['disks']['passgenerator']['root'] = storage_path('app/passgenerator');
} else {
    throw new Exception('There must be a default filesystem defined.');
}

return $fs_config;
