<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AuthenticableServiceInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Contracts;

/**
 * Interface AuthenticableServiceInterface
 * @package Jitesoft\SimpleLogin\Contracts
 *
 * Interface for service to fetch a Authenticable object.
 */
interface AuthenticableServiceInterface {

    /**
     * Find a Authenticable by its identifier.
     *
     * @param string $identifier           - Identifier to select the authenticable from.
     * @return null|AuthenticableInterface - Resulting object or null.
     */
    public function findByIdentifier(string $identifier): ?AuthenticableInterface;

}
