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

class PatternTest extends TestCase
{
    public function testPatternURL()
    {
        $rules = [
            'a' => ['type' => 'string', 'pattern' => 'URL'],
            'b' => ['type' => 'string', 'pattern' => 'URL'],
            'c' => ['type' => 'string', 'pattern' => 'URL']
        ];
        $array = [
            'a' => 'www.domain.com',
            'b' => 'http://www.google.com/index.html',
            'c' => '™dožma Òin.com©',
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 'www.domain.com',
            'b' => 'http://www.google.com/index.html',
            'c' => 'domain.com'
        ];
        $this->assertEquals($expected, $result);
    }

    public function testPatternEmail()
    {
        $rules = [
            'a' => ['type' => 'string', 'pattern' => 'EMAIL'],
            'b' => ['type' => 'string', 'pattern' => 'EMAIL'],
            'c' => ['type' => 'string', 'pattern' => 'EMAIL']
        ];
        $array = [
            'a' => 'noreply@domain.com',
            'b' => 'no.reply+mailbox@domain.com',
            'c' => '(noreply) at :domain.©com',
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 'noreply@domain.com',
            'b' => 'no.reply+mailbox@domain.com',
            'c' => 'noreplyatdomain.com'
        ];
        $this->assertEquals($expected, $result);
    }

    public function testPatternMAC()
    {
        $rules = [
            'a' => ['type' => 'string', 'pattern' => 'MAC'],
            'b' => ['type' => 'string', 'pattern' => 'MAC'],
            'c' => ['type' => 'string', 'pattern' => 'MAC'],
            'd' => ['type' => 'string', 'pattern' => 'MAC']
        ];
        $array = [
            'a' => 'AB:CD:EF:12:34:56',
            'b' => 'AB-CD-EF-12-34-56',
            'c' => 'ABCD.EF12.3456',
            'd' => 'STDD:RE5P:DD:00:BV4:$#!@%^&*12'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => 'AB:CD:EF:12:34:56',
            'b' => 'AB-CD-EF-12-34-56',
            'c' => 'ABCD.EF12.3456',
            'd' => 'DD:E5:DD:00:B4:12'
        ];
        $this->assertEquals($expected, $result);
    }

    public function testPatternIP()
    {
        $rules = [
            'a' => ['type' => 'string', 'pattern' => 'IP'],
            'b' => ['type' => 'string', 'pattern' => 'IP'],
            'c' => ['type' => 'string', 'pattern' => 'IP'],
            'd' => ['type' => 'string', 'pattern' => 'IP']
        ];
        $array = [
            'a' => '127.0.0.1',
            'b' => '255.255.255.255',
            'c' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'd' => 'H1GGR12.$@#31^^%.FFD.123::$334D'
        ];
        $result = Sanitization::sanitize($array, $rules);
        $expected = [
            'a' => '127.0.0.1',
            'b' => '255.255.255.255',
            'c' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'd' => '112.31.FFD.123::334D' //Okay this isn't valid, but its sanitized correctly.
        ];
        $this->assertEquals($expected, $result);
    }
}