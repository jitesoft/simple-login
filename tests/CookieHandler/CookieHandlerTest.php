<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CookieHandlerTest.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests\CookieHandler;

use Carbon\Carbon;
use Jitesoft\SimpleLogin\CookieHandler\Cookie;
use Jitesoft\SimpleLogin\CookieHandler\CookieHandler;
use Jitesoft\SimpleLogin\CookieHandler\CookieHandlerInterface;
use phpmock\Mock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CookieHandlerTest extends TestCase {

    protected $namespace;
    /** @var CookieHandlerInterface */
    protected $cookieHandler;

    protected function setUp() {
        parent::setUp();
        Carbon::setTestNow(new Carbon('2017-10-01', 'UTC'));
        $this->cookieHandler = new CookieHandler();
        $this->namespace     = (new ReflectionClass(CookieHandler::class))->getNamespaceName();
    }

    public function testSet() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'setcookie',
            function($key, $value, $lifetime, $domain, $location, $secure) use(&$called) {
                $called = true;

                $this->assertEquals('test-key', $key);
                $this->assertEquals('test-value', $value);
                $this->assertEquals(Carbon::now()->addWeek(1)->getTimestamp(), $lifetime);
                $this->assertEquals('', $domain);
                $this->assertEquals('', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue($this->cookieHandler->set(new Cookie('test-key', 'test-value')));
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue($this->cookieHandler->set('test-key', 'test-value'));
        $this->assertTrue($called);
        $mock->disable();
    }

    public function testSetDefineLifetime() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'setcookie',
            function($key, $value, $lifetime, $domain, $location, $secure) use(&$called) {
                $called = true;

                $this->assertEquals('test-key', $key);
                $this->assertEquals('test-value', $value);
                $this->assertEquals(Carbon::now()->addWeek(2)->getTimestamp(), $lifetime);
                $this->assertEquals('', $domain);
                $this->assertEquals('', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 60 * 24 * 7 * 2))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue($this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 60 * 24 * 7 * 2));
        $this->assertTrue($called);
        $mock->disable();
    }

    public function testSetWithDomain() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'setcookie',
            function($key, $value, $lifetime, $domain, $location, $secure) use(&$called) {
                $called = true;

                $this->assertEquals('test-key', $key);
                $this->assertEquals('test-value', $value);
                $this->assertEquals(Carbon::now()->addWeek(1)->getTimestamp(), $lifetime);
                $this->assertEquals('domain.tdl', $domain);
                $this->assertEquals('', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl'))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue(
            $this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl')
        );
        $this->assertTrue($called);
        $mock->disable();
    }

    public function testSetWithLocation() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'setcookie',
            function($key, $value, $lifetime, $domain, $location, $secure) use(&$called) {
                $called = true;

                $this->assertEquals('test-key', $key);
                $this->assertEquals('test-value', $value);
                $this->assertEquals(Carbon::now()->addWeek(1)->getTimestamp(), $lifetime);
                $this->assertEquals('domain.tdl', $domain);
                $this->assertEquals('/test', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl', '/test'))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue(
            $this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl', '/test')
        );
        $this->assertTrue($called);
        $mock->disable();
    }

    public function testSetFail() {

        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'setcookie',
            function() use(&$called) {
                $called = true;
                return false;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl', '/test'))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue(
            $this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl', '/test')
        );
        $this->assertTrue($called);
        $mock->disable();

    }

    public function testGet() {
        $_COOKIE['test-key'] = 'test-value';
        $this->assertEquals(
            'test-value',
            $this->cookieHandler->get('test-key')
        );

        $this->assertEquals('meh', $this->cookieHandler->get('abc', 'meh'));

        unset($_COOKIE['test-key']);
    }

    public function testHas() {

        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'isset',
            function() use(&$called) {
                $called = true;
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue($this->cookieHandler->has('test-id'));
        $this->assertTrue($called);
        $mock->disable();
    }

    public function testHasNot() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'isset',
            function() use(&$called) {
                $called = true;
                return false();
            }
        );

        $mock->enable();
        $this->assertFalse($this->cookieHandler->has('test-id'));
        $this->assertTrue($called);
        $mock->disable();
    }

    public function testUnset() {
        $called  = false;
        $called2 = false;
        $called3 = false;

        $mock1 = new Mock(
            $this->namespace,
            'isset',
            function() use (&$called) {
                $called = true;
                return true;
            }
        );

        $mock2 = new Mock(
            $this->namespace,
            'unset',
            function() use (&$called2) {
                $called2 = true;
            }
        );

        $mock3 = new Mock(
            $this->namespace,
            'setcookie',
            function($id, $value, $lifetime) use(&$called3) {
                $called3 = true;
                $this->assertEquals('test-key', $id);
                $this->assertEquals('test-value', $value);
                $this->assertEquals(Carbon::now()->getTimestamp() - 3600, $lifetime);
            }
        );

        $mock1->enable();
        $mock2->enable();
        $mock3->enable();
        $this->assertTrue($this->cookieHandler->unset('test-key'));
        $this->assertTrue($called);
        $this->assertTrue($called2);
        $this->assertTrue($called3);
        $mock1->disable();
        $mock2->disable();
        $mock3->disable();
    }

    public function testUnsetFalse() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'isset',
            function() use (&$called) {
                $called = true;
                return true;
            }
        );

        $mock->enable();
        $this->assertFalse($this->cookieHandler->unset('test-key'));
        $this->assertTrue($called);
        $mock->disable();
    }
}
