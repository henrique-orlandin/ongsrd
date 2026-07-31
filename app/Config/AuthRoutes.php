<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthRoutes as ShieldAuthRoutes;

class AuthRoutes extends ShieldAuthRoutes
{
    public array $routes = [
        'register' => [
            [
                'get',
                'cms/register',
                'RegisterController::registerView',
                'register',
            ],
            [
                'post',
                'cms/register',
                'RegisterController::registerAction',
            ],
        ],
        'login' => [
            [
                'get',
                'cms/login',
                'LoginController::loginView',
                'login',
            ],
            [
                'post',
                'cms/login',
                'LoginController::loginAction',
            ],
        ],
        'magic-link' => [
            [
                'get',
                'cms/login/magic-link',
                'MagicLinkController::loginView',
                'magic-link',
            ],
            [
                'post',
                'cms/login/magic-link',
                'MagicLinkController::loginAction',
            ],
            [
                'get',
                'cms/login/verify-magic-link',
                'MagicLinkController::verify',
                'verify-magic-link',
            ],
        ],
        'logout' => [
            [
                'get',
                'cms/logout',
                'LoginController::logoutAction',
                'logout',
            ],
        ],
        'auth-actions' => [
            [
                'get',
                'cms/auth/a/show',
                'ActionController::show',
                'auth-action-show',
            ],
            [
                'post',
                'cms/auth/a/handle',
                'ActionController::handle',
                'auth-action-handle',
            ],
            [
                'post',
                'cms/auth/a/verify',
                'ActionController::verify',
                'auth-action-verify',
            ],
        ],
    ];
}
