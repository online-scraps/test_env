<?php
//This is hierarchy for system users
//To access this array config('system.user_level.super_user')
return [
    "user_level"=>[
        "super_user"=>0,
        "system_admin"=>1,
        "organization_user"=>2,
        "store_user"=>3,
        "store_admin"=>4,
    ]
];