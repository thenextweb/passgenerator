<?php
/**
 * If there's no specific filesystem defined for us, let's just copy the default one and change
 * the root
 */

$config = config('filesystems');

// Check whether there is a specific filesystem defined for passgenerator
// or we should use the default one.
if (isset($config['disks']['passgenerator'])) {
    $passgenerator_fs = 'passgenerator';
} else {
    $passgenerator_fs = $config['default'];
}

if (isset($config['disks'][$passgenerator_fs])) {
    $config['disks']['passgenerator'] = $config['disks'][$passgenerator_fs];
    $config['disks']['passgenerator']['root'] = storage_path('app/passgenerator');
} else {
    throw new Exception("There must be a default filesystem defined.");
}
return $config;
