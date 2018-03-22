<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Config.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin;

use Jitesoft\Container\Container;
use Jitesoft\Container\Exceptions\ContainerException;
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
 * @codeCoverageIgnore
 */
class Config {

    /** @var Container */
    protected $container;

    /**
     * Config constructor.
     * @param array $containerBindings
     * @throws ContainerException
     */
    public function __construct(array $containerBindings = []) {
        $containerBindings = array_merge([
            LoggerInterface::class         => [ 'class' => NullLogger::class, 'singleton' => true ],
            CryptoInterface::class         => [ 'class' => BlowfishCrypto::class, 'singleton' => true ],
            SessionStorageInterface::class => [ 'class' => SessionStorage::class, 'singleton' => true ],
            CookieHandlerInterface::class  => [ 'class' => CookieHandler::class, 'singleton' => true ]
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
