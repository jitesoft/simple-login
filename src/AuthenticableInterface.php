<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AuthenticableInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin;

/**
 * Interface AuthenticableInterface
 * @package Contracts
 *
 * Interface for models which can be authenticated by the simple login system.
 */
interface AuthenticableInterface {

    /**
     * Get the unique identifier for the Authenticable.
     *
     * @return mixed - Identifier.
     */
    public function getAuthIdentifier();

    /**
     * Get the Authenticable password.
     *
     * @return string - Password.
     */
    public function getAuthPassword(): string;

    /**
     * Get the value used to retrieve the "remember me" session object.
     *
     * @return string - Token.
     */
    public function getRememberToken(): string;

    /**
     * Set token which is used to authenticate the Authenticable with the session.
     *
     * @param string $token - The token value.
     * @return void
     */
    public function setRememberToken(string $token);

}
