<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Authenticator.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Auth;

use Jitesoft\SimpleLogin\Config;
use Jitesoft\SimpleLogin\Cookies\CookieHandlerInterface;
use Jitesoft\SimpleLogin\Crypto\CryptoInterface;
use Jitesoft\SimpleLogin\Sessions\SessionStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Authenticator
 * This class is the entry point of the SimpleLogin system. All the logic is made
 * either inside it or through it.
 */
class Authenticator implements AuthenticatorInterface {

    /** @var LoggerInterface */
    private $logger;
    /** @var string */
    private $rememberTokenFormat = '%s:%s';
    /** @var string */
    private $cookieNameFormat = 'simple_login_cookie_%s';
    /** @var ContainerInterface */
    private $container;

    private function getSessionStorage(): SessionStorageInterface {
        return $this->container[SessionStorageInterface::class];
    }

    private function getCookieHandler(): CookieHandlerInterface {
        return $this->container[CookieHandlerInterface::class];
    }

    private function getCrypto(): CryptoInterface {
        return $this->container[CryptoInterface::class];
    }

    private function getAuthenticableRepository(): AuthenticableRepositoryInterface {
        return $this->container[AuthenticableRepositoryInterface::class];
    }

    public function __construct(?Config $config = null) {
        if ($config === null) {
            $config = new Config();
        }

        /** @var ContainerInterface $container */
        $this->container = $config->container;
    }

    public function getLoggedInAuthenticable(): ?AuthenticableInterface {
        $auth = $this->getSessionStorage()->get('auth');
        if (!$auth) {
            return null;
        }

        return $this->getAuthenticableRepository()->findByIdentifier($auth->identifier);
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
        $auth = $this->getAuthenticableRepository()->findByIdentifier($identifier);
        return $this->getCrypto()->validate($key, $auth->getAuthPassword());
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
        $auth = $this->getAuthenticableRepository()->findByIdentifier($identifier);

        if ($auth === null) {
            return null;
        }

        if ($this->getCrypto()->validate($key, $auth->getAuthPassword())) {
            $this->getSessionStorage()->set('auth', [
                'identifier'      => $auth->getAuthIdentifier()
            ]);

            if ($remember) {
                // Create remember token from random string.
                $userIp = $_SERVER['REMOTE_ADDR'];
                $key    = openssl_random_pseudo_bytes(64);
                $auth->setRememberToken($key);
                $this->getAuthenticableRepository()->setRememberToken(
                    $auth->getAuthIdentifier(),
                    $auth->getRememberToken()
                );
                $this->getCookieHandler()->set(
                   sprintf($this->cookieNameFormat, 'remember_token'),
                   base64_encode(
                       sprintf(
                           $this->rememberTokenFormat,
                           $auth->getAuthIdentifier(),
                           $this->getCrypto()->encrypt(sprintf('%s.%s', $key, $userIp))
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
        $cookie = $this->getCookieHandler()->get(sprintf($this->cookieNameFormat, 'remember_token'));
        if ($cookie === null) {
            return null;
        }

        $userIp  = $_SERVER['REMOTE_ADDR'];
        $decoded = base64_decode($cookie);
        $id      = explode(':', $decoded)[0];
        $key     = explode(':', $decoded)[1];

        $auth = $this->getAuthenticableRepository()->findByIdentifier($id);
        if ($auth === null) {
            return null;
        }

        if ($this->getCrypto()->validate($auth->getRememberToken(), sprintf('%s.%s', $key, $userIp))) {
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
        $auth = $this->getSessionStorage()->get('auth', null);
        if (!$auth) {
            return false;
        }

        if ($auth->identifier !== $authenticable->getAuthIdentifier()) {
            return false;
        }

        $this->getSessionStorage()->set('auth', null);
        return true;
    }

}
