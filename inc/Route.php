<?php

$routes = [
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
            'add',
            'update',
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
    ]    
];