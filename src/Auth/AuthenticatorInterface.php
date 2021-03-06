<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AuthenticatiorInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Auth;

use Psr\Log\LoggerAwareInterface as LoggerAware;

/**
 * Interface AuthenticatorInterface
 * @package Contracts
 *
 * Interface for Authenticator services.
 */
interface AuthenticatorInterface extends LoggerAware {

    /**
     * Get currently logged in authenticable.
     *
     * @return AuthenticableInterface|null
     */
    public function getLoggedInAuthenticable(): ?AuthenticableInterface;

    /**
     * Authenticate a user
     *
     * @param string $identifier
     * @param string $key
     * @return bool
     */
    public function authenticate(string $identifier, string $key): bool;

    /**
     * Log in a user.
     *
     * @param string $identifier - The user identifier (email, username or the like).
     * @param string $key        - The key which is used for authentication, password.
     * @param bool   $remember   - If a remember token should be stored.
     * @return AuthenticableInterface|null - Result, User object on successful login, else null.
     */
    public function login(string $identifier, string $key, bool $remember = false): ?AuthenticableInterface;

    /**
     * Log a authenticable in using a remember token.
     * The cookie is fetched from the CookieHandlerInterface implementation set in the container.
     *
     * @return AuthenticableInterface|null
     */
    public function cookieLogin(): ?AuthenticableInterface;

    /**
     * Log out given authenticable from the system.
     * If null, the currently logged in will be logged out.
     *
     * @param AuthenticableInterface|null $authenticable - The authenticable to log out.
     * @return bool                                      - Result, true if successful, else false.
     */
    public function logout(?AuthenticableInterface $authenticable = null): bool;

}
