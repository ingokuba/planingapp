<?php

class PlaningController
{

    private $model;

    public function __construct(PlaningModel $model)
    {
        $this->model = $model;
    }
}