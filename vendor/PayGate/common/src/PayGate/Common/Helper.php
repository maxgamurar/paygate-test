<?php
/**
 * Helper class
 */

namespace PayGate\Common;

/**
 * Helper class
 *
 */
class Helper
{
    /**
     * Convert a string to camelCase.
     *
     */
    public static function camelCase($str)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Validate a card number (Luhn algorithm)
     *
     * @param  string  $number The card number
     * @return boolean True if the supplied card number is valid
     */
    public static function validateLuhn($number)
    {
        $str = '';
        foreach (array_reverse(str_split($number)) as $i => $c) {
            $str .= $i % 2 ? $c * 2 : $c;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }

    /**
     * Initialize an object with a given array of parameters
     *
     * @param mixed $target     The object to set parameters on
     * @param array $parameters An array of parameters to set
     */
    public static function initialize($target, $parameters)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $method = 'set'.ucfirst(static::camelCase($key));
                if (method_exists($target, $method)) {
                    $target->$method($value);
                }
            }
        }
    }

    /**
     * Resolve a gateway class to a short name.
     *
     */
    public static function getGatewayShortName($className)
    {
        if (0 === strpos($className, '\\')) {
            $className = substr($className, 1);
        }

        if (0 === strpos($className, 'PayGate\\')) {
            return trim(str_replace('\\', '_', substr($className, 8, -7)), '_');
        }

        return '\\'.$className;
    }

    /**
     * Resolve a short gateway name to a full namespaced gateway class.
     *
     * @param  string  $shortName The short gateway name
     * @return string  The fully namespaced gateway class name
     */
    public static function getGatewayClassName($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        $shortName = str_replace('_', '\\', $shortName);
        if (false === strpos($shortName, '\\')) {
            $shortName .= '\\';
        }

        return '\\PayGate\\'.$shortName.'Gateway';
    }
}
