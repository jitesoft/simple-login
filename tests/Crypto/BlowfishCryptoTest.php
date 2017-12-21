<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  BlowfishCryptoTest.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

namespace Jitesoft\SimpleLogin\Tests\Crypto;

use Jitesoft\SimpleLogin\Crypto\BlowfishCrypto;
use Jitesoft\SimpleLogin\Tests\TestLogger;
use Jitesoft\SimpleLogin\Tests\Traits\CryptoTestTrait;
use PHPUnit\Framework\TestCase;

class BlowfishCryptoTest extends TestCase {
    use CryptoTestTrait;

    protected function setUp() {
        parent::setUp();

        $this->implementation = new BlowfishCrypto();
        $this->implementation->setLogger(new TestLogger());

        $this->testEncryptedValue = function(string $value) {
            $info = password_get_info($value);
            $this->assertEquals(PASSWORD_BCRYPT, $info['algo']);
        };
    }


}
