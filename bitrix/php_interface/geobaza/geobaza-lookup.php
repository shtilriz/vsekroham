#!/usr/bin/php
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

if (count($argc) >= 1) {
    $ip = $argv[1];
    $geobaza = lookup($ip);
    $ind = ' ';
    echo PHP_EOL;
    echo $ind.'Information for IP-address: '.$ip;
    echo PHP_EOL, PHP_EOL;
    if (!empty($geobaza)) {
        if ($geobaza->count() == 1) {
            echo $ind, $geobaza->first()->name, PHP_EOL;
        }
        else {
            $i = 0;
            foreach ($geobaza as $item) {
                $str = str_repeat('-', 2 * $i);
                if ($i > 0) echo $ind.'+--'.$str.$item->name, PHP_EOL;
                else echo $ind.'+--'.$item->name, PHP_EOL;

                if ($i < $geobaza->count() - 1) echo $ind.'|', PHP_EOL;
                $i++;
            }
        }
    }
    else {
        echo $ind.'No information for given IP-address';
    }
}
else {
    echo PHP_EOL, 'Usage: geobaza-lookup.php ip_address', PHP_EOL;
}
echo PHP_EOL;
?>