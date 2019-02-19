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
            'a' => '<a href="http://www.google.com">Hello</a>',
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

    public function testMultidimensionalGroupArray()
    {
        $rules = [
            'Population' => [
                'type' => 'array',
                'fields' => [
                    'People' => [
                        'type' => 'group',
                        'fields' => [
                            'Name' => [
                                'type' => 'string'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $array = [
            'Population' => [
                'People' => [
                    0 => [
                        'Name' => 'John Smith'
                    ],
                    1 => [
                        'Name' => 'Jane Smith'
                    ]
                ]
            ]
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'Population' => [
                'People' => [
                    0 => [
                        'Name' => 'John Smith'
                    ],
                    1 => [
                        'Name' => 'Jane Smith'
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMultiMultidimensionalGroupArray()
    {
        $rules = [
            'RateV4Response' => [
                'type' => 'array',
                'fields' => [
                    'Package' => [
                        'type' => 'group',
                        'fields' => [
                            '@ID' => [
                                'type' => 'string',
                                'required' => true
                            ],
                            'ZipOrigination' => [
                                'type' => 'string',
                                'required' => true,
                                'pattern' => '\d{5}'
                            ],
                            'ZipDestination' => [
                                'type' => 'string',
                                'required' => true,
                                'pattern' => '\d{5}'
                            ],
                            'Pounds' => [
                                'type' => 'decimal',
                                'required' => true,
                            ],
                            'Ounces' => [
                                'type' => 'decimal',
                                'required' => true,
                            ],
                            'FirstClassMailType' => [
                                'type' => 'string'
                            ],
                            'Container' => [
                                'type' => 'string',
                            ],
                            'Size' => [
                                'type' => 'string',
                                'required' => true,
                                'values' => [
                                    'REGULAR',
                                    'LARGE'
                                ]
                            ],
                            'Width' => [
                                'type' => 'decimal'
                            ],
                            'Length' => [
                                'type' => 'decimal'
                            ],
                            'Height' => [
                                'type' => 'decimal'
                            ],
                            'Girth' => [
                                'type' => 'decimal'
                            ],
                            'Machinable' => [
                                'type' => 'boolean'
                            ],
                            'Zone' => [
                                'type' => 'string'
                            ],
                            'Postage' => [
                                'type' => 'group',
                                'required' => true,
                                'fields' => [
                                    '@CLASSID' => [
                                        'type' => 'integer'
                                    ],
                                    'MailService' => [
                                        'type' => 'string'
                                    ],
                                    'Rate' => [
                                        'type' => 'decimal'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $array = ['RateV4Response' => ['Package' => ['@ID' => '123', 'ZipOrigination' => 20500, 'ZipDestination' => 90210, 'Pounds' => '0', 'Ounces' => '32', 'Size' => 'REGULAR', 'Machinable' => 'TRUE', 'Zone' => '8', 'Postage' => [0 => ['@CLASSID' => '1', 'MailService' => 'Priority Mail 2-Day<sup>™</sup>', 'Rate' => '12.75'], 1 => ['@CLASSID' => '22', 'MailService' => 'Priority Mail 2-Day<sup>™</sup> Large Flat Rate Box', 'Rate' => '18.85'], 2 => ['@CLASSID' => '17', 'MailService' => 'Priority Mail 2-Day<sup>™</sup> Medium Flat Rate Box', 'Rate' => '13.60'], 3 => ['@CLASSID' => '28', 'MailService' => 'Priority Mail 2-Day<sup>™</sup> Small Flat Rate Box', 'Rate' => '7.15']]]]];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'RateV4Response' => [
                'Package' => [
                    '@ID' => '123',
                    'ZipOrigination' => '20500',
                    'ZipDestination' => '90210',
                    'Pounds' => 0.0,
                    'Ounces' => 32.0,
                    'Size' => 'REGULAR',
                    'Machinable' => true,
                    'Zone' => '8',
                    'Postage' => [
                        0 => [
                            '@CLASSID' => 1,
                            'MailService' => 'Priority Mail 2-Day™',
                            'Rate' => 12.75
                        ],
                        1 => [
                            '@CLASSID' => 22,
                            'MailService' => 'Priority Mail 2-Day™ Large Flat Rate Box',
                            'Rate' => 18.85
                        ],
                        2 => [
                            '@CLASSID' => 17,
                            'MailService' => 'Priority Mail 2-Day™ Medium Flat Rate Box',
                            'Rate' => 13.60
                        ],
                        3 => [
                            '@CLASSID' => 28,
                            'MailService' => 'Priority Mail 2-Day™ Small Flat Rate Box',
                            'Rate' => 7.15
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMultiMultiMultidimensionalGroupArray()
    {
        $rules = [
            'RateV4Response' => [
                'type' => 'array',
                'fields' => [
                    'Package' => [
                        'type' => 'group',
                        'fields' => [
                            '@ID' => [
                                'type' => 'string',
                                'required' => true
                            ],
                            'ZipOrigination' => [
                                'type' => 'string',
                                'required' => true,
                                'pattern' => '\d{5}'
                            ],
                            'ZipDestination' => [
                                'type' => 'string',
                                'required' => true,
                                'pattern' => '\d{5}'
                            ],
                            'Pounds' => [
                                'type' => 'decimal',
                                'required' => true,
                            ],
                            'Ounces' => [
                                'type' => 'decimal',
                                'required' => true,
                            ],
                            'FirstClassMailType' => [
                                'type' => 'string'
                            ],
                            'Container' => [
                                'type' => 'string',
                            ],
                            'Size' => [
                                'type' => 'string',
                                'required' => true,
                                'values' => [
                                    'REGULAR',
                                    'LARGE'
                                ]
                            ],
                            'Width' => [
                                'type' => 'decimal'
                            ],
                            'Length' => [
                                'type' => 'decimal'
                            ],
                            'Height' => [
                                'type' => 'decimal'
                            ],
                            'Girth' => [
                                'type' => 'decimal'
                            ],
                            'Machinable' => [
                                'type' => 'boolean'
                            ],
                            'Zone' => [
                                'type' => 'string'
                            ],
                            'Postage' => [
                                'type' => 'group',
                                'required' => true,
                                'fields' => [
                                    '@CLASSID' => [
                                        'type' => 'integer'
                                    ],
                                    'MailService' => [
                                        'type' => 'string'
                                    ],
                                    'Rate' => [
                                        'type' => 'decimal'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $array = [
            'RateV4Response' => [
                'Package' => [
                    0 => [
                        '@ID' => '123',
                        'ZipOrigination' => 20500,
                        'ZipDestination' => 90210,
                        'Pounds' => '0',
                        'Ounces' => '32',
                        'Size' => 'REGULAR',
                        'Machinable' => 'TRUE',
                        'Zone' => '8',
                        'Postage' => [
                            0 => [
                                '@CLASSID' => '1',
                                'MailService' => 'Priority Mail 2-Day<sup>™</sup>',
                                'Rate' => '12.75'
                            ],
                            1 => [
                                '@CLASSID' => '22',
                                'MailService' => 'Priority Mail 2-Day<sup>™</sup> Large Flat Rate Box',
                                'Rate' => '18.85'
                            ],
                            2 => [
                                '@CLASSID' => '17',
                                'MailService' => 'Priority Mail 2-Day<sup>™</sup> Medium Flat Rate Box',
                                'Rate' => '13.60'
                            ],
                            3 => [
                                '@CLASSID' => '28',
                                'MailService' => 'Priority Mail 2-Day<sup>™</sup> Small Flat Rate Box',
                                'Rate' => '7.15'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'RateV4Response' => [
                'Package' => [
                    0 => [
                        '@ID' => '123',
                        'ZipOrigination' => '20500',
                        'ZipDestination' => '90210',
                        'Pounds' => 0.0,
                        'Ounces' => 32.0,
                        'Size' => 'REGULAR',
                        'Machinable' => true,
                        'Zone' => '8',
                        'Postage' => [
                            0 => [
                                '@CLASSID' => 1,
                                'MailService' => 'Priority Mail 2-Day™',
                                'Rate' => 12.75
                            ],
                            1 => [
                                '@CLASSID' => 22,
                                'MailService' => 'Priority Mail 2-Day™ Large Flat Rate Box',
                                'Rate' => 18.85
                            ],
                            2 => [
                                '@CLASSID' => 17,
                                'MailService' => 'Priority Mail 2-Day™ Medium Flat Rate Box',
                                'Rate' => 13.6
                            ],
                            3 => [
                                '@CLASSID' => 28,
                                'MailService' => 'Priority Mail 2-Day™ Small Flat Rate Box',
                                'Rate' => 7.15
                            ]
                        ]
                    ]
                ]
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
                'pattern' => '\d{5}'
            ],
        ];
        $array = [
            'a' => 'abc12345def',
            'b' => '2005-08-15T15:52:01+00:00ABC',
            'c' => '12345'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => '12345',
            'b' => '2005-08-15T15:52:01+00:00',
            'c' => '12345'
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
            'a' => '2017-02-18T20:44:48+00:00ABCDEF',
            'b' => '2017##$$$-02-18T20:44:48Z',
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
    
    public function testValues()
    {
        $rules = [
            'a' => [
                'type' => 'integer',
                'values' => [
                    100,
                    101,
                    102
                ]
            ],
            'b' => [
                'type' => 'string',
                'values' => [
                    'hello',
                    'goodbye'
                ]
            ],
            'c' => [
                'type' => 'string',
                'values' => 'goodbye'
            ]
        ];
        $array = [
            'a' => '101',
            'b' => 'hello',
            'c' => 'goodbye'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 101,
            'b' => 'hello',
            'c' => 'goodbye'
        ];
        $this->assertEquals($expected, $result);
    }

    public function testValuesFailure()
    {
        $rules = [
            'a' => [
                'type' => 'integer',
                'values' => [
                    100,
                    101,
                    102
                ]
            ]
        ];
        $array = [
            'a' => '103',
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 103,
        ];
        $this->assertEquals($expected, $result);
    }
}
