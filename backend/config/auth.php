<?php

return [
    'jwt' => [
        'secret'     => env('JWT_SECRET', 'your-secret-key'),
        'algorithm'  => env('JWT_ALGORITHM', 'HS256'),
        'expiration' => (int) env('JWT_EXPIRATION', 3600),
    ],
    'roles' => [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'front_desk' => 'Front Desk',
        'housekeeping' => 'Housekeeping',
        'chef' => 'Chef',
        'accountant' => 'Accountant',
    ],
    'permissions' => [
        'admin' => ['*'],
        'manager' => ['view', 'create', 'edit', 'delete'],
        'front_desk' => ['view', 'create', 'edit'],
        'housekeeping' => ['view', 'edit'],
        'chef' => ['view', 'create', 'edit'],
        'accountant' => ['view', 'create', 'edit', 'delete'],
    ],
];
