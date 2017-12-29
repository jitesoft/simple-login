<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  SessionStorage.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Sessions;

use Jitesoft\Log\NullLogger;
use Psr\Log\LoggerInterface;

/**
 * Class SessionStorage
 *
 * Session storage implementation using the native php SessionHandler class as session handler.
 */
class SessionStorage implements SessionStorageInterface {

    private $sessionFromat = '%s';

    protected $logger;

    /**
     * SessionStorage constructor.
     * @codeCoverageIgnore
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface {
        return $this->logger ?? new NullLogger();
    }

    /**
     * Fetch a value by its key from the session storage.
     * If the value is not set, the default parameter value will be returned instead.
     *
     * @param string $key     - The key to get the value of.
     * @param mixed $default  - Default value if key is not set.
     * @return mixed          - The resulting value, either the set value or the default parameter.
     */
    public function get(string $key, $default = null) {
        $this->getLogger()->debug("Trying to fetch value from session with key {key}.", ['key'=> $key]);

        $key   = sprintf($this->sessionFromat, $key);
        $value = isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        if ($value === null) {
            $this->getLogger()->error("Failed to get session value with key {key}.", ['key' => $key]);
            return $default;
        }

        return json_decode($value);
    }

    /**
     * Set a value to a given key.
     * If the key already exists, it will overwrite the value.
     *
     * @param string $key   - Key to set.
     * @param mixed $value  - Value to set.
     * @return bool         - Result, true if success, false if error.
     */
    public function set(string $key, $value): bool {
        $this->getLogger()->debug("Trying to write session variable with key {key}.", ['key' => $key]);
        $_SESSION[sprintf($this->sessionFromat, $key)] = json_encode($value);
        return true;
    }

    /**
     * @param string $key - Key to unset.
     * @return bool       - Result, true if unset, false if error.
     */
    public function unset(string $key): bool {
        $this->getLogger()->debug('Attempting to destroy session with key {key}.', ['key' => $key]);

        $key = sprintf($this->sessionFromat, $key);
        if (!isset($_SESSION[$key])) {
            return false;
        }

        unset($_SESSION[$key]);
        return true;
    }

    /**
     * Check if the session has a specific key set.
     *
     * @param string $key - Key to check if it is set.
     * @return bool       - Result, true if it exists, false if not set.
     */
    public function has(string $key): bool {
        return array_key_exists(sprintf($this->sessionFromat, $key), $_SESSION);
    }

}
