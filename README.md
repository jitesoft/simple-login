# Simple Login

A simple login system which can be used as a standalone package or extended as a package in whatever framework you prefer.

The system is built to be easy to apply to any use case where a login module is required.  
But the default values are quite easy to change.

Simple login comes with a simple dependency injector. The injectors container implements the PSR-11 container interface.  
So it's possible to swap it out for another container.

## Installation

```
composer require jitesoft/simple-login
```

## Usage

### Out of the box

The package is intended to be possible to use without modifying 


### Modifiable parts

#### Authenticable

To be able to use your own models or objects in the login system, the class which is intended to be authenticated
have to implement the `Jitesoft\SimpleLogin\Contracts\AuthenticableInterface` interface.  
If it does not, it is not possible for the system to fetch the required values.  

#### Authenticable repository

For the system to be able to fetch a specific Authenticable, it has to have a AuthenticableRepository which.  
The repository needs to implement the `Jitesoft\SimpleLogin\Contracts\AuthenticableRepositoryInterface`.  
There is one forced method to implement, and it should return a `AuthenticableInterface` based on its identifier.  
The identifier is up to you, if its a email, username, or whatever you feel like.

#### Logger

The default logger is a logger which outputs all log information to nothing. The system can use any PSR-3 implemented logger.  

#### Session storage

The default session storage uses the php SessionHandler class. The `Jitesoft\SimpleLogin\Contracts\SessionStorageInterface` 
can be implemented to use your own session handler.

#### Crypto

By default, the system uses the Blowfish algorithm to encrypt passwords.  
It's possible to change the default algorithm by creating a class implementing the `Jitesoft\SimpleLogin\Contracts\CryptoInterface` interface.
