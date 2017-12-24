<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  SessionStorageTest.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests\SessionStorage;

use Jitesoft\SimpleLogin\SessionStorage\SessionStorage;
use Jitesoft\SimpleLogin\SessionStorage\SessionStorageInterface;
use phpmock\Mock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SessionStorageTest extends TestCase {

    /** @var SessionStorageInterface */
    private $storage;

    protected function setUp() {
        parent::setUp();

        $this->storage = new SessionStorage();
    }

    protected function tearDown() {
        parent::tearDown();
        session_destroy();
    }

    public function testGetDefault() {
        $out = $this->storage->get('key', 'value');
        $this->assertEquals('value', $out);
    }

    public function testGet() {
        $_SESSION['key'] = json_encode('value'); // The storage encodes on set, so need to do that here too.
        $out             = $this->storage->get('key', null);
        $this->assertEquals('value', $out);
    }

    public function testSet() {
        $this->assertTrue($this->storage->set('key', 'value'));
        $this->assertEquals('value', json_decode($_SESSION['key']));
    }

    public function testSetOverwrite() {
        $this->assertTrue($this->storage->set('key', 'value'));
        $this->assertEquals('value', json_decode($_SESSION['key']));
        $this->assertTrue($this->storage->set('key', 'another_value'));
        $this->assertEquals('another_value', json_decode($_SESSION['key']));
    }

    public function testUnset() {
        $_SESSION['key'] = 'value';
        $this->assertTrue(isset($_SESSION['key']));
        $this->assertTrue($this->storage->unset('key'));
        $this->assertFalse(isset($_SESSION['key']));
    }

    public function testUnsetNoEntry() {
        $this->assertFalse($this->storage->unset('key'));
    }

    public function testHas() {
        $_SESSION['key'] = 'value';
        $this->assertTrue($this->storage->has('key'));
    }

    public function testHasNot() {
        $this->assertFalse($this->storage->has('key'));
    }

}