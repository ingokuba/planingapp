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
            User::$CREATED_AT
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
        $createdAt = $this->getValue(User::$CREATED_AT);
        if (! empty($createdAt)) {
            $message .= "Attribute 'createdAt' should not be set for storing.!";
        }
        return $message;
    }

    /**
     * Login the user to the session.
     *
     * @return string Error message.
     */
    public function login(): string
    {
        $email = $this->getValue(User::$EMAIL);
        $password = $this->getValue(User::$PASSWORD);
        if (empty($email) || empty($password)) {
            return "Please enter your credentials.";
        }
        $result = $this->database->select(User::$USER, "*", User::$EMAIL . "='$email'");
        if ($result != null && $result[User::$PASSWORD] == $password) {
            // Cookie lifespan: 30 minutes
            setcookie(User::$USER, "$email", time() + 1800, "/");
            header("Location: /");
            return "";
        }
        return "Invalid credentials.";
    }

    /**
     * Logout the user from the session.
     */
    public static function logout()
    {
        $cookie = $_COOKIE[User::$USER];
        if (isset($cookie)) {
            setcookie(User::$USER, '', time() - 1000);
        }
    }

    /**
     * Returns the user found with the email in the session cookie.
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
        $email = Util::getCookieValue(User::$USER);
        if (empty($email)) {
            return null;
        }
        $result = $database->select(User::$USER, "*", User::$EMAIL . "='$email'");
        if ($result != null) {
            $user->setValue($user->ID, $result[$user->ID]);
            $user->setValue(User::$EMAIL, $email);
            $user->setValue(User::$GIVENNAME, $result[User::$GIVENNAME]);
            $user->setValue(User::$SURNAME, $result[User::$SURNAME]);
            $user->setValue(User::$CREATED_AT, $result[User::$CREATED_AT]);
            return $user;
        }
        // User not found.
        return null;
    }
}