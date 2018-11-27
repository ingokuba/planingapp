<?php

class Util
{

    /**
     * Valid cards for the game.
     *
     * @var array
     */
    public static $CARDS = array(
        0,
        0.5,
        1,
        2,
        3,
        5,
        8,
        13,
        20,
        40,
        100,
        '?',
        'Coffee'
    );

    public function __construct()
    {
        throw new BadFunctionCallException("This utility class cannot be instanciated.");
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
            return Util::trim($cookie);
        }
        return "";
    }

    /**
     * Get data from the POST request.
     *
     * @return mixed Trimmed value from the post request or null if not found.
     */
    public static function getPostData($id)
    {
        $data = $_POST[$id];
        if (isset($data)) {
            return Util::trim($data);
        }
        return null;
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