<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  SessionStorageTest.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests\Sessions;

use Jitesoft\SimpleLogin\Sessions\SessionStorage;
use Jitesoft\SimpleLogin\Sessions\SessionStorageInterface;
use Jitesoft\SimpleLogin\Tests\AbstractTestCase;
use phpmock\Mock;

class SessionStorageTest extends AbstractTestCase {

    /** @var SessionStorageInterface */
    private $storage;

    private $mock;

    protected function setUp() {
        parent::setUp();

        $this->mock = new Mock(
            (new \ReflectionClass(SessionStorage::class))->getNamespaceName(),
            'session_status',
            function() {
                return PHP_SESSION_ACTIVE;
            }
        );
        $this->mock->enable();
        $this->storage = $this->container->get(SessionStorageInterface::class);
    }

    protected function tearDown() {
        parent::tearDown();
        $this->mock->disable();
        $_SESSION = [];
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
