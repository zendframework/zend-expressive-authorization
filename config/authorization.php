<?php
return [
    'ZendRbac' => [
        'roles' => [
            // insert the role with parent (if any)
            // e.g. 'editor' => ['admin'] (admin is parent of editor)
        ],
        'permissions' => [
            // for each role insert one or more permissions
            // e.g. 'admin' => ['admin.pages']
        ]
    ]
];
