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

require_once 'utils.php';

define('VERSION', '1.0.4.15.01');

define('FILE_PATH', dirname(__FILE__).'/data/geobaza.dat');
define('NO_CACHE', 0);
define('MEMORY_CACHE', 1);

/**
 * Exceptions
 */

/**
 * Base module exception
 */
class GeobazaException extends Exception {}

class GeobazaIPException extends GeobazaException {}

/**
 * Binary files
 */

class FileSingleton {
    protected static $instances = array();

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }

    public function unpack($bytes, $fmt) {
        $data = $this->read($bytes);
        $unp = unpack($fmt, $data);
        return $unp;
    }
}

class BinaryFile extends FileSingleton {
    public static function init($path) {
        if (!array_key_exists($path, self::$instances)) {
            self::$instances[$path] = new self($path);
        }
        return self::$instances[$path];
    }

    private function __construct($path) {
        $this->file = fopen($path, 'r');
    }

    public function read($bytes) {
        return fread($this->file, $bytes);
    }

    public function seek($offset, $whence=SEEK_SET) {
        return fseek($this->file, $offset, $whence);
    }

    public function tell() {
        return ftell($this->file);
    }
}

class BinaryBlob extends FileSingleton {
    private $position;
    private $blob;

    public static function init($path) {
        if (!array_key_exists($path, self::$instances)) {
            self::$instances[$path] = new self($path);
        }
        return self::$instances[$path];
    }

    private function __construct($path) {
        $this->blob = file_get_contents($path);
        $this->position = 0;
    }

    public function read($bytes) {
        $chunk = substr($this->blob, $this->position, $bytes);
        $this->position += $bytes;
        return $chunk;
    }

    public function seek($offset, $whence=0) {
        switch ($whence) {
            case 1:
                $this->position += $offset;
            case 2:
                $this->position = count($this->blob) + $offset;
            default:
                $this->position = $offset;
        }
    }

    public function tell() {
        return $this->position;
    }
}

/**
 * Languages
 */

class Language {
    public $id, $name;

    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

class Languages {
    static $languages = array(
        'aa' => 'Afar',
        'ab' => 'Abkhazian',
        'ae' => 'Avestan',
        'af' => 'Afrikaans',
        'ak' => 'Akan',
        'am' => 'Amharic',
        'an' => 'Aragonese',
        'ar' => 'Arabic',
        'as' => 'Assamese',
        'av' => 'Avaric',
        'ay' => 'Aymara',
        'az' => 'Azerbaijani',
        'ba' => 'Bashkir',
        'be' => 'Belarusian',
        'bg' => 'Bulgarian',
        'bh' => 'Bihari',
        'bi' => 'Bislama',
        'bm' => 'Bambara',
        'bn' => 'Bengali',
        'bo' => 'Tibetan',
        'br' => 'Breton',
        'bs' => 'Bosnian',
        'ca' => 'Catalan; Valencian',
        'ce' => 'Chechen',
        'ch' => 'Chamorro',
        'co' => 'Corsican',
        'cr' => 'Cree',
        'cs' => 'Czech',
        'cu' => 'Church Slavic',
        'cv' => 'Chuvash',
        'cy' => 'Welsh',
        'da' => 'Danish',
        'de' => 'German',
        'dv' => 'Divehi; Dhivehi; Maldivian',
        'dz' => 'Dzongkha',
        'ee' => 'Ewe',
        'el' => 'Greek, Modern',
        'en' => 'English',
        'eo' => 'Esperanto',
        'es' => 'Spanish; Castilian',
        'et' => 'Estonian',
        'eu' => 'Basque',
        'fa' => 'Persian',
        'ff' => 'Fulah',
        'fi' => 'Finnish',
        'fj' => 'Fijian',
        'fo' => 'Faroese',
        'fr' => 'French',
        'fy' => 'Western Frisian',
        'ga' => 'Irish',
        'gd' => 'Gaelic; Scottish Gaelic',
        'gl' => 'Galician',
        'gn' => 'Guarani',
        'gu' => 'Gujarati',
        'gv' => 'Manx',
        'ha' => 'Hausa',
        'he' => 'Hebrew',
        'hi' => 'Hindi',
        'ho' => 'Hiri Motu',
        'hr' => 'Croatian',
        'ht' => 'Haitian; Haitian Creole',
        'hu' => 'Hungarian',
        'hy' => 'Armenian',
        'hz' => 'Herero',
        'ia' => 'Interlingua',
        'id' => 'Indonesian',
        'ie' => 'Interlingue; Occidental',
        'ig' => 'Igbo',
        'ii' => 'Sichuan Yi; Nuosu',
        'ik' => 'Inupiaq',
        'io' => 'Ido',
        'is' => 'Icelandic',
        'it' => 'Italian',
        'iu' => 'Inuktitut',
        'ja' => 'Japanese',
        'jv' => 'Javanese',
        'ka' => 'Georgian',
        'kg' => 'Kongo',
        'ki' => 'Kikuyu; Gikuyu',
        'kj' => 'Kuanyama; Kwanyama',
        'kk' => 'Kazakh',
        'kl' => 'Kalaallisut; Greenlandic',
        'km' => 'Central Khmer',
        'kn' => 'Kannada',
        'ko' => 'Korean',
        'kr' => 'Kanuri',
        'ks' => 'Kashmiri',
        'ku' => 'Kurdish',
        'kv' => 'Komi',
        'kw' => 'Cornish',
        'ky' => 'Kirghiz; Kyrgyz',
        'la' => 'Latin',
        'lb' => 'Luxembourgish; Letzeburgesch',
        'lg' => 'Ganda',
        'li' => 'Limburgan; Limburger; Limburgish',
        'ln' => 'Lingala',
        'lo' => 'Lao',
        'lt' => 'Lithuanian',
        'lu' => 'Luba-Katanga',
        'lv' => 'Latvian',
        'mg' => 'Malagasy',
        'mh' => 'Marshallese',
        'mi' => 'Maori',
        'mk' => 'Macedonian',
        'ml' => 'Malayalam',
        'mn' => 'Mongolian',
        'mo' => 'Moldavian; Moldovan',
        'mr' => 'Marathi',
        'ms' => 'Malay',
        'mt' => 'Maltese',
        'my' => 'Burmese',
        'na' => 'Nauru',
        'nb' => 'Bokmål, Norwegian; Norwegian Bokmål',
        'nd' => 'Ndebele, North; North Ndebele',
        'ne' => 'Nepali',
        'ng' => 'Ndonga',
        'nl' => 'Dutch (Flemish)',
        'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',
        'no' => 'Norwegian',
        'nr' => 'Ndebele, South; South Ndebele',
        'nv' => 'Navajo; Navaho',
        'ny' => 'Chichewa; Chewa; Nyanja',
        'oc' => 'Occitan (post 1500)',
        'oj' => 'Ojibwa',
        'om' => 'Oromo',
        'or' => 'Oriya',
        'os' => 'Ossetian; Ossetic',
        'pa' => 'Panjabi; Punjabi',
        'pi' => 'Pali',
        'pl' => 'Polish',
        'ps' => 'Pushto; Pashto',
        'pt' => 'Portuguese',
        'qu' => 'Quechua',
        'rm' => 'Romansh',
        'rn' => 'Rundi',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'rw' => 'Kinyarwanda',
        'sa' => 'Sanskrit',
        'sc' => 'Sardinian',
        'sd' => 'Sindhi',
        'se' => 'Northern Sami',
        'sg' => 'Sango',
        'si' => 'Sinhala; Sinhalese',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'sm' => 'Samoan',
        'sn' => 'Shona',
        'so' => 'Somali',
        'sq' => 'Albanian',
        'sr' => 'Serbian',
        'ss' => 'Swati',
        'st' => 'Sotho, Southern',
        'su' => 'Sundanese',
        'sv' => 'Swedish',
        'sw' => 'Swahili',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'tg' => 'Tajik',
        'th' => 'Thai',
        'ti' => 'Tigrinya',
        'tk' => 'Turkmen',
        'tl' => 'Tagalog',
        'tn' => 'Tswana',
        'to' => 'Tonga (Tonga Islands)',
        'tr' => 'Turkish',
        'ts' => 'Tsonga',
        'tt' => 'Tatar',
        'tw' => 'Twi',
        'ty' => 'Tahitian',
        'ug' => 'Uighur; Uyghur',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'uz' => 'Uzbek',
        've' => 'Venda',
        'vi' => 'Vietnamese',
        'vo' => 'Volapük',
        'wa' => 'Walloon',
        'wo' => 'Wolof',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'za' => 'Zhuang; Chuang',
        'zh' => 'Chinese',
        'zu' => 'Zulu');

    static function list_languages() {
        foreach (self::$languages as $id => $name) {
            echo $id, $name, EOL;
        }
    }

    static function get_language($id) {
        $lang = self::$languages[$id];
        if (empty($lang)) {
            throw GeobazaException(sprintf('Language with ISO ID %s does not exists!', $id));
        }
        return new Language($id, $lang);
    }
}

/**
 * Geography
 */

class LatLon {
    public $latitude, $longitude;

    function __construct($lat=NULL, $lon=NULL) {
        $this->latitude = $lat;
        $this->longitude = $lon;
    }
}

class Geography extends Serializer {
    public $center;

    function __construct($args) {
        foreach ($args as $key => $value) {
            $this->$key = $value;
        }
    }

    function as_xml() {
        $xml = new DOMDocument('1.0', 'utf-8');
        $root = $xml->createElement('center');
        $root->setAttribute('latitude', $this->center->latitude);
        $root->setAttribute('longitude', $this->center->longitude);
        $xml->appendChild($root);
        return $xml;
    }

    function as_array() {
        return array('center' => array('latitude' => $this->center->latitude, 'longitude' => $this->center->longitude));
    }
}

/**
 * Geographical objects
 */

class AbstractObject extends Serializer {
    protected $encoding = 'utf-8';
    protected $utf;
}

class GeobazaObject extends AbstractObject {
    protected $parent = NULL;
    protected $query, $encoding;

    public $id, $level, $type, $iso_id, $name, $translations, $child, $population, $geography;

    const COUNTRY = 'country';
    const REGION = 'region';
    const LOCALITY = 'locality';
    const SPECIAL = 'special';

    function __construct($data, $encoding='utf-8') {
        $this->encoding = strtolower($encoding);
        $this->id = (int)$data->id;
        $this->level = (int)$data->level;
        $this->type = $data->type;
        if (isset($data->iso_id)) {
            $this->iso_id = $data->iso_id;
        }

        $this->set_geography($data);
        $this->set_population($data);
        $this->set_translation($data);
        $this->set_name();
        $this->encode();
    }

    function __set($name, $value) {
        switch ($name) {
            case 'query':
                $this->query = $value;
            case 'parent':
                $this->parent = $value;
        }
    }

    function __get($name) {
        switch ($name) {
            case 'parent':
                $this->fetch_parent();
                return $this->parent;
        }
    }

    protected function fetch_parent() {
        if (is_int($this->parent)) {
            $data = $this->query->get_from($this->parent);

            if (count($data) == 2) {
                $obj = $data[0];
                $parent = $data[1]->parent;
            }
            else {
                $obj = $data[0];
                $parent = NULL;
            }
            $this->parent = Geobaza::create_instance($obj, $this->encoding, $childs=$this, $parent=$parent);
            $this->parent->query = $this->query;
        }
    }

    protected function set_geography($data) {
        $lat = $lon = NULL;
        if (isset($data->lat)) {
            $lat = $data->lat;
        }
        if (isset($data->lon)) {
            $lon = $data->lon;
        }
        $this->geography = new Geography(array('center' => new LatLon($lat, $lon)));
    }

    protected function set_population($data) {
        $this->population = NULL;
        if (isset($data->population)) {
            $this->population = (int)$data->population;
        }
    }

    protected function set_translation($data) {
        $RAW_MAP = array(
            'official' => array(0, 'name_official'),
            'alt' => array(1, 'name'),
        );

        $this->translations = array();
        foreach ($RAW_MAP as $type => $value) {
            $priority = $value[0];
            $raw_key = $value[1];

            $obj = array('type' => $type);
            foreach ($data->$raw_key as $lang => $name) {
                $obj[strtolower($lang)] = $name;
            }
            $this->translations[] = (object)$obj;
        }
    }

    protected function set_name() {
        $this->name = $this->translations[0]->en;
    }

    protected function encode() {
        $this->utf = new stdClass();
        $this->utf->translations = array();
        foreach ($this->translations as $item) {
            $this->utf->translations[] = clone($item);
        }
        $this->utf->name = $this->name;

        for ($i = 0; $i < count($this->translations); $i++) {
            foreach ($this->translations[$i] as $key => $value) {
                if ($key != 'type') {
                    $value = convert_encoding($value, $this->encoding);
                }
                $this->translations[$i]->$key = $value;
            }
        }
    }

    function as_xml() {
        $xml = new DOMDocument('1.0', $this->encoding);
        $root = $xml->createElement('object');
        $xml->appendChild($root);
        $root->setAttribute('type', $this->type);
        $root->setAttribute('id', $this->id);
        $root->setAttribute('level', $this->level);
        $this->fetch_parent();
        if (!empty($this->parent)) {
            $root->setAttribute('parent', $this->parent->id);
        }
        if (!empty($this->child)) {
            $root->setAttribute('child', $this->child->id);
        }

        $name = $xml->createElement('name');
        $name->appendChild($xml->createTextNode($this->utf->name));
        $root->appendChild($name);

        $iso_id = $xml->createElement('iso-id');
        $iso_id->appendChild($xml->createTextNode($this->iso_id));
        $root->appendChild($iso_id);

        $population = $xml->createElement('population');
        $population->appendChild($xml->createTextNode($this->population));
        $root->appendChild($population);

        $translations = $xml->createElement('translations');
        foreach($this->utf->translations as $item) {
            $group = $xml->createElement('group');
            $translations->appendChild($group);
            $group->setAttribute('type', $item->type);
            foreach($item as $key => $value) {
                if ($key != 'type') {
                    $translation = $xml->createElement('item');
                    $translation->setAttribute('language', $key);
                    $translation->appendChild($xml->createTextNode($value));
                    $group->appendChild($translation);
                }
            }
        }
        $root->appendChild($translations);

        $geography = $xml->createElement('geography');
        $geography_xml = $this->geography->as_xml();
        $node = $xml->importNode($geography_xml->documentElement, true);
        $geography->appendChild($node);
        $root->appendChild($geography);

        return $xml;
    }

    function as_array() {
        $object = array(
            'type' => $this->type,
            'name' => $this->utf->name,
            'iso_id' => $this->iso_id,
            'id' => $this->id,
            'level' => $this->level,
        );

        $this->fetch_parent();
        if (!empty($this->parent)) {
            $object['parent'] = $this->parent->id;
        }
        if (!empty($this->child)) {
            $object['child'] = $this->child->id;
        }

        $object['geography'] = $this->geography->as_array();
        $object['population'] = $this->population;
        $object['translations'] = $this->utf->translations;

        return $object;
    }
}

class Region extends GeobazaObject {
    public $language;

    function __construct($data, $encoding='utf-8') {
        $this->set_lang($data);
        parent::__construct($data, $encoding);
    }

    protected function set_lang($data) {
        $this->language = NULL;
        if (isset($data->lang)) {
            $lang = strtolower($data->lang);
            if ($lang) {
                $this->language = Languages::get_language($lang);
            }
        }
    }

    protected function encode() {
        parent::encode();
        if ($this->language instanceof Language) {
            $this->utf->language = clone($this->language);
        } else {
            $this->utf->language = $this->language;
        }

        if ($this->encoding != 'utf-8') {
            $this->language->name = convert_encoding($this->language->name, $this->encoding);
        }
    }

    function as_xml() {
        $xml = parent::as_xml();
        $language = $xml->createElement('language');
        $xml->documentElement->appendChild($language);
        if (!empty($this->utf->language)) {
            $language->setAttribute('id', $this->utf->language->id);
            $language->appendChild($xml->createTextNode($this->utf->language->name));
        }
        return $xml;
    }

    function as_array() {
        $object = parent::as_array();
        $language = NULL;
        if (!empty($this->utf->language)) {
            $language = array(
                'name' => $this->utf->language->name,
                'id' => $this->utf->language->id
            );
        }
        $object['language'] = $language;
        return $object;
    }
}

class Country extends Region {
    public $tld;

    function __construct($data, $encoding='utf-8') {
        parent::__construct($data, $encoding);
        $this->set_tld($data);
    }

    protected function set_tld() {
        $this->tld = NULL;
        if (isset($this->iso_id)) {
            if ($this->iso_id == 'GB') {
                $this->tld = 'uk';
            }
            else {
                $this->tld = strtolower($this->iso_id);
            }
        }
    }

    function as_xml() {
        $xml = parent::as_xml();
        $tld = $xml->createElement('tld');
        $xml->documentElement->appendChild($tld);
        $tld->appendChild($xml->createTextNode($this->tld));
        return $xml;
    }

    function as_array() {
        $object = parent::as_array();
        $object['tld'] = $this->tld;
        return $object;
    }
}

class Locality extends GeobazaObject {}

class SpecialRange extends AbstractObject {
    function __construct($data, $encoding='utf-8') {
        $this->utf = new stdClass();
        $this->type = GeobazaObject::SPECIAL;
        $this->encoding = strtolower($encoding);
        $name = $data->special ? $data->special : NULL;
        $this->name = $this->utf->name = $name;
        if (!empty($name) && $this->encoding != 'utf-8') {
            $this->name = convert_encoding($name, $this->encoding);
        }
    }

    function as_xml() {
        $xml = new DOMDocument('1.0', $this->encoding);
        $root = $xml->createElement('object');
        $root->setAttribute('type', $this->type);
        $name = $xml->createElement('name');
        $name->appendChild($xml->createTextNode($this->utf->name));
        $root->appendChild($name);
        $xml->appendChild($root);
        return $xml;
    }

    function as_array() {
        $object = array('type' => $this->type, 'name' => $this->utf->name);

        return $object;
    }
}

/**
 * Query
 */

class Geobaza extends Serializer implements Iterator, Countable, ArrayAccess {
    /**
     * Iterator position
     * @var integer
     */
    private $position = 0;

    private $is_special = NULL;

    private $list = array();
    private $country;
    private $regions = array();
    private $localities = array();

    function __construct($meta) {
        $this->meta = $meta;
        $this->headers = $meta->headers;
    }

    function current() {
        return $this->list[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        $this->position++;
    }

    function rewind() {
        $this->position = 0;
    }

    function valid() {
        if ($this->position < count($this->list)) {
            return true;
        }
        return false;
    }

    function offsetSet($offset, $value) {
        throw new Exception('You can not assign values to Geobaza!');
    }

    function offsetExists($offset) {
        return isset($this->list[$offset]);
    }

    function offsetUnset($offset) {
        throw new Exception('You can not change Geobaza object list!');
    }

    function offsetGet($offset) {
        if (isset($this->list[$offset])) {
            return $this->list[$offset];
        }
        throw new OutOfRangeException('Invalid offset!');
    }

    protected function set_special_flag() {
        if (!isset($this->is_special)) {
            foreach ($this->list as $obj) {
                if ($obj->type == GeobazaObject::SPECIAL) {
                    $this->is_special = true;
                    return;
                }
            }
            $this->is_special = false;
        }
    }

    function __get($name) {
        switch ($name) {
            case 'is_special':
                $this->set_special_flag();
                return $this->is_special;
            case 'localities':
                return self::from_list($this->localities, $this->meta);
            case 'regions':
                return self::from_list($this->regions, $this->meta);
            case 'country':
                return $this->country;
        }
    }

    function append($item) {
        $this->add_by_type($item);
        $this->list[] = $item;
    }

    function extend($items) {
        foreach ($items as $item) {
            $this->append($item);
        }
    }

    static function from_array($data, $meta) {
        $geobaza = new self($meta);
        $data = array_reverse($data);
        $parent = NULL;

        foreach ($data as $obj) {
            if (isset($obj->special)) {
                $geobaza->append(new SpecialRange($obj, $meta->encoding));
            }
            else if (isset($obj->parent)) {
                # If object only contains link
                $parent = $obj->parent;
            }
            else {
                $inst = Geobaza::create_instance($obj, $meta->encoding);
                $inst->query = $meta->query;
                $geobaza->append($inst);
                $inst->parent = $parent;
                if ($parent instanceof AbstractObject) {
                    $parent->child = $inst;
                }
                $parent = $inst;
            }
        }
        return $geobaza;
    }

    static function from_list($items, $meta) {
        $geobaza = new self($meta);
        $geobaza->extend($items);
        return $geobaza;
    }

    static function create_instance($obj, $encoding, $child=NULL, $parent=NULL) {
        $type_map = array(
            GeobazaObject::COUNTRY => 'Country',
            GeobazaObject::REGION => 'Region',
            GeobazaObject::LOCALITY => 'Locality'
        );
        $type = $obj->type;
        $item_class = $type_map[$type];
        $item = new $item_class($obj, $encoding);
        $item->child = $child;
        if (!empty($parent)) {
            $item->parent = $parent;
        }
        return $item;
    }

    protected function add_by_type($obj) {
        switch ($obj->type) {
            case GeobazaObject::COUNTRY:
                $this->country = $obj;
                break;
            case GeobazaObject::REGION:
                $this->regions[] = $obj;
                break;
            case GeobazaObject::LOCALITY:
                $this->localities[] = $obj;
                break;
        }
    }

    function name_path($delimiter=', ') {
        return join($delimiter, $this->name_list());
    }

    function name_list() {
        $list = array();
        foreach ($this as $item) {
            $list[] = $item->name;
        }
        return $list;
    }

    function id_list() {
        $list = array();
        foreach ($this as $item) {
            $list[] = $item->id;
        }
        return $list;
    }

    function first() {
        return $this->list[0];
    }

    function last() {
        return $this->list[count($this->list) - 1];
    }

    function count() {
        return count($this->list);
    }

    function as_xml() {
        $xml = new DOMDocument('1.0', $this->meta->encoding);
        $root = $xml->createElement('geobaza');
        $root->setAttribute('api-version', $this->headers->api_version);
        $root->setAttribute('release', $this->headers->release);
        $root->setAttribute('build-timestamp', $this->headers->build_timestamp);
        $root->setAttribute('build-date', $this->headers->build_date);
        $root->setAttribute('lite', (int)$this->headers->lite);
        $root->setAttribute('query-timestamp', time(true));
        $root->setAttribute('ip', $this->meta->ip);
        $this->set_special_flag();
        $root->setAttribute('is-special', (int)$this->is_special);

        $objects = $xml->createElement('objects');
        foreach ($this as $item) {
            $item_xml = $item->as_xml();
            $node = $xml->importNode($item_xml->documentElement, true);
            $objects->appendChild($node);
        }
        $root->appendChild($objects);
        $xml->appendChild($root);
        return $xml;
    }

    function as_array() {
        $this->set_special_flag();
        $object = array(
            'api_version' => $this->headers->api_version,
            'release' => $this->headers->release,
            'build_timestamp' => $this->headers->build_timestamp,
            'build_date' => $this->headers->build_date,
            'lite' => (int)$this->headers->lite,
            'query_timestamp' => time(true),
            'ip' => $this->meta->ip,
            'is_special' => (int)$this->is_special,
        );
        $object['objects'] = array();
        foreach ($this as $item) {
            $object['objects'][] = $item->as_array();
        }
        return $object;
    }
}

class GeobazaQuery extends Serializer {
    private $level = array();
    private $offset;
    private $f;
    private $meta;
    private $encoding;

    function __get($name) {
        if ($name == 'encoding') {
            return $this->encoding;
        }
    }

    function __construct($path=FILE_PATH, $cache=NO_CACHE, $encoding='utf-8') {
        $this->encoding = strtolower($encoding);

        if ($cache == MEMORY_CACHE) {
            $this->f = BinaryBlob::init($path);
            $this->f->seek(0);
        }
        else {
            $this->f = BinaryFile::init($path);
            $this->f->seek(0);
        }

        if ($this->f->read(7) != 'GEOBAZA') {
            throw new GeobazaException('Invalid datafile signature!');
        }

        # Headers
        $unpack = $this->f->unpack(2, 'nlen');
        $this->headers = json_decode($this->f->read($unpack['len']));
        $this->headers->lite = (isset($this->headers->lite) ? true : false);
        $this->headers->release = trim($this->headers->release);

        while (true) {
            $data = $this->f->unpack(1, 'Cdata');
            $hi = ($data['data'] >> 4) & 0x0f;
            $this->level[] = $hi;
            if ($hi == 0) {
                break;
            }
            $lo = $data['data'] & 0x0f;
            $this->level[] = $lo;
            if ($lo == 0) {
                break;
            }
        }

        $this->offset = $this->f->tell();
    }

    protected function get_json($offset, $path=false) {
        $json_str = array();
        while ($offset) {
            $length = $this->get_length($offset);
            if ($length > 0) {
                $obj_str = $this->f->read($length);
                $unp_offset = $this->f->unpack(4, 'Ndata');
                $offset = $unp_offset['data'];
                $json_str[] = $obj_str;
                if ($path == false) {
                    $length = $this->get_length($offset);
                    if ($length && $offset) {
                        $json_str[] = sprintf('{"parent": %d}', $offset);
                    }
                    break;
                }
            }
            else {
                return;
            }
        }
        return sprintf('[%s]', join(',', $json_str));
    }

    protected function get_length($offset) {
        $position = $offset & 0x7fffffff;
        $this->f->seek($position, 0);
        $unpack = $this->f->unpack(2, 'nlen');
        return $unpack['len'] & 0xffff;
    }

    protected function get_data($ip, $path=false) {
        $offset = $this->offset;
        $ip_int = ip2long($ip);
        $shift = 32;
        for ($i = 0; $i < sizeof($this->level); $i++) {
            $shift -= $this->level[$i];
            $index = (($ip_int >> $shift)) & ((1 << $this->level[$i]) - 1);
            $tell = $offset + $index * 4;
            $this->f->seek($tell, 0);
            $unpack = $this->f->unpack(4, 'Ndata');
            $offset = $unpack['data'] & 0xffffffff;
            if ($offset & 0x80000000) {
                return $this->get_json($offset, $path);
            }
        }
    }

    protected function get_meta($ip) {
        $meta = array(
            'ip' => $ip,
            'query' => $this,
            'headers' => $this->headers,
            'encoding' => $this->encoding
        );
        return (object)$meta;
    }

    function get($ip) {
        $json_str = $this->get_data($ip);
        if (!empty($json_str)) {
            $geobaza = Geobaza::from_array(json_decode($json_str), $this->get_meta($ip));
            return $geobaza->first();
        }
    }

    function get_list($ip) {
        $json_str = $this->get_data($ip);
        if (!empty($json_str)) {
            $geobaza = Geobaza::from_array(json_decode($json_str), $this->get_meta($ip));
            return $geobaza;
        }
    }

    function get_path($ip) {
        $json_str = $this->get_data($ip, $path=true);
        if (!empty($json_str)) {
            $geobaza = Geobaza::from_array(json_decode($json_str), $this->get_meta($ip));
            return $geobaza;
        }
    }

    function get_from($offset) {
        $json_str = $this->get_json($offset);
        if (!empty($json_str)) {
            return json_decode($json_str);
        }
    }
}

/**
 * Shortcut functions
 */

function lookup($ip) {
    try {
        $geobaza = new GeobazaQuery();
        return $geobaza->get_path($ip);
    }
    catch (GeobazaException $e) {
        throw $e;
    }
}

function lookup_xml($ip, $pretty=false) {
    $geobaza = lookup($ip);
    if (!empty($geobaza)) {
        if ($pretty == true) {
            return $geobaza->to_pretty_xml();
        }
        return $geobaza->to_xml();
    }
}

function lookup_json($ip, $pretty=false) {
    $geobaza = lookup($ip);
    if (!empty($geobaza)) {
        if ($pretty == true) {
            return $geobaza->to_pretty_json();
        }
        return $geobaza->to_json();
    }
}
?>
