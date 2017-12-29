# Simple Login

[![Build Status](https://travis-ci.org/jitesoft/simple-login.svg?branch=master)](https://travis-ci.org/jitesoft/simple-login)

[![codecov](https://codecov.io/gh/jitesoft/simple-login/branch/master/graph/badge.svg)](https://codecov.io/gh/jitesoft/simple-login)

## Observe!

This repository is under development and the project is not yet production ready.  

----

A simple login system which can be used as a standalone package or extended as a package in whatever framework you prefer.

Simple login comes with a very simple dependency container. The container implements the PSR-11 container interface. Which makes it possible
to switch to any other PSR-11 compatible container.

## Installation

```
composer require jitesoft/simple-login
```

## Usage

### Out of the box

A few of the interfaces in the package have to be implemented to be able to use the package.  
Those include:

* `Jitesoft\SimpleLogin\Auth\AuthenticableInterface`
* `Jitesoft\SimpleLogin\Auth\AuthenticableRepositoryInterface`

When implemented they have to be added to the package config to be possible to be used by the package.  

The `AuthenticableInterface` is supposed to be implemented by the User model of the project and the `AuthenticableRepositoryInterface` should
handle fetching of the user from whichever database or similar that is used.  
In the example directory, you can find a simple example of a implemented repository using `PDO`.

## Changing the interfaces

Most of the SimpleLogin classes can be changed to your own if wanted.
In most cases the interfaces just have to be bound to your own class and it should work right away.  
Following is a list of interfaces that are used inside the system to make it work:

* `CookieHandlerInterface` (Defaults to normal cookies).
* `CryptoInterface` (Defaults to the blowfish algorithm).
* `SessionStorageInterface` (Defaults to normal sessions).

The default implementations should be sufficient enough for most systems, but they can be easily changed by
setting your bindings when constructing the `Config` object that you pass into the `Jitesoft\SimpleLogin\Auth\Authenticator` constructor.

If you wish to change the Container to another PSR-11 container, you can create a new Config class which inherits from the 
`Jitesoft\SimpleLogin\Config` class. Set the protected `$container` field to your specific Container implementation and it will
be used instead.

## Examples

In the [example](https://github.com/jitesoft/simple-login/tree/develop/example) directory two examples can be found.  

The [LoginLogout](https://github.com/jitesoft/simple-login/tree/develop/example/LoginLogout) example shows how to easily use the system to login and logout from a service. To test it, just serve
the index file via php (`php -S localhost:8000`) and navigate to `localhost:8000` in the browser. The faked user's password and username are both `admin`.

The [Repository](https://github.com/jitesoft/simple-login/tree/develop/example/Repository) example shows how to easily implement your own repository to be used in the system, it uses a `PDO` object to 
handle the database queries.
