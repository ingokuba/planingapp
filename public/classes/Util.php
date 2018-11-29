<?php

/**
 * 
 * Utility class for various static helper methods.
 *
 */
class Util
{

    /**
     * Overrides default constructor to prevent instanciation.
     *
     * @throws BadFunctionCallException When constructor is called.
     */
    public function __construct()
    {
        throw new BadFunctionCallException("This utility class cannot be instanciated.");
    }

    /**
     * Get array of valid cards from the configuration.
     *
     * @return array Valid cards (numeric/string).
     */
    public static function getCards(): array
    {
        return Configuration::getNode("cards");
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

    /**
     * Checks whether two variables have the same value.
     *
     * @param number|string $var1
     * @param number|string $var2
     * @return bool If both variables have the same value.
     */
    public static function compare($var1, $var2): bool
    {
        if (is_numeric($var1) && is_numeric($var2)) {
            return $var1 == $var2;
        }
        if (is_string($var1) && is_string($var2)) {
            return $var1 === $var2;
        }
        return false;
    }

    /**
     * Encrypts a password with the blowfish encryption algorithm.
     *
     * @param string $password
     *            The plaintext password.
     * @return string Hashed password.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}