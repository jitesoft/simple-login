<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AuthenticableRepositoryInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin;

/**
 * Interface AuthenticableRepositoryInterface
 * @package Jitesoft\SimpleLogin\Contracts
 *
 * Interface for service to fetch a Authenticable object.
 */
interface AuthenticableRepositoryInterface {

    /**
     * Find a Authenticable by its identifier.
     *
     * @param string $identifier           - Identifier to select the authenticable from.
     * @return null|AuthenticableInterface - Resulting object or null.
     */
    public function findByIdentifier(string $identifier): ?AuthenticableInterface;

    /**
     * Set a given remember token for a authenticable identifier.
     *
     * @param string $identifier - Identifier that the authenticable is selected from.
     * @param string $token      - Remember token to store.
     * @return bool
     */
    public function setRememberToken(string $identifier, string $token): bool;

}
