<?php

$routes = [
    'account' => [
        'class' => 'User',
        'routes' => [
            'get',
            'list-delivery' => 'listDelivery',
            'upload-delivery' => 'uploadDelivery',
            'delete-delivery' => 'deleteDelivery',
            'update-profile' => 'updateProfile'
        ]
    ],
    'auth' => [
        'class' => 'Auth',
        'routes' => [
            'login',
            'register',
            'auth',
            'change-password' => 'changePassword',
            'logout'
        ]
    ],
    'product_category' => [
        'class' => 'ProductCategory',
        'routes' => [
            'list'
        ]
    ],
    'products' => [
        'class' => 'Product',
        'routes' => [
            'list',
            'get'
        ]
    ],
    'orders' => [
        'class' => 'Order',
        'routes' => [
            'list',
            'get',
            'detail',
            'add',
            'cancel',
            'check',
            'checkout',
            'delete'
        ]
    ],
    'carts' => [
        'class' => 'Cart',
        'routes' => [
            'list',
            'add',
            'edit',
            'delete'
        ]
    ],
    'banners' => [
        'class' => 'Banner',
        'routes' => [
            'list'
        ]
    ]
];