<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  CookieHandlerTest.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests\Cookies;

use Carbon\Carbon;
use Jitesoft\SimpleLogin\Cookies\Cookie;
use Jitesoft\SimpleLogin\Cookies\CookieHandler;
use Jitesoft\SimpleLogin\Cookies\CookieHandlerInterface;
use Jitesoft\SimpleLogin\Tests\AbstractTestCase;
use phpmock\Mock;
use ReflectionClass;

class CookieHandlerTest extends AbstractTestCase {

    protected $namespace;
    /** @var CookieHandlerInterface */
    protected $cookieHandler;

    protected function setUp() {
        parent::setUp();
        Carbon::setTestNow(new Carbon('2017-10-01', 'UTC'));
        $this->cookieHandler = $this->container->get(CookieHandlerInterface::class);
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
                $this->assertEquals('{"value":"test-value","lifetime":604800,"domain":"","location":""}', $value);
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
                $this->assertEquals('{"value":"test-value","lifetime":1209600,"domain":"","location":""}', $value);
                $this->assertEquals(Carbon::now()->addWeeks(2)->getTimestamp(), $lifetime);
                $this->assertEquals('', $domain);
                $this->assertEquals('', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7 * 2))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue($this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7 * 2));
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
                $this->assertEquals(
                    '{"value":"test-value","lifetime":604800,"domain":"domain.tld","location":""}',
                    $value
                );
                $this->assertEquals(Carbon::now()->addWeek(1)->getTimestamp(), $lifetime);
                $this->assertEquals('domain.tld', $domain);
                $this->assertEquals('', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tld'))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue(
            $this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tld')
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
                $this->assertEquals(
                    '{"value":"test-value","lifetime":604800,"domain":"domain.tld","location":"\/test"}',
                    $value
                );
                $this->assertEquals(Carbon::now()->addWeek(1)->getTimestamp(), $lifetime);
                $this->assertEquals('domain.tld', $domain);
                $this->assertEquals('/test', $location);
                $this->assertTrue($secure);
                return true;
            }
        );

        $mock->enable();
        $this->assertTrue(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tld', '/test'))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertTrue(
            $this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tld', '/test')
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
        $this->assertFalse(
            $this->cookieHandler->set(new Cookie('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl', '/test'))
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertFalse(
            $this->cookieHandler->set('test-key', 'test-value', 60 * 60 * 24 * 7, 'domain.tdl', '/test')
        );
        $this->assertTrue($called);
        $mock->disable();

    }

    public function testGet() {
        $_COOKIE['test-key'] = '{"value":"test-value","lifetime":604800,"domain":"domain.tld","location":""}';
        $this->assertEquals(
            'test-value',
            $this->cookieHandler->get('test-key')->getValue()
        );

        $this->assertNull($this->cookieHandler->get('abc'));

        unset($_COOKIE['test-key']);
    }

    public function testHas() {

        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'array_key_exists',
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
            'array_key_exists',
            function() use(&$called) {
                $called = true;
                return false;
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

        $mock1 = new Mock(
            $this->namespace,
            'array_key_exists',
            function() use (&$called) {
                $called = true;
                return true;
            }
        );

        $mock3 = new Mock(
            $this->namespace,
            'setcookie',
            function($id, $value, $lifetime) use(&$called2) {
                $called2 = true;
                $this->assertEquals('test-key', $id);
                $this->assertEquals('test-value', $value);
                $this->assertEquals(Carbon::now()->getTimestamp() - 3600, $lifetime);
            }
        );

        $mock1->enable();
        $mock3->enable();
        $this->assertTrue($this->cookieHandler->unset('test-key'));
        $this->assertTrue($called);
        $this->assertTrue($called2);
        $mock1->disable();
        $mock3->disable();
    }

    public function testUnsetFalse() {
        $called = false;
        $mock   = new Mock(
            $this->namespace,
            'array_key_exists',
            function() use (&$called) {
                $called = true;
                return false;
            }
        );

        $mock->enable();
        $this->assertFalse($this->cookieHandler->unset('test-key'));
        $this->assertTrue($called);
        $mock->disable();
    }
}
