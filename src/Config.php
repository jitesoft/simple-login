<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Config.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin;

use Jitesoft\Container\Container;
use Jitesoft\Log\NullLogger;
use Jitesoft\SimpleLogin\Cookies\CookieHandler;
use Jitesoft\SimpleLogin\Cookies\CookieHandlerInterface;
use Jitesoft\SimpleLogin\Crypto\CryptoInterface;
use Jitesoft\SimpleLogin\Crypto\BlowfishCrypto;
use Jitesoft\SimpleLogin\Sessions\SessionStorage;
use Jitesoft\SimpleLogin\Sessions\SessionStorageInterface;
use Psr\Log\LoggerInterface;

// @codeCoverageIgnoreStart
return [
    // If using another container with set dependencies, change the ContainerInterface dependency to it.
    'Container' => new Container([
        LoggerInterface::class         => new NullLogger(),
        CryptoInterface::class         => new BlowfishCrypto(),
        SessionStorageInterface::class => new SessionStorage(),
        CookieHandlerInterface::class  => new CookieHandler()
    ]),
    'ThrowExceptions' => true
];
// @codeCoverageIgnoreEnd
