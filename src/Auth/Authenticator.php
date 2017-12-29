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
    /** @var SessionStorageInterface */
    private $sessionStorage;
    /** @var CookieHandlerInterface */
    private $cookieHandler;
    /** @var AuthenticableRepositoryInterface */
    private $authRepository;
    /** @var CryptoInterface */
    private $crypto;

    public function __construct(?Config $config = null) {
        if ($config === null) {
            $config = new Config();
        }

        /** @var ContainerInterface $container */
        $this->container = $config->container;

        // Fetch required interfaces from container.
        $this->logger         = $this->container[LoggerInterface::class];
        $this->sessionStorage = $this->container[SessionStorageInterface::class];
        $this->cookieHandler  = $this->container[CookieHandlerInterface::class];
        $this->crypto         = $this->container[CryptoInterface::class];
        $this->authRepository = $this->container[AuthenticableRepositoryInterface::class];
    }

    public function getLoggedInAuthenticable(): ?AuthenticableInterface {
        $this->logger->debug('Attempting to fetch currently logged in authenticable.');
        $auth = $this->sessionStorage->get('auth');
        if (!$auth) {
            $this->logger->debug('No authenticable currently logged in.');
            return null;
        }

        $this->logger->debug('Successfully fetched logged in authenticable.');
        return $this->authRepository->findByIdentifier($auth->identifier);
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
        $this->logger->debug('Authenticating a given id:key pair.');
        // Fetch the user from the auth service.
        $auth   = $this->authRepository->findByIdentifier($identifier);
        $result = $this->crypto->validate($key, $auth->getAuthPassword());
        $this->logger->debug('Result: {result}.', ['result' => $result]);
        return $result;
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
        $this->logger->debug('Trying to log in authenticable with identifier: {id}', ['id' => $identifier]);
        $auth = $this->authRepository->findByIdentifier($identifier);

        if ($auth === null) {
            $this->logger->error('No authenticable with the identifier {id} could be found.', ['id' => $identifier]);
            return null;
        }

        if ($this->crypto->validate($key, $auth->getAuthPassword())) {
            $this->logger->debug(
                'The authenticable with identifier {id} was successfully validated.',
                ['id' => $identifier]
            );
            $this->sessionStorage->set('auth', [
                'identifier'      => $auth->getAuthIdentifier()
            ]);

            if ($remember) {
                $this->logger->debug('Creating remember token.');
                // Create remember token from random string.
                $userIp = $_SERVER['REMOTE_ADDR'];
                $key    = openssl_random_pseudo_bytes(64);
                $auth->setRememberToken($key);
                $this->authRepository->setRememberToken(
                    $auth->getAuthIdentifier(),
                    $auth->getRememberToken()
                );
                $this->cookieHandler->set(
                   sprintf($this->cookieNameFormat, 'remember_token'),
                   base64_encode(
                       sprintf(
                           $this->rememberTokenFormat,
                           $auth->getAuthIdentifier(),
                           $this->crypto->encrypt(sprintf('%s.%s', $key, $userIp))
                       )
                   )
                );
            }

            $this->logger->debug('Returning the result.');
            return $auth;
        }

        $this->logger->error(
            'Failed to validate the password of authenticable with identifier {id}',
            ['id' => $identifier]
        );
        return null;
    }

    /**
     * Log a authenticable in using a remember token.
     * The cookie is fetched from the CookieHandlerInterface implementation set in the container.
     *
     * @return AuthenticableInterface|null
     */
    public function cookieLogin(): ?AuthenticableInterface {
        $this->logger->debug('Attempting to log a authenticable in via their remember token.');
        $cookie = $this->cookieHandler->get(sprintf($this->cookieNameFormat, 'remember_token'));
        if ($cookie === null) {
            $this->logger->warning('No cookie found.');
            return null;
        }

        $userIp  = $_SERVER['REMOTE_ADDR'];
        $decoded = base64_decode($cookie);
        $id      = explode(':', $decoded)[0];
        $key     = explode(':', $decoded)[1];

        $auth = $this->authRepository->findByIdentifier($id);
        if ($auth === null) {
            $this->logger->error('Authenticable with id from token could not be found.');
            return null;
        }

        if ($this->crypto->validate($auth->getRememberToken(), sprintf('%s.%s', $key, $userIp))) {
            $this->logger->debug('Successfully verified the token and authenticable.');
            return $auth;
        }

        $this->logger->warning('Failed to log in authenticable with remember token.');
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
        $this->logger->debug('Starting logout procedure.');
        if ($authenticable === null) {
            $authenticable = $this->sessionStorage->get('auth', null);
        }

        if (!$authenticable) {
            $this->logger->error('Failed to log out authenticable, could not found authenticable.');
            return false;
        }

        $this->logger->debug('Authenticable found, identifier {id}.', $authenticable->getAuthIdentifier());
        $this->sessionStorage->set('auth', null);
        return true;
    }

}
