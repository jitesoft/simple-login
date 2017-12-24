<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CryptoInterface.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Crypto;

use Psr\Log\LoggerAwareInterface as LoggerAware;

/**
 * Interface CryptoInterface
 * @package Contracts
 *
 * Interface for encrypting passwords and similar type of values.
 * Recommended to use a one-way encryption algorithm such as blowfish or argon2i.
 */
interface CryptoInterface extends LoggerAware {

    /**
     * Encrypt a value.
     *
     * @param string $value - Value to encrypt.
     * @return string       - Resulting encrypted string.
     */
    public function encrypt(string $value): string;

    /**
     * Compare a none-encrypted value with a encrypted value.
     *
     * @alias validate
     * @param string $value     - Value to validate.
     * @param string $encrypted - Encrypted value.
     * @return bool             - Result, true if same else false.
     */
    public function compare(string $value, string $encrypted): bool;

    /**
     * Compare a none-encrypted value with a encrypted value.
     *
     * @alias compare
     * @param string $value     - Value to validate.
     * @param string $encrypted - Encrypted value.
     * @return bool             - Result, true if same else false.
     */
    public function validate(string $value, string $encrypted): bool;

}
