<?php

declare(strict_types=1);

use MoveElevator\Typo3ChameleonLogin\Controller\SeasonModuleController;

return [
    SeasonModuleController::MODULE => [
        'parent' => 'site',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/site/login-seasons',
        'icon' => 'EXT:typo3_chameleon_login/Resources/Public/Icons/season.svg',
        'labels' => 'typo3_chameleon_login.module',
        'routes' => [
            '_default' => [
                'target' => SeasonModuleController::class.'::handleRequest',
            ],
        ],
    ],
];
