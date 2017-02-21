<?php
/**    __  ___      ____  _     ___                           _                    __
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

namespace Multidimensional\ArraySanitization\Test;

use Multidimensional\ArraySanitization\Sanitization;
use PHPUnit\Framework\TestCase;

class SanitizationTest extends TestCase
{
    public function testIntegers()
    {
        $rules = [
            'a' => [
                'type' => 'integer'
            ],
            'b' => [
                'type' => 'integer'
            ],
            'c' => [
                'type' => 'integer'
            ],
            'd' => [
                'type' => 'integer'
            ]
        ];
        $array = [
            'a' => 10,
            'b' => '11',
            'c' => 12.1,
            'd' => '13.9'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 10,
            'b' => 11,
            'c' => 12,
            'd' => 13
        ];
        $this->assertEquals($expected, $result);
    }
    
    public function testDecimal()
    {
        $rules = [
            'a' => [
                'type' => 'decimal'
            ],
            'b' => [
                'type' => 'decimal'
            ],
            'c' => [
                'type' => 'decimal'
            ],
            'd' => [
                'type' => 'decimal'
            ]
        ];
        $array = [
            'a' => 10,
            'b' => '11.5',
            'c' => 12.1,
            'd' => '13.9'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 10,
            'b' => 11.5,
            'c' => 12.1,
            'd' => 13.9
        ];
        $this->assertEquals($expected, $result);
    }
    
    public function testString()
    {
        $rules = [
            'a' => [
                'type' => 'string'
            ],
            'b' => [
                'type' => 'string'
            ],
            'c' => [
                'type' => 'string'
            ],
            'd' => [
                'type' => 'string'
            ]
        ];
        $array = [
            'a' => '<a href="www.website.com>Hello</a>',
            'b' => "This isn't really a good test.",
            'c' => "The string filter doesn't really do that <i>much</i>.",
            'd' => 'Okay. Maybe it does a little?'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => "Hello",
            'b' => "This isn&#39;t really a good test.",
            'c' => "The string filter doesn&#39;t really do that much.",
            'd' => "Okay. Maybe it does a little?"
        ];
        $this->assertEquals($expected, $result);
    }
    
    public function testMultidimensionalArray()
    {
        $rules = [
            'a' => [
                'type' => 'integer'
            ],
            'b' => [
                'type' => 'Custom',
                'fields' => [
                    'a' => [
                        'type' => 'decimal'
                    ]
                ]
            ]
        ];    
        $array = [
            'a' => 10.1,
            'b' => [
                'a' => 10.1
            ]
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => '10',
            'b' => [
                'a' => 10.1
            ]
        ];
        $this->assertEquals($expected, $result);
    }
    
    public function testBoolean()
    {
        $rules = [
            'a' => [
                'type' => 'boolean'
            ],
            'b' => [
                'type' => 'boolean'
            ],
            'c' => [
                'type' => 'boolean'
            ],
            'd' => [
                'type' => 'boolean'
            ],
            'e' => [
                'type' => 'boolean'
            ]
        ];        
        $array = [
            'a' => true,
            'b' => 'true',
            'c' => false,
            'd' => 'false',
            'e' => null
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => true,
            'b' => true,
            'c' => false,
            'd' => false,
            'e' => false
        ];
        $this->assertEquals($expected, $result);
    }
    
    public function testPattern()
    {
        $rules = [
            'a' => [
                'pattern' => '\d{5}'
            ],
            'b' => [
                'pattern' => 'ISO 8601'
            ],
            'c' => [
                'type' => 'string',
                'pattern' => 'one|two|three'
            ]   
        ];
        $array = [
            'a' => 'abc12345def',
            'b' => '2005-08-15T15:52:01+00:00ABC',
            'c' => 'sixtythree'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => '12345',
            'b' => '2005-08-15T15:52:01+00:00',
            'c' => 'three'
        ];
        $this->assertEquals($expected, $result);
    }
    
    public function testISO8601()
    {
        $rules = [
            'a' => [
                'pattern' => 'ISO 8601'
            ],
            'b' => [
                'pattern' => 'ISO 8601'
            ],
            'c' => [
                'pattern' => 'ISO 8601'
            ]
        ];
        $array = [
            'a' => '2017-02-18T20:44:48+00:00',
            'b' => '2017-02-18T20:44:48Z',
            'c' => '2017-02-18T20:44:48-06:00',
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => '2017-02-18T20:44:48+00:00',
            'b' => '2017-02-18T20:44:48Z',
            'c' => '2017-02-18T20:44:48-06:00'
        ];
        $this->assertEquals($expected, $result);
    }
}