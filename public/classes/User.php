<?php

class User
{

    private $model;

    private $givenname;

    private $surname;

    private $email;

    private $password;

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
        $result = $this->model->select("User", "*", "email", "'$this->email'");
        // check if user already exists:
        if ($result == null) {
            if (! $this->isStorable()) {
                $response = "User is missing attributes.";
            } else {
                // store new user:
                $response = $this->model->insert("User", "givenName, surname, email, password", "'$this->givenname', '$this->surname', '$this->email', '$this->password'");
            }
        } else if ($override) {
            $id = $result["userID"];
            // update existing user:
            if (! $this->model->update("User", "userID=$id", "givenName='$this->givenname', surname='$this->surname', email='$this->email', password='$this->password'")) {
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

    public function setGivenname(string $value)
    {
        $this->givenname = $value;
    }

    public function setSurname(string $value)
    {
        $this->surname = $value;
    }

    public function setEmail(string $value)
    {
        $this->email = $value;
    }

    public function setPassword(string $value)
    {
        $this->password = $value;
    }
}