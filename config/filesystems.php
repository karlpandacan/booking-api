<?php
return [
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => env('STORAGE_LOCAL', ''),
            'permissions' => [
                'file' => [
                    'public' => 0775,
                    'private' => 0775,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0755,
                ],
            ],
        ],
    ]
];
