<?php

return [
    'client_id' => ['a24be1c4-018d-4cf5-a4f8-9ce86452563a'],
    'trusted_client_ids' => array_filter(explode(',', env('PASSPORT_TRUSTED_CLIENT_IDS', ''))),

    'allowed_logout_redirect_hosts' => [
        'localhost:5173', // front Vue en dev
        // 'app-a.mondomaine.com', 'app-b.mondomaine.com', ... en prod
    ], 
];

