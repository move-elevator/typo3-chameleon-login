<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Middleware;

use MoveElevator\Typo3ChameleonLogin\Season\SeasonConfiguration;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Backend\Routing\Route;

use function array_replace_recursive;
use function in_array;

/**
 * The core reads the login options from TYPO3_CONF_VARS before any login event is dispatched,
 * so the active season has to be merged in before the controller runs.
 */
final readonly class ApplyActiveSeason implements MiddlewareInterface
{
    private const array LOGIN_ROUTES = [
        'login',
        'login_frameset',
        'password_forget',
        'password_forget_initiate_reset',
        'password_reset_validate',
        'password_reset_finish',
        'auth_mfa',
        'setup_mfa',
    ];

    public function __construct(
        private SeasonConfiguration $seasonConfiguration,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');
        if ($route instanceof Route && in_array($route->getOption('_identifier'), self::LOGIN_ROUTES, true)) {
            $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
                $GLOBALS['TYPO3_CONF_VARS'],
                $this->seasonConfiguration->build(),
            );
        }

        return $handler->handle($request);
    }
}
