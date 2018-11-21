<?php

class User
{

    public $USER = "User";

    public $EMAIL = "email";

    private $model;

    private $givenname;

    private $surname;

    private $email;

    private $password;

    private $createdAt;

    public function __construct(PlaningModel $model)
    {
        $this->model = $model;
    }

    /**
     * Stores a user to the database.
     *
     * @param bool $override
     *            Configure whether to update an existing user or not.
     * @return string Error message.
     */
    public function store(bool $override): string
    {
        $response = "";
        $result = $this->model->select($this->USER, "*", $this->EMAIL, "'$this->email'");
        // check if user already exists:
        if ($result == null) {
            if (! $this->isStorable()) {
                $response = "User is missing attributes.";
            } else {
                // store new user:
                $response = $this->model->insert($this->USER, "givenName, surname, email, password", "'$this->givenname', '$this->surname', '$this->email', '$this->password'");
            }
        } else if ($override) {
            $id = $result["userID"];
            // update existing user:
            if (! $this->model->update($this->USER, "userID=$id", "givenName='$this->givenname', surname='$this->surname', email='$this->email', password='$this->password'")) {
                $response = "Updating the user was not successful.";
            }
        } else {
            $response = "The selected email is already in use.";
        }
        return $response;
    }

    /**
     * Checks if the user is missing any attributes.
     *
     * @return bool Whether this user is storable to the database.
     */
    private function isStorable(): bool
    {
        if (empty($this->givenname) || empty($this->surname) || empty($this->email) || empty($this->password)) {
            return false;
        }
        return true;
    }

    /**
     * Login the user to the session.
     *
     * @return string Error message.
     */
    public function login(): string
    {
        $result = $this->model->select($this->USER, "*", "$this->EMAIL", "'$this->email'");
        if ($result != null && $result["password"] == $this->password) {
            // Cookie lifespan: 30 minutes
            setcookie($this->USER, "$this->email", time() + 1800, "/");
            header("Location: /");
            return "";
        }
        return "Invalid credentials.";
    }

    /**
     * Logout the user in the session.
     */
    public function logout()
    {
        $cookie = $_COOKIE[$this->USER];
        if (isset($cookie)) {
            setcookie($this->USER, '', time() - 1000);
        }
    }

    public function getUserFromSession()
    {
        $this->email = $this->getCookieValue($this->USER); // get from cookie
        if (empty($this->email)) {
            return null;
        }
        $result = $this->model->select($this->USER, "*", $this->EMAIL, "'$this->email'");
        if ($result != null) {
            $this->givenname = $result["givenName"];
            $this->surname = $result["surname"];
            $this->createdAt = $result["createdAt"];
            return $this;
        }
        // User not found.
        return null;
    }

    private function getCookieValue(string $name): string
    {
        $cookie = $_COOKIE[$name];
        if (isset($cookie)) {
            return $cookie;
        }
        return "";
    }

    public function setGivenname(string $value)
    {
        $this->givenname = $value;
    }

    public function getGivenname(): string
    {
        return $this->givenname;
    }

    public function setSurname(string $value)
    {
        $this->surname = $value;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setEmail(string $value)
    {
        $this->email = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setPassword(string $value)
    {
        $this->password = $value;
    }
}