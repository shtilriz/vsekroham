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

# Import Geobaza
require_once 'geobaza.php';

$ip = '85.142.15.254';

/**
 * Initialize
 */

$query = new GeobazaQuery();

/**
 * Headers: API version, binary file release version, build time
 */

echo $query->headers->release, PHP_EOL;
# '11.10'
echo $query->headers->build_date, PHP_EOL;
# Fri Sep 30 18:15:36 MSK 2011
echo $query->headers->api_version, PHP_EOL;
# 5
echo $query->headers->build_timestamp, PHP_EOL;
# 1317392136

/**
 * Get single object
 */

$obj = $query->get($ip);

/**
 * Object properties
 */

# Name
echo $obj->name, PHP_EOL;
# Velikiy Novgorod

# Type
echo $obj->type, PHP_EOL;
# locality

# Unique ID
echo $obj->id, PHP_EOL;
# 5069

# Nesting level
echo $obj->level, PHP_EOL;
# 3

# Coordinates
echo $obj->geography->center->latitude, PHP_EOL;
# 58.516
echo $obj->geography->center->longitude, PHP_EOL;
# 31.283

# Parent object
echo $obj->parent->name, PHP_EOL;
# Novgorod Region

/**
* Translations
*/

echo $obj->translations[0]->ru, PHP_EOL;
# Великий Новгород
echo $obj->translations[0]->en, PHP_EOL;
# Velikiy Novgorod
echo $obj->translations[0]->type, PHP_EOL;
# official

/**
 * Get full path
 */

$geobaza = $query->get_path($ip);

# Iteration over objects list
foreach ($geobaza as $obj) {
    echo $obj->name, PHP_EOL;
}
# Russian Federation
# North West Federal Region
# Novgorod Region
# Velikiy Novgorod

# You can also access to Geobaza items via indexes, like in array
for ($i = 0; $i < count($geobaza); $i++) {
    echo $geobaza[$i]->name, PHP_EOL;
}
# Result will be the same

# Country
echo $geobaza->country->name, PHP_EOL;
# Russian Federation

# Regions
foreach ($geobaza->regions as $obj) {
    echo $obj->name, PHP_EOL;
}
# North West Federal Region
# Novgorod Region

# Localities
foreach ($geobaza->localities as $obj) {
    echo $obj->name, PHP_EOL;
}
# Velikiy Novgorod

/**
 * Serialization
 */

echo PHP_EOL, 'JSON', PHP_EOL, $geobaza->to_json(), PHP_EOL;
# JSON output
echo PHP_EOL, 'XML', PHP_EOL, $geobaza[0]->to_pretty_xml(), PHP_EOL;
# XML output

/**
 * Special Ranges
 */

# Reserved
$obj = $query->get('127.0.0.1');

echo $obj instanceof SpecialRange, PHP_EOL;
# 1 (true)
echo $obj->type, PHP_EOL;
# special
echo $obj->name, PHP_EOL;
# Loopback

# Unallocated
$obj = $query->get('170.0.0.0');
echo $obj->name, PHP_EOL;
# Unallocated

/**
 * Geobaza object 'is_special' flag
 */

$path = $query->get_path('192.168.0.0');

echo $path->is_special, PHP_EOL;
# 1 (true)
?>