<?php

return [
    'autoload' => false,
    'hooks' => [
        'epay_config_init' => [
            'epay',
        ],
        'addon_action_begin' => [
            'epay',
        ],
        'action_begin' => [
            'epay',
            'third',
        ],
        'upgrade' => [
            'shop',
        ],
        'app_init' => [
            'shop',
        ],
        'view_filter' => [
            'shop',
            'third',
        ],
        'user_sidenav_after' => [
            'shop',
        ],
        'config_init' => [
            'summernote',
            'third',
        ],
        'user_delete_successed' => [
            'third',
        ],
        'user_logout_successed' => [
            'third',
        ],
        'module_init' => [
            'third',
        ],
    ],
    'route' => [
        '/shop/$' => 'shop/index/index',
        '/shop/a/[:id]' => 'shop/goods/index',
        '/shop/p/[:diyname]' => 'shop/page/index',
        '/shop/s' => 'shop/search/index',
        '/shop/c/[:diyname]' => 'shop/category/index',
        '/shop/coupon/[:coupon]' => 'shop/coupon/show',
        '/shop/coupon' => 'shop/coupon/index',
        '/shop/exchange/[:id]' => 'shop/exchange/show',
        '/shop/exchange' => 'shop/exchange/index',
        '/third$' => 'third/index/index',
        '/third/connect/[:platform]' => 'third/index/connect',
        '/third/callback/[:platform]' => 'third/index/callback',
        '/third/bind/[:platform]' => 'third/index/bind',
        '/third/unbind/[:platform]' => 'third/index/unbind',
    ],
    'priority' => [],
    'domain' => '',
];
