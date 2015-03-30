<?php
# Copyright (c) 2015, CN-Software Ltd.
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without modification,
# are permitted provided that the following conditions are met:
#
#     1. Redistributions of source code must retain the above copyright notice,
#        this list of conditions and the following disclaimer.
#
#     2. Redistributions in binary form must reproduce the above copyright
#        notice, this list of conditions and the following disclaimer in the
#        documentation and/or other materials provided with the distribution.
#
#     3. Neither the name of CN-Software Ltd. nor the names of its contributors
#        may be used to endorse or promote products derived from this software
#        without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
# ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
# ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
# (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
# ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

require_once 'geobaza.php';

/**
 * Random IP address generator
 * @param array $arr
 */
function rand_ip($arr=array()) {
    while (count($arr) < 4) {
        $arr[] = rand(0, 255);
    }
    return join('.', $arr);
}

function ip_list($blocks=array(), $count=10) {
    $ips = array();
    while (count($ips) < $count) {
        $ips[] = rand_ip($blocks);
    }
    return $ips;
}

/**
 * Very basic tests
 * You may extend it for your purposes
 *
 * PHPUnit needed for running these tests
 * @link http://www.phpunit.de/manual/current/en/index.html
 * After install just run in the geobaza root directory that holds geobaza.php:
 *
 * $ phpunit tests.php
 *
 */
class GeobazaTest extends PHPUnit_Framework_TestCase {
    protected $iters = 100000;

    protected function setUp() {
        $this->geobaza = new GeobazaQuery();
    }

    function testQuery() {
        for ($i = $this->iters; $i > 0; $i--) {
            $ip = rand_ip();
            $query = $this->geobaza->get($ip);
            $this->assertTrue((bool)($query instanceof GeobazaObject | $query instanceof SpecialRange));
        }
    }

    function testPathQuery() {
        for ($i = $this->iters; $i > 0; $i--) {
            $ip = rand_ip();
            $query = $this->geobaza->get_path($ip);
            $this->assertTrue($query instanceof Geobaza);
            $this->assertGreaterThanOrEqual(1, $query->count());
            foreach ($query as $item) {
                $this->assertTrue((bool)($item instanceof GeobazaObject | $item instanceof SpecialRange));
            }
        }
    }
}

/**
 * Speed test for internal use
 * Will be skipped
 */
class GeobazaNoCacheSpeedTest extends PHPUnit_Framework_TestCase {
    protected $iters = 100000;

    protected function setUp() {
        $this->geobaza = new GeobazaQuery();
    }

    function testSpeed() {
        $start = time(true);
        for ($i = $this->iters; $i > 0; $i--) {
            $ip = rand_ip();
            $query = $this->geobaza->get($ip);
        }
        $end = time(true) - $start;
        $this->assertLessThanOrEqual(20, $end);
    }
}

class GeobazaMemCacheSpeedTest extends GeobazaNoCacheSpeedTest {
    protected function setUp() {
        $this->geobaza = new GeobazaQuery($path=FILE_PATH, $cache=MEMORY_CACHE);
    }
}
