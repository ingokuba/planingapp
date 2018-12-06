<?php

class User extends Entity
{

    /**
     * Entity type/table name.
     *
     * @var string
     */
    public static $USER = "User";

    /**
     * The first name of the person.
     */
    public static $GIVENNAME = "givenName";

    /**
     * The last name of the person.
     */
    public static $SURNAME = "surname";

    /**
     * The <b>unique</b> email and login of the user.
     */
    public static $EMAIL = "email";

    /**
     * The login password of the user.
     */
    public static $PASSWORD = "password";

    /**
     * Generated token to identify user from session value.
     */
    public static $TOKEN = "token";

    /**
     * Create timestamp.
     * Read only.
     */
    public static $CREATED_AT = "createdAt";

    protected function initializeEntityType(): string
    {
        return User::$USER;
    }

    protected function initializeAttributes(): array
    {
        return array(
            User::$GIVENNAME,
            User::$SURNAME,
            User::$EMAIL,
            User::$PASSWORD,
            User::$CREATED_AT,
            User::$TOKEN
        );
    }

    /**
     * Constraints:
     * <ul>
     * <li>givenName
     * <ul>
     * <li>notnullable
     * </ul>
     * <li>surname
     * <ul>
     * <li>notnullable
     * </ul>
     * <li>email
     * <ul>
     * <li>notnullable
     * <li>unique
     * </ul>
     * <li>password
     * <ul>
     * <li>notnullable
     * </ul>
     */
    protected function checkConstraints(): string
    {
        $message = $this->isEmpty(array(
            User::$GIVENNAME,
            User::$SURNAME,
            User::$EMAIL,
            User::$PASSWORD
        ));
        // email must be unique:
        $email = $this->getValue(User::$EMAIL);
        $result = $this->database->select(User::$USER, "*", User::$EMAIL . "='$email'");
        if ($result != null) {
            $message .= "Email must be unique. ";
        }
        // token must be unique:
        $token = $this->getValue(User::$TOKEN);
        if (isset($token)) {
            $result = $this->database->select(User::$USER, "*", User::$TOKEN . "='$token'");
            if ($result != null) {
                $message .= "Token must be unique. ";
            }
        }
        $createdAt = $this->getValue(User::$CREATED_AT);
        if (! empty($createdAt)) {
            $message .= "Attribute 'createdAt' should not be set for storing.!";
        }
        return $message;
    }

    /**
     * Hash the password before storing the entity.
     */
    public function store(): string
    {
        $password = $this->getValue(User::$PASSWORD);
        $this->setValue(User::$PASSWORD, Util::hashPassword($password));
        return parent::store();
    }

    /**
     * Login the user to the session.
     *
     * @param bool $withCredentials
     *            If set to true, the credentials of the user will be checked with the database.
     * @return string Error message.
     */
    public function login(bool $withCredentials): string
    {
        if (! $withCredentials) {
            $this->generateToken();
            $this->setLoginCookie();
            return "";
        }
        $email = $this->getValue(User::$EMAIL);
        $password = $this->getValue(User::$PASSWORD);
        if (empty($email) || empty($password)) {
            return "Please enter your credentials.";
        }
        $result = $this->database->select(User::$USER, "*", User::$EMAIL . "='$email'");
        if ($result != null && password_verify($password, $result[User::$PASSWORD])) {
            $this->generateToken();
            $this->setLoginCookie();
            return "";
        }
        return "Invalid credentials.";
    }

    /**
     * Generate a user token and store it.
     */
    private function generateToken()
    {
        $email = $this->getValue(User::$EMAIL);
        $token = uniqid($email, true);
        $this->setValue(User::$TOKEN, $token);
        if (! $this->database->update(User::$USER, User::$EMAIL . "='$email'", USER::$TOKEN . "='$token'")) {
            throw new BadFunctionCallException("Cannot store token $token");
        }
    }

    /**
     * Set login cookie in the session.
     */
    private function setLoginCookie(): void
    {
        $token = $this->getValue(User::$TOKEN);
        setcookie(User::$USER, "$token", time() + 1800, "/");
        header("Location: /");
    }

    /**
     * Logout the user from the session.
     */
    public function logout()
    {
        $token = $_COOKIE[User::$USER];
        if (isset($token)) {
            $this->database->update(User::$USER, USER::$TOKEN . "='$token'", USER::$TOKEN . "=null");
            setcookie(User::$USER, '', time() - 1000);
        }
    }

    /**
     * Returns the user found with the token in the session cookie.
     *
     * @param Database $database
     *            Needed for the database connection.
     * @return NULL|User Logged in user or null when user is not logged in or was deleted.
     *        
     */
    public static function getUserFromSession(Database $database)
    {
        $user = new User($database);
        // get email from cookie:
        $token = Util::getCookieValue(User::$USER);
        if (empty($token)) {
            return null;
        }
        $result = $database->select(User::$USER, "*", User::$TOKEN . "='$token'");
        if ($result != null) {
            $user->setValue($user->ID, $result[$user->ID]);
            $user->setValue(User::$EMAIL, $result[User::$EMAIL]);
            $user->setValue(User::$GIVENNAME, $result[User::$GIVENNAME]);
            $user->setValue(User::$SURNAME, $result[User::$SURNAME]);
            $user->setValue(User::$CREATED_AT, $result[User::$CREATED_AT]);
            return $user;
        }
        // User not found.
        return null;
    }
}