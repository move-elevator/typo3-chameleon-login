<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Tests\Unit\Routing;

use MoveElevator\Typo3ChameleonLogin\Routing\LoginRoute;
use PHPUnit\Framework\Attributes\{CoversClass, Test};
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Core\Http\ServerRequest;

#[CoversClass(LoginRoute::class)]
final class LoginRouteTest extends TestCase
{
    #[Test]
    public function matchesLoginRoutes(): void
    {
        $request = new ServerRequest()->withAttribute('route', new Route('/login', ['_identifier' => 'password_forget']));

        self::assertTrue(LoginRoute::matches($request));
    }

    #[Test]
    public function doesNotMatchOtherBackendRoutes(): void
    {
        $request = new ServerRequest()->withAttribute('route', new Route('/main', ['_identifier' => 'main']));

        self::assertFalse(LoginRoute::matches($request));
    }

    #[Test]
    public function doesNotMatchRequestsWithoutBackendRoute(): void
    {
        self::assertFalse(LoginRoute::matches(new ServerRequest()));
    }
}
