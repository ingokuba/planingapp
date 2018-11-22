<?php

/**
 * Default index page of the application. 
 * Parent class for all different pages.
 * 
 * @author Ingo Kuba
 */
class Page implements IPage
{

    protected $model;

    protected $controller;

    protected $user;

    public $body;

    public function __construct(PlaningController $controller, PlaningModel $model)
    {
        $this->controller = $controller;
        $this->model = $model;
    }

    private function getBody(): string
    {
        // initialize user:
        $this->user = User::getUserFromSession($this->model);
        $loginBlock = "<div class='row'><button class='btn btn-lg mb-1' data-toggle='tooltip' title='Login' onclick='window.location=\"Login\";'><i class='fas fa-user-lock'></i></button>
                        <i class='fas fa-arrow-left ml-2 pt-3'> Login here</i></div>";
        if ($this->user != null) {
            $givenname = $this->user->getValue(User::$GIVENNAME);
            $surname = $this->user->getValue(User::$SURNAME);
            $createdAt = date("d.m.Y", strtotime($this->user->getValue(User::$CREATED_AT)));
            $loginBlock = "<div class='row'><button class='btn btn-lg mb-1' data-toggle='tooltip' title='Logout' onclick='document.cookie=\"User=; expires=new Date(); path=/;\";location.reload();'><i class='fas fa-sign-out-alt'></i></button>
                        <div class='ml-2 pt-3'>Welcome <b>$givenname $surname</b> (Member since $createdAt)</div></div>";
        }
        // no body defined -> default
        if ($this->body == null) {
            // image source: https://images.mentalfloss.com/sites/default/files/styles/mf_image_16x9/public/345eyrhfj.png?itok=35gvnyvU&resize=1100x1100
            $this->body = "$loginBlock
                            <img src='resources/pokerdogs.png' class='img-fluid row mt-2 mb-3' alt='image not found'>
                            <h1 class='text-center'>Cost estimation game for software projects.</h1>";
        }
        return $this->body;
    }

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