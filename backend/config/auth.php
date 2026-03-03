<?php

return [
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?? 'your-secret-key',
        'algorithm' => getenv('JWT_ALGORITHM') ?? 'HS256',
        'expiration' => getenv('JWT_EXPIRATION') ?? 3600,
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
