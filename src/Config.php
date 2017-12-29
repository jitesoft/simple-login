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

/**
 * Class Config
 *
 * @property Container $container
 */
class Config {

    protected $container;

    public function __construct(array $containerBindings = []) {
        $containerBindings = array_merge([
            LoggerInterface::class         => new NullLogger(),
            CryptoInterface::class         => new BlowfishCrypto(),
            SessionStorageInterface::class => new SessionStorage(),
            CookieHandlerInterface::class  => new CookieHandler()
        ], $containerBindings);

        $this->container = new Container($containerBindings);
    }

    public function __get($val) {
        if (property_exists(self::class, $val)) {
            return $this->{$val};
        }

        return null;
    }

}
