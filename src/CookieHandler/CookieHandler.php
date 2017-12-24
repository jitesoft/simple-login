<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CookieHandler.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\CookieHandler;

use Psr\Log\LoggerInterface;

class CookieHandler implements CookieHandlerInterface {

    /**
     * Get a cookie from the cookie handler.
     *
     * @param string $id - Cookie identifier or name.
     * @return null|string  - The resulting value as string, null if no cookie was found with given id.
     */
    public function get(string $id): ?string {

    }

    /**
     * Set a cookie.
     *
     * @param string $id - Cookie identifier or name.
     * @param string $value - Value of the cookie as a string.
     * @param int $lifetime - Cookie lifetime (defaults to 7 days).
     * @param string $domain - Domain, defaults to ''.
     * @param string $location - Server location, defaults to ''.
     * @return bool             - Result, true if success.
     */
    public function set(string $id,
                        string $value,
                        int $lifetime = (60 * 60 * 24 * 7),
                        string $domain = '',
                        string $location = ''
    ): bool {

    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger) {

    }

}
