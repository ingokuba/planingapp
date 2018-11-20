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

    public $body;

    public function __construct(PlaningController $controller, PlaningModel $model)
    {
        $this->controller = $controller;
        $this->model = $model;
    }

    private function getBody(): string
    {
        if ($this->body == null) {
            // image source: https://images.mentalfloss.com/sites/default/files/styles/mf_image_16x9/public/345eyrhfj.png?itok=35gvnyvU&resize=1100x1100
            $this->body = "<img src='templates/pokerdogs.png' class='img-fluid' alt='image not found'>
                            <h1 class='mt-4'>Cost estimation game for software projects.</h1>";
        }
        return $this->body;
    }

    public function output(): string
    {
        $body = $this->getBody();

        ob_start();
        require 'templates/default.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}