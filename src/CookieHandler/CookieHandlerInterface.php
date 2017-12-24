<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CookieHandlerInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\CookieHandler;

use Psr\Log\LoggerAwareInterface;

/**
 * Interface CookieHandlerInterface
 * @package Jitesoft\SimpleLogin\Contracts
 *
 * Contract for cookie handler implementations.
 */
interface CookieHandlerInterface extends LoggerAwareInterface {

    /**
     * Get a cookie from the cookie handler.
     *
     * @param string $id    - Cookie identifier or name.
     * @param null $default - Default value to return cookie does not contain any data.
     * @return null|string  - The resulting value as string, null if no cookie was found with given id.
     */
    public function get(string $id, $default = null): ?string;

    /**
     * Set a cookie.
     *
     * @param string $id        - Cookie identifier or name.
     * @param string $value     - Value of the cookie as a string.
     * @param int    $lifetime  - Cookie lifetime (defaults to 7 days).
     * @param string $domain    - Domain, defaults to ''.
     * @param string $location  - Server location, defaults to ''.
     * @return bool             - Result, true if success.
     */
    public function set(string $id,
                         string $value,
                         int $lifetime = (60 * 60 * 24 * 7),
                         string $domain = '',
                         string $location = ''
    ): bool;

    /**
     * Check if a cookie with given identifier/name exists.
     *
     * @param string $id - Cookie identifier or name.
     * @return bool      - Result, true if exists, else false.
     */
    public function has(string $id): bool;

    /**
     * Remove a given cookie.
     *
     * @param string $id - Cookie identifier or name.
     * @return bool      - Result, true if removed else false.
     */
    public function unset(string $id): bool;

}
