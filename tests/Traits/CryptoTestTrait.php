<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CryptoTestTrait.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests\Traits;

use Jitesoft\SimpleLogin\Contracts\CryptoInterface;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait CryptoTestTrait {

    /** @var CryptoInterface */
    protected $implementation;

    /** @var callable */
    protected $testEncryptedValue;

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

}
