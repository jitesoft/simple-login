<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Authenticator.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin;

use Jitesoft\SimpleLogin\Contracts\AuthenticableInterface;
use Jitesoft\SimpleLogin\Contracts\AuthenticableServiceInterface;
use Jitesoft\SimpleLogin\Contracts\AuthenticatorInterface;
use Jitesoft\SimpleLogin\Contracts\CryptoInterface;
use Jitesoft\SimpleLogin\Contracts\SessionStorageInterface;
use Psr\Log\LoggerInterface;

class Authenticator implements AuthenticatorInterface {

    private $sessionStorage;
    private $crypto;
    private $logger;
    private $authenticableService;

    /**
     * Authenticator constructor.
     * @param CryptoInterface $crypto
     * @param SessionStorageInterface $sessionStorage
     * @param LoggerInterface $logger
     * @param AuthenticableServiceInterface $authenticableService
     */
    public function __construct(CryptoInterface $crypto,
                                 SessionStorageInterface $sessionStorage,
                                 LoggerInterface $logger,
                                 AuthenticableServiceInterface $authenticableService) {

        $this->crypto               = $crypto;
        $this->sessionStorage       = $sessionStorage;
        $this->logger               = $logger;
        $this->authenticableService = $authenticableService;
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
        $auth = $this->authenticableService->findByIdentifier($identifier);
        return $this->crypto->validate($key, $auth->getAuthPassword());
    }

    /**
     * Log in a user.
     *
     * @param string $identifier - The user identifier (email, username or the like).
     * @param string $key - The key which is used for authentication, password.
     * @param bool $remember - If a remember token should be stored.
     * @return AuthenticableInterface|null - Result, User object on successful login, else null.
     */
    public function login(string $identifier, string $key, bool $remember = false): ?AuthenticableInterface {
        $auth = $this->authenticableService->findByIdentifier($identifier);

        if ($this->crypto->validate($key, $auth->getAuthPassword())) {
            $this->sessionStorage->set('simple_login_auth', [
                'identifier_name' => $auth->getAuthIdentifierName(),
                'identifier'      => $auth->getAuthIdentifier()
            ]);

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
    }

}
