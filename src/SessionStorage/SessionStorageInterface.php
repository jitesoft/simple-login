<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  ContainerInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\SessionStorage;

use Psr\Log\LoggerAwareInterface as LoggerAware;

/**
 * Interface SessionStorageInterface
 * @package Contracts
 *
 * Contract for session storage implementations.
 */
interface SessionStorageInterface extends LoggerAware {

    /**
     * Fetch a value by its key from the session storage.
     * If the value is not set, the default parameter value will be returned instead.
     *
     * @param string $key     - The key to get the value of.
     * @param mixed  $default - Default value if key is not set.
     * @return mixed          - The resulting value, either the set value or the default parameter.
     */
    public function get(string $key, $default = null);

    /**
     * Set a value to a given key.
     * If the key already exists, it will overwrite the value.
     *
     * @param string $key   - Key to set.
     * @param mixed  $value - Value to set.
     * @return bool         - Result, true if success, false if error.
     */
    public function set(string $key, $value): bool;

    /**
     * @param string $key - Key to unset.
     * @return bool       - Result, true if unset, false if error.
     */
    public function unset(string $key): bool;

    /**
     * Check if the session has a specific key set.
     *
     * @param string $key - Key to check if it is set.
     * @return bool       - Result, true if it exists, false if not set.
     */
    public function has(string $key): bool;

}
