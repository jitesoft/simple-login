<?php
namespace Example;

use Jitesoft\SimpleLogin\Auth\AuthenticableInterface;
use Jitesoft\SimpleLogin\Auth\AuthenticableRepositoryInterface;
use PDO;

// This example is intended to show how easily one could create a simple repository for a system which
// uses PDO as db adapter.
// It could just as well use the mysqli API or a mongo wrapper if wanted.

class AuthenticableRepository implements AuthenticableRepositoryInterface {

    private $database;

    public function __construct(PDO $database) {
        // In this case a PDO object is passed through the repository constructor.
        // This could be done through binding PDO and the Repository in the container.
        $this->database = $database;
    }

    /**
     * Find a Authenticable by its identifier.
     *
     * @param string $identifier - Identifier to select the authenticable from.
     * @return null|AuthenticableInterface - Resulting object or null.
     */
    public function findByIdentifier(string $identifier): ?AuthenticableInterface {
        // Simple PDO fetch User class, where the user is a class implementing the AuthenticableInterface.
        $statement = $this->database->prepare('SELECT * FROM users WHERE username=:name');
        $statement->execute(['name' => $identifier]);
        return $statement->fetchObject(User::class);
    }

    /**
     * Set a given remember token for a authenticable identifier.
     *
     * @param string $identifier - Identifier that the authenticable is selected from.
     * @param string $token - Remember token to store.
     * @return bool
     */
    public function setRememberToken(string $identifier, string $token): bool {
        // Simple PDO update where the token and username is passed to the database to update the given user.
        $statement = $this->database->prepare('UPDATE users SET remember_token=:token WHERE username=:name');
        $statement->bindParam('token', $token);
        $statement->bindParam('name', $identifier);
        return $statement->execute();
    }
}
