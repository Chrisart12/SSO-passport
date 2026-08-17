<?php

return [
    'client_id' => ['a24be1c4-018d-4cf5-a4f8-9ce86452563a', 'a27ea5af-af2b-4795-98c1-fdf29ad8a714'],
    'trusted_client_ids' => array_filter(explode(',', env('PASSPORT_TRUSTED_CLIENT_IDS', ''))),

    'allowed_logout_redirect_hosts' => [
        'localhost:5176', // front Vue en dev
        'localhost:5177', // front React en dev
        'localhost:5178', // front Angular en dev
        // 'app-a.mondomaine.com', 'app-b.mondomaine.com', ... en prod
    ], 
];

