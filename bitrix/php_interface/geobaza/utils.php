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

/**
 * XML & JSON Serializer
 */
class Serializer {
    public function to_xml() {
        $xml = $this->as_xml();
        return $xml->saveXML();
    }

    public function to_json() {
        return json_encode($this->as_array());
    }

    public function to_pretty_xml() {
        $xml = $this->as_xml();
        $xml->formatOutput = true;
        return $xml->saveXML();
    }

    /**
     * PHP >= 5.4.0
     * @link http://php.net/manual/en/function.json-encode.php#refsect1-function.json-encode-changelog
     * @return string
     */
    public function to_pretty_json() {
        return json_encode($this->as_array(), JSON_PRETTY_PRINT);
    }
}

/**
 * Multibyte
 */

if (function_exists('iconv')) {
    function convert_encoding($str, $to, $from='utf-8') {
        return iconv($from, $to, $str);
    }
} else if (function_exists('mb_convert_encoding')) {
    function convert_encoding($str, $to, $from='utf-8') {
        return mb_convert_encoding($str, $to, $from);
    }
} else {
    function convert_encoding($str, $to, $from='utf-8') {
        throw Exception('You need "Multibyte String" or "iconv" for this.');
    }
}
?>