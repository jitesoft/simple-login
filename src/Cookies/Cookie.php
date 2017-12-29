<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Cookie.php - Part of the simple-login project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\SimpleLogin\Cookies;

/**
 * Class Cookie
 *
 * Data representation of a given cookie.
 */
class Cookie {

    protected $key;

    protected $value;

    protected $lifetime;

    protected $domain;

    protected $location;

    public function __construct(string $key,
                                 string $value,
                                 int $lifetime = 604800,
                                 string $domain = '',
                                 string $location = '') {

        $this->key      = $key;
        $this->value    = $value;
        $this->lifetime = $lifetime;
        $this->domain   = $domain;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getLifetime(): int {
        return $this->lifetime;
    }

    /**
     * @return string
     */
    public function getDomain(): string {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

}
