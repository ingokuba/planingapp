<?php

/**
 * Default index page of the application. 
 * Parent class for all other views.
 * 
 */
class Page
{

    /**
     * Database connection.
     *
     * @var Database
     */
    protected $database;

    /**
     * Logged in user.
     *
     * @var User
     */
    public $user;

    /**
     * Sets the database connection and retrieves the user from the session cookie.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
        // initialize user:
        $this->user = User::getUserFromSession($this->database);
    }

    /**
     * Initializes the content of the page.
     *
     * @return string Body for the page.
     */
    protected function getBody(): string
    {
        if ($this->user != null) {
            header("Location: Welcome");
        }
        // image source: https://images.mentalfloss.com/sites/default/files/styles/mf_image_16x9/public/345eyrhfj.png?itok=35gvnyvU&resize=1100x1100
        return "<div class='row mt-3'><button class='btn btn-lg btn-success mb-1' data-toggle='tooltip' title='Login' onclick='window.location=\"Login\";'><i class='fas fa-user-lock'></i></button>
                    <i class='fas fa-arrow-left ml-2 pt-3'> Login here</i>
                </div>
                <img src='resources/pokerdogs.png' class='img-fluid row mt-2 mb-3' alt='image not found'>
                <h1 class='text-center'>Cost estimation game for software projects.</h1>";
    }

    /**
     * Builds the page with the body content.
     *
     * @return string The html for the page.
     */
    public function output(): string
    {
        $body = $this->getBody();

        ob_start();
        require 'resources/templates/default.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}