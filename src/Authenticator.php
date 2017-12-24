<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Authenticator.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin;

use Jitesoft\Container\Container;
use Jitesoft\SimpleLogin\CookieHandler\CookieHandlerInterface;
use Jitesoft\SimpleLogin\Crypto\CryptoInterface;
use Jitesoft\SimpleLogin\SessionStorage\SessionStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Authenticator implements AuthenticatorInterface {

    /** @var SessionStorageInterface */
    private $sessionStorage;
    /** @var CryptoInterface */
    private $crypto;
    /** @var LoggerInterface */
    private $logger;
    /** @var AuthenticableRepositoryInterface */
    private $authenticableRepository;
    /** @var string */
    private $rememberTokenFormat = '%s:%s';
    /** @var string */
    private $cookieNameFormat = 'simple_login_cookie_%s';
    /** @var CookieHandlerInterface */
    private $cookieHandler;

    private function getConfig() {
        return require_once dirname(__FILE__) . "/Config.php";
    }

    public function __construct() {
        $config    = $this->getConfig();
        $container = $config[ContainerInterface::class];

        if ($container instanceof Container) {
            foreach ($config['Dependencies'] as $interface => $value) {
                $container->set($interface, $value);
            }
        }

        $this->crypto                  = $container->get(CryptoInterface::class);
        $this->sessionStorage          = $container->get(SessionStorageInterface::class);
        $this->logger                  = $container->get(LoggerInterface::class);
        $this->authenticableRepository = $container->get(AuthenticableRepositoryInterface::class);
        //$this->cookieHandler           = $container->get(CookieHandlerInterface::class);
    }

    public function getLoggedInAuthenticable(): ?AuthenticableInterface {
        $auth = $this->sessionStorage->get('auth');
        return $this->authenticableRepository->findByIdentifier($auth->identifier);
    }

    /**
     * @param SessionStorageInterface $sessionStorage
     * @return mixed
     */
    public function setSessionStorage(SessionStorageInterface $sessionStorage) {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Authenticate a user
     *
     * @param string $identifier
     * @param string $key
     * @return bool
     */
    public function authenticate(string $identifier, string $key): bool {
        // Fetch the user from the auth service.
        $auth = $this->authenticableRepository->findByIdentifier($identifier);
        return $this->crypto->validate($key, $auth->getAuthPassword());
    }

    /**
     * Log in a user.
     *
     * @param string $identifier - The user identifier (email, username or the like).
     * @param string $key        - The key which is used for authentication, password.
     * @param bool $remember     - If a remember token should be stored.
     * @return AuthenticableInterface|null - Result, User object on successful login, else null.
     */
    public function login(string $identifier, string $key, bool $remember = false): ?AuthenticableInterface {
        $auth = $this->authenticableRepository->findByIdentifier($identifier);

        if ($auth === null) {
            return null;
        }

        if ($this->crypto->validate($key, $auth->getAuthPassword())) {
            $this->sessionStorage->set('auth', [
                'identifier'      => $auth->getAuthIdentifier()
            ]);

            if ($remember) {
                // Create remember token from random string.
                $key = openssl_random_pseudo_bytes(64);
                $auth->setRememberToken($key);
                $this->authenticableRepository->setRememberToken($auth->getAuthIdentifier(), $auth->getRememberToken());
                $this->cookieHandler->set(
                   sprintf($this->cookieNameFormat, 'remember_token'),
                   base64_encode(
                       sprintf(
                           $this->rememberTokenFormat,
                           $auth->getAuthIdentifier(),
                           $this->crypto->encrypt($key)
                       )
                   )
                );
            }

            return $auth;
        }

        return null;
    }

    /**
     * Log a authenticable in using a remember token.
     * The cookie is fetched from the CookieHandlerInterface implementation set in the container.
     *
     * @return AuthenticableInterface|null
     */
    public function cookieLogin(): ?AuthenticableInterface {
        $cookie = $this->cookieHandler->getCookie(sprintf($this->cookieNameFormat, 'remember_token'));
        if ($cookie === null) {
            return null;
        }

        $decoded = base64_decode($cookie);
        $id      = explode(':', $decoded)[0];
        $key     = explode(':', $decoded)[1];

        $auth = $this->authenticableRepository->findByIdentifier($id);
        if ($auth === null) {
            return null;
        }

        if ($this->crypto->validate($auth->getRememberToken(), $key)) {
            return $auth;
        }

        return null;
    }

    /**
     * Log out given authenticable from the system.
     * If null, the currently logged in will be logged out.
     *
     * @param AuthenticableInterface|null $authenticable - The authenticable to log out.
     * @return bool                                      - Result, true if successful, else false.
     */
    public function logout(?AuthenticableInterface $authenticable = null): bool {
        $auth = $this->sessionStorage->get('simple_login_auth', null);
        if (!$auth) {
            return false;
        }

        if ($auth['identifier'] !== $authenticable->getAuthIdentifier()) {
            return false;
        }

        $this->sessionStorage->set('simple_login_auth', null);
        return true;
    }

}
