<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  BlowfishCryptoTest.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests\Crypto;

use Jitesoft\SimpleLogin\Crypto\BlowfishCrypto;
use Jitesoft\SimpleLogin\Crypto\CryptoInterface;
use Jitesoft\SimpleLogin\Tests\Traits\CryptoTestTrait;
use PHPUnit\Framework\TestCase;

class BlowfishCryptoTest extends TestCase {

    /** @var CryptoInterface */
    protected $implementation;
    /** @var callable */
    protected $testEncryptedValue;

    protected function setUp() {
        parent::setUp();

        $this->implementation     = new BlowfishCrypto();
        $this->testEncryptedValue = function(string $value) {
            $info = password_get_info($value);
            $this->assertEquals(PASSWORD_BCRYPT, $info['algo']);
        };
    }

    public function testEncrypt() {
        $result = $this->implementation->encrypt("value");
        $this->assertInternalType("string", $result);
        if (!$this->testEncryptedValue) {
            call_user_func($this->testEncryptedValue, $result);
        }
    }

    public function testCompare() {
        $encrypted = $this->implementation->encrypt("abc123");
        $result    = $this->implementation->compare("abc123", $encrypted);
        $result2   = $this->implementation->compare("123abc", $encrypted);
        $this->assertTrue($result);
        $this->assertFalse($result2);

    }

    public function testValidate() {
        $encrypted = $this->implementation->encrypt("abc123");
        $result    = $this->implementation->validate("abc123", $encrypted);
        $result2   = $this->implementation->validate("123abc", $encrypted);
        $this->assertTrue($result);
        $this->assertFalse($result2);
    }

    public function testValidateInvalidAlgo() {
        $this->assertFalse($this->implementation->validate('abc123', 'abc453'));
    }
}
