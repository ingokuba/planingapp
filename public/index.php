<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

function autoload($className)
{
    require "classes/$className.php";
}
spl_autoload_register("autoload");

$model = new PlaningModel();

$controller = new PlaningController($model);

require "views/IPage.php";
require "views/Page.php";

// get page from request parameter and check if it exists:
$url =  htmlspecialchars($_SERVER["REQUEST_URI"]);
$page = trim($url);
$page = str_replace("/", "", $page);
if ($page == "" || ! file_exists("views/$page.php")) {
    $page = "Page";
} else {
    // load view:
    require "views/$page.php";
}
// display view:
$view = new $page($controller, $model);
echo $view->output();
