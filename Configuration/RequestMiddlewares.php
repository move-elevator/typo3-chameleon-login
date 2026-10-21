<?php

declare(strict_types=1);

use MoveElevator\Typo3ChameleonLogin\Middleware\ApplyActiveSeason;

return [
    'backend' => [
        'move-elevator/typo3-chameleon-login/apply-active-season' => [
            'target' => ApplyActiveSeason::class,
            'after' => [
                'typo3/cms-backend/backend-routing',
            ],
        ],
    ],
];
