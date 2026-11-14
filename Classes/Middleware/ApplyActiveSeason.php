<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Middleware;

use MoveElevator\Typo3ChameleonLogin\Routing\LoginRoute;
use MoveElevator\Typo3ChameleonLogin\Season\SeasonConfiguration;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

use function array_replace_recursive;

/**
 * The core reads the login options from TYPO3_CONF_VARS before any login event is dispatched,
 * so the active season has to be merged in before the controller runs.
 */
final readonly class ApplyActiveSeason implements MiddlewareInterface
{
    public function __construct(
        private SeasonConfiguration $seasonConfiguration,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (LoginRoute::matches($request)) {
            $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
                $GLOBALS['TYPO3_CONF_VARS'],
                $this->seasonConfiguration->build(),
            );
        }

        return $handler->handle($request);
    }
}
