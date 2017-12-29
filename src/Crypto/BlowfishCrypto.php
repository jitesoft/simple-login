<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  BlowfishCrypto.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Crypto;

use Jitesoft\Log\NullLogger;
use Psr\Log\LoggerInterface;

/**
 * Class BlowfishCrypto
 *
 * Blowfish implementation of the CryptoInterface contract.
 */
class BlowfishCrypto implements CryptoInterface {

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
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

    private function getLogger(): LoggerInterface {
        return $this->logger ?? new NullLogger();
    }

    /**
     * Encrypt a value.
     *
     * @param string $value - Value to encrypt.
     * @return string       - Resulting encrypted string.
     */
    public function encrypt(string $value): string {
        $this->getLogger()->debug("Encrypting value using Blowfish algorithm");
        $value = password_hash($value, PASSWORD_BCRYPT);
        $this->getLogger()->debug("Encryption complete.");
        return $value;
    }

    /**
     * Compare a none-encrypted value with a encrypted value.
     *
     * @alias validate
     * @param string $value - Value to validate.
     * @param string $encrypted - Encrypted value.
     * @return bool             - Result, true if same else false.
     */
    public function compare(string $value, string $encrypted): bool {
        $this->getLogger()->debug("Comparing two values using the Blowfish algorithm.");

        // Validate hash type.
        $info = password_get_info($encrypted);
        if ($info['algo'] !== PASSWORD_BCRYPT) {
            $this->getLogger()->error("Invalid algorithm in the encrypted value. Expected {exp}, got {algo}",
                ['exp' => 'Blowfish', 'algo' => $info['algoName']]
            );
            return false;
        }

        if (!password_verify($value, $encrypted)) {
            $this->getLogger()->error("Comparision failed. Values where not identical.");
            return false;
        }

        $this->getLogger()->debug("Comparision complete.");
        return true;
    }

    /**
     * Compare a none-encrypted value with a encrypted value.
     *
     * @alias compare
     * @param string $value - Value to validate.
     * @param string $encrypted - Encrypted value.
     * @return bool             - Result, true if same else false.
     */
    public function validate(string $value, string $encrypted): bool {
        $this->getLogger()->debug("Validation method called.");
        $result = $this->compare($value, $encrypted);
        $this->getLogger()->debug("Validation method finished.");
        return $result;
    }

}
