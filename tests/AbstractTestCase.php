<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AbstractTestCase.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Tests;

use Jitesoft\Container\Container;
use Jitesoft\SimpleLogin\Config;
use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase {

    /** @var Container */
    protected $container;

    protected function setUp() {
        parent::setUp();

        $this->container = (new Config())->container;
    }

    protected function tearDown() {
        parent::tearDown();

        Container::removeContainer('simple_login');
    }


}
