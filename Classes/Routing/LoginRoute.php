<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Routing;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Route;

use function in_array;

final class LoginRoute
{
    private const array IDENTIFIERS = [
        'login',
        'login_frameset',
        'password_forget',
        'password_forget_initiate_reset',
        'password_reset_validate',
        'password_reset_finish',
        'auth_mfa',
        'setup_mfa',
    ];

    public static function matches(ServerRequestInterface $request): bool
    {
        $route = $request->getAttribute('route');

        return $route instanceof Route && in_array($route->getOption('_identifier'), self::IDENTIFIERS, true);
    }
}
