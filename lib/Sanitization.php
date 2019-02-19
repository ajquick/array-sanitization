<?php
/**
 *       __  ___      ____  _     ___                           _                    __
 *      /  |/  /_  __/ / /_(_)___/ (_)___ ___  ___  ____  _____(_)___  ____   ____ _/ /
 *     / /|_/ / / / / / __/ / __  / / __ `__ \/ _ \/ __ \/ ___/ / __ \/ __ \ / __ `/ /
 *    / /  / / /_/ / / /_/ / /_/ / / / / / / /  __/ / / (__  ) / /_/ / / / // /_/ / /
 *   /_/  /_/\__,_/_/\__/_/\__,_/_/_/ /_/ /_/\___/_/ /_/____/_/\____/_/ /_(_)__,_/_/
 *
 *  Array Sanitization Library
 *  Copyright (c) Multidimension.al (http://multidimension.al)
 *  Github : https://github.com/multidimension-al/array-sanitization
 *
 *  Licensed under The MIT License
 *  For full copyright and license information, please see the LICENSE file
 *  Redistributions of files must retain the above copyright notice.
 *
 *  @copyright  Copyright © 2017-2019 Multidimension.al (http://multidimension.al)
 *  @link       https://github.com/multidimension-al/array-sanitization Github
 *  @license    http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Multidimensional\ArraySanitization;

class Sanitization
{

    /**
     * @param array $array
     * @param array $rules
     * @return array
     */
    public static function sanitize($array, $rules)
    {
        $newArray = [];
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value) && isset($rules[$key]['type']) && strtolower($rules[$key]['type']) == 'array' && isset($rules[$key]['fields'])) {
                    $newArray[$key] = self::sanitize($value, $rules[$key]['fields']);
                } elseif (in_array($key, array_keys($rules))) {
                    $newArray[$key] = self::sanitizeField($value, $rules[$key]);
                }
            }
        }

        return $newArray;
    }

    /**
     * @param string $value
     * @param array $rules
     * @return string $value
     * @internal param string $key
     */
    public static function sanitizeField($value, $rules)
    {
        if (is_array($value) && isset($rules['type']) && strtolower($rules['type']) == 'group' && isset($rules['fields'])) {
            foreach ($value as $k => $v) {
                if (is_array($v) && !isset($rules['fields'][$k])) {
                    $value[$k] = self::sanitize($v, $rules['fields']);
                } elseif (isset($rules['fields'][$k])) {
                    $value[$k] = self::sanitizeField($v, $rules['fields'][$k]);
                }
            }
        } else {
            if (isset($rules['pattern'])) {
                if ($rules['pattern'] == 'ISO 8601') {
                    $value = self::sanitizeISO8601($value);
                } else {
                    $value = self::sanitizePattern($value, $rules['pattern']);
                }
            }

            if (isset($rules['values'])) {
                $value = self::sanitizeValues($value, $rules['values']);
            }

            if (isset($rules['type'])) {
                if ($rules['type'] == 'integer') {
                    $value = self::sanitizeInteger($value);
                } elseif ($rules['type'] == 'string') {
                    $value = self::sanitizeString($value);
                } elseif ($rules['type'] == 'decimal') {
                    $value = self::sanitizeDecimal($value);
                } elseif ($rules['type'] == 'boolean') {
                    $value = self::sanitizeBoolean($value);
                }
            }
        }

        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    protected static function sanitizeISO8601($value)
    {
        $pattern = '(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:Z|[+-][01]\d:[0-5]\d)';
        $value = trim($value);

        if (preg_match('/^' . $pattern . '$/', $value)
            || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $value)
            || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value)
        ) {
            return $value;
        }

        return preg_replace('/[^0-9TZ\:\-\+]/', '', $value);
    }

    /**
     * @param string $value
     * @param string $pattern
     * @return string
     */
    protected static function sanitizePattern($value, $pattern)
    {
        if (preg_match('/^' . $pattern . '$/', $value)) {
            return $value;
        }

        return preg_replace('/[^' . $pattern . ']/', '', $value);
    }

    /**
     * @param string $value
     * @param array|string $array
     * @return string
     */
    protected static function sanitizeValues($value, $array)
    {
        if (is_array($array)) {
            foreach ($array as $key) {
                if (strcasecmp($value, $key) === 0) {
                    return $key;
                }
            }
        } elseif (strcasecmp($value, $array) === 0) {
            return $array;
        }

        return $value;
    }

    /**
     * @param string|int $value
     * @return int
     */
    protected static function sanitizeInteger($value)
    {
        return (int)filter_var(intval($value), FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @param string $value
     * @return string
     */
    protected static function sanitizeString($value)
    {
        return (string)filter_var(strval($value), FILTER_SANITIZE_STRING);
    }

    /**
     * @param string|float $value
     * @return float
     */
    protected static function sanitizeDecimal($value)
    {
        return (float)filter_var(floatval($value), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * @param string|bool $value
     * @return true|false|null
     */
    protected static function sanitizeBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
