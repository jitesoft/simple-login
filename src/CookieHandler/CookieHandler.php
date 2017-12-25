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
     * @param mixed $default - Default value to return cookie does not contain any data.
     * @return Cookie|null   - The resulting value as string, null if no cookie was found with given id.
     */
    public function get(string $id, $default = null): ?Cookie {
        // TODO: Implement get() method.
    }

    /**
     * Set a cookie.
     *
     * @param string|Cookie $cookie - Cookie as a cookie object, identifier or name.
     * @param string $value - Value of the cookie as a string.
     * @param int $lifetime - Cookie lifetime (defaults to 7 days).
     * @param string $domain - Domain, defaults to ''.
     * @param string $location - Server location, defaults to ''.
     * @return bool                 - Result, true if success.
     */
    public function set($cookie,
                        string $value = '',
                        int $lifetime = (60 * 60 * 24 * 7),
                        string $domain = '',
                        string $location = ''
    ): bool {
        // TODO: Implement set() method.
    }

    /**
     * Check if a cookie with given identifier/name exists.
     *
     * @param string $id - Cookie identifier or name.
     * @return bool      - Result, true if exists, else false.
     */
    public function has(string $id): bool {
        // TODO: Implement has() method.
    }

    /**
     * Remove a given cookie.
     *
     * @param string $cookie - Cookie as cookie object, identifier or name.
     * @return bool          - Result, true if removed else false.
     */
    public function unset($cookie): bool {
        // TODO: Implement unset() method.
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger) {
        // TODO: Implement setLogger() method.
    }
}
