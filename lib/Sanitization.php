<?php
/**
 *     __  ___      ____  _     ___                           _                    __
 *    /  |/  /_  __/ / /_(_)___/ (_)___ ___  ___  ____  _____(_)___  ____   ____ _/ /
 *   / /|_/ / / / / / __/ / __  / / __ `__ \/ _ \/ __ \/ ___/ / __ \/ __ \ / __ `/ /
 *  / /  / / /_/ / / /_/ / /_/ / / / / / / /  __/ / / (__  ) / /_/ / / / // /_/ / /
 * /_/  /_/\__,_/_/\__/_/\__,_/_/_/ /_/ /_/\___/_/ /_/____/_/\____/_/ /_(_)__,_/_/
 *
 * CONFIDENTIAL
 *
 * © 2017 Multidimension.al - All Rights Reserved
 *
 * NOTICE:  All information contained herein is, and remains the property of
 * Multidimension.al and its suppliers, if any.  The intellectual and
 * technical concepts contained herein are proprietary to Multidimension.al
 * and its suppliers and may be covered by U.S. and Foreign Patents, patents in
 * process, and are protected by trade secret or copyright law. Dissemination
 * of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained.
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
                if (in_array($key, array_keys($rules))) {
                    $newArray[$key] = self::sanitizeField($key, $value, $rules[$key]);
                }
            }
        }

        return $newArray;
    }

    /**
     * @param string $key
     * @param string $value
     * @param array  $rules
     */
    public static function sanitizeField($key, $value, $rules)
    {
        if (is_array($value) && isset($rules['fields'])) {
            return self::sanitize($value, $rules['fields']);
        }

        if (isset($rules['pattern'])) {
            if ($rules['pattern'] == 'ISO 8601') {
                $value = self::sanitizeISO8601($value);
            } else {
                $value = self::sanitizePattern($value, $rules['pattern']);
            }
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

        return $value;
    }

    /**
     * @param string|int $value
     * @return int
     */
    protected static function sanitizeInteger($value)
    {
        return filter_var(intval($value), FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @param string $value
     * @return string
     */
    protected static function sanitizeString($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * @param string|float $value
     * @return float
     */
    protected static function sanitizeDecimal($value)
    {
        return filter_var(floatval($value), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * @param string|bool $value
     * @return true|false|null
     */
    protected static function sanitizeBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    
    /**
     * @param string $value
     * @return string
     */
    protected static function sanitizeISO8601($value)
    {
        $pattern = '(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:Z|[+-][01]\d:[0-5]\d)';
        $value = trim($value);
         
        if (preg_match('/^' . $pattern . '$/', $value) ||
            preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $value) || 
            preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/$', $value)) {
            return $value;        
        }

        return preg_replace('/[^0-9TZ\:\-\+]/', '', $value);;
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
}
