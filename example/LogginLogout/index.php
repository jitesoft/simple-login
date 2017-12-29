<?php
require_once '../../vendor/autoload.php';
use Jitesoft\Container\Container;
use Jitesoft\SimpleLogin\Auth\AuthenticableRepositoryInterface;
use Jitesoft\SimpleLogin\Auth\Authenticator;
use Jitesoft\SimpleLogin\Auth\AuthenticableInterface;
use Jitesoft\SimpleLogin\Auth\AuthenticatorInterface;
use Jitesoft\SimpleLogin\Config;
use Jitesoft\SimpleLogin\Crypto\CryptoInterface;

// In this example we only have a very very simple form for logging in a user named admin with the password admin.
// Due to the nature of the example, I've decided that the container should load a different AuthenticableRepository,
// this so that we can easily just check the given username and password of a "faked" user.

// For the sake of simplicity a new anonymous object is created which inherits the AuthenticableRepositoryInterface.
// Its bound to the interface in the container, so that it will be used when querying for a authenticable.
$authRepoImplementation = new class implements AuthenticableRepositoryInterface {

    public function findByIdentifier(string $identifier): ?AuthenticableInterface {
        // Simple check, is the identifier 'admin', create a new authenticable (also a anonymous class)
        // else null should be returned!
        if ($identifier !== 'admin') {
            return null;
        }

        return new class implements AuthenticableInterface {

            public function getAuthIdentifier() {
                return 'admin';
            }

            public function getAuthPassword(): string {
                // In this example I'm lazy and a new container is created in here.
                // of course this would never be done in a real life case, instead it should just return the
                // users password as stored in the db (encrypted!!!).
                $container = new Container();
                $crypto    = $container->get(CryptoInterface::class); // Blowfish crypto by default.
                return $crypto->encrypt('admin');
            }

            public function getRememberToken(): string {
                return ''; // Ignore remember token, this example does not support it.
            }

            public function setRememberToken(string $token) {
                // Ignore this too, due to same as above.
            }
        };
    }

    public function setRememberToken(string $identifier, string $token): bool {
        return false; // Nope!
    }
};

// A configuration object is not required to be passed to the constructor of the Authenticator, but
// to easily bind the Auth repository to its interface, we create one here and pass our implementation in.
$config = new Config([AuthenticableRepositoryInterface::class => $authRepoImplementation]);

// Due to login functionality the best way to check if a user is logged in is to use
// the AuthenticatorInterface implementation.
/** @var AuthenticatorInterface $authenticator */
$authenticator = new Authenticator($config);

// Check if the 'submit' button is pressed.
if (isset($_POST['submit'])) {
    // Log the user in.
    // In this example, when the authenticator calls login with the supplied username and password, the authenticator
    // will fetch the auth object by identifier (admin) from the repository created above.
    // It will then test the supplied password with the stored password (admin) and return the auth or null.
    if ($authenticator->login($_POST['username'], $_POST['password'], false) === null) {
        echo "Invalid username or password.";
    }
}

// Fetch the logged in authenticable, which is fetched from the session.
// If none exists, null is returned and we can be sure no one is logged in.
$auth = $authenticator->getLoggedInAuthenticable();
if ($auth !== null) {
    // If auth is not null, there is a user logged in!
    if (isset($_POST['logout'])) {
        $authenticator->logout($auth);
        echo 'Logged out.';
    } else {
        ?>
        <body>
            <span><?php printf('User %s is logged in.', $auth->getAuthIdentifier()) ?></span>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <input type="submit" value="Log out!" name="logout"/>
            </form>
        </body>
        <?php
        die();
    }
}
?>
    <body>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password">

        <input type="submit" value="Login!" name="submit">
    </form>
    </body>

