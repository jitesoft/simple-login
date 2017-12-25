<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CookieHandler.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\CookieHandler;

use Carbon\Carbon;
use Jitesoft\Log\NullLogger;
use Psr\Log\LoggerInterface;

class CookieHandler implements CookieHandlerInterface {

    private $logger;

    protected function getLogger(): LoggerInterface {
        return $this->logger ?? new NullLogger();
    }

    /**
     * Get a cookie from the cookie handler.
     *
     * @param string $id - Cookie identifier or name.
     * @return Cookie|null - The resulting value as string, null if no cookie was found with given id.
     *
     */
    public function get(string $id): ?Cookie {
        $this->getLogger()->debug('Trying to fetch cookie data from cookie with id {id}.', [
            'id' => $id
        ]);

        if (isset($_COOKIE[$id])) {
            $cookieData = $_COOKIE[$id];
            $cookieData = json_decode($cookieData, true);
            $cookie     = new Cookie(
                $id,
                $cookieData['value'],
                $cookieData['lifetime'] - Carbon::Now()->getTimestamp(),
                $cookieData['domain'],
                $cookieData['location']
            );
            $this->getLogger()->debug('Cookie fetched successfully.');
            return $cookie;
        }
        $this->getLogger()->error('Failed to fetch cookie data for cookie with id {id}.', [
            'id' => $id
        ]);

        return null;
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
                        int $lifetime = 604800,
                        string $domain = '',
                        string $location = ''
    ): bool {

        if ($cookie instanceof Cookie) {
            $value    = $cookie->getValue();
            $lifetime = $cookie->getLifetime();
            $domain   = $cookie->getDomain();
            $location = $cookie->getLocation();
            $cookie   = $cookie->getKey();
        }

        $this->getLogger()->debug('Creating a cookie with id {id} and lifetime {lifetime}.',
            [
                'id'       => $cookie,
                'lifetime' => $lifetime
            ]
        );

        $value = json_encode([
            'value'    => $value,
            'lifetime' => $lifetime,
            'domain'   => $domain,
            'location' => $location
        ]);

        $lifetime = Carbon::now()->getTimestamp() + $lifetime;
        $result   = setcookie($cookie, $value, $lifetime, $domain, $location, true, false);

        return $result;
    }

    /**
     * Check if a cookie with given identifier/name exists.
     *
     * @param string $id - Cookie identifier or name.
     * @return bool      - Result, true if exists, else false.
     */
    public function has(string $id): bool {
        $result = array_key_exists($id, $_COOKIE);
        $this->getLogger()->debug('Checking if cookie with id {id} exists. Result: {result}.', [
            'id'     => $id,
            'result' => $result
        ]);
        return $result;
    }

    /**
     * Remove a given cookie.
     *
     * @param string $cookie - Cookie as cookie object, identifier or name.
     * @return bool          - Result, true if removed else false.
     */
    public function unset($cookie): bool {
        $id = $cookie instanceof Cookie ? $cookie->getValue() : $cookie;
        $this->getLogger()->debug('Removing cookie with id {id}.', ['id' => $id]);
        if (!$this->has($id)) {
            return false;
        }

        setcookie($id, 'test-value', Carbon::now()->getTimestamp() - 3600);
        unset($_COOKIE[$id]);

        return $this->has($id);
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
}
