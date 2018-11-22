<?php

class PlaningController
{

    private $model;

    public function __construct(PlaningModel $model)
    {
        $this->model = $model;
    }

    /**
     * Get value of the cookie with the given name.
     *
     * @param string $name
     *            Name of the cookie.
     * @return string Value of the cookie or empty.
     */
    public static function getCookieValue(string $name): string
    {
        $cookie = $_COOKIE[$name];
        if (isset($cookie)) {
            return PlaningController::trim($cookie);
        }
        return "";
    }

    /**
     * Get data from the POST request.
     *
     * @return string Trimmed value from the post request or empty string if not found.
     */
    public static function getPostData($id): string
    {
        $data = $_POST[$id];
        if (isset($data)) {
            return PlaningController::trim($data);
        }
        return "";
    }

    /**
     * Formats a given string.
     * <ul>
     * <li>1. trims whitespace
     * <li>2. unquotes
     * <li>3. removes html special characters
     * </ul>
     *
     * @param string $string
     *            Untrimmed string.
     * @return string Trimmed string.
     */
    public static function trim(string $string): string
    {
        $string = trim($string);
        $string = stripslashes($string);
        return htmlspecialchars($string);
    }
}