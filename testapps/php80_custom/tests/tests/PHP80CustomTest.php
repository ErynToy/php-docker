<?php
/**
 * Copyright 2015 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Cloud\tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class PHP80CustomTest extends TestCase
{
    private $client;

    private static $extensions = array(
        # static
        'date',
        'libxml',
        'openssl',
        'pcre',
        'zlib',
        'bz2',
        'ctype',
        'curl',
        'dom',
        'fileinfo',
        'filter',
        'hash',
        'iconv',
        'json',
        'mailparse',
        'SPL',
        'session',
        'PDO',
        'standard',
        'pcntl',
        'pdo_pgsql',
        'pgsql',
        'Phar',
        'posix',
        'readline',
        'Reflection',
        'mysqlnd',
        'SimpleXML',
        'sockets',
        'pdo_mysql',
        'mysqli',
        'tokenizer',
        'xml',
        'xmlreader',
        'xmlwriter',
        'zip',
        'cgi-fcgi',
        # shared
        'apcu',
        'bcmath',
        'calendar',
        'exif',
        'ftp',
        'gd',
        'gettext',
        'intl',
        'mbstring',
        'memcached',
        'mysql',
        'redis',
        'shmop',
        'soap',
        'sqlite3',
        'pdo_sqlite',
        'xmlrpc',
        'xsl',
        'mongodb',
        'imagick',
    );

    public static function setUpBeforeClass(): void
    {
        // Wait for nginx to start
        sleep(3);
    }

    public function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://php80-custom:8080/']);
    }

    public function testExtensions()
    {
        $resp = $this->client->get('extensions.php');
        $loaded = $resp->getBody()->getContents();
        foreach (self::$extensions as $ext) {
            $this->assertStringContainsString($ext, $loaded);
        }
    }

    public function testApcIsAbleToExecuteCommonOperations()
    {
        $resp = $this->client->get('apc.php');
        $body = $resp->getBody()->getContents();

        $this->assertStringContainsString('success storing in apcu', $body);
        $this->assertStringContainsString('success fetching from apcu', $body);
        $this->assertStringContainsString('success deleting from apcu', $body);
    }

    public function testImagickCanLoad()
    {
        $resp = $this->client->get('imagick.php');
        $body = $resp->getBody()->getContents();

        // test image should by 300px by 1px
        $this->assertStringContainsString('300x1', $body);
    }

    public function testFrontControllerFileEnv()
    {
        // Access the top page and it should be served by app.php
        $resp = $this->client->get('');
        $body = $resp->getBody()->getContents();
        $this->assertStringContainsString('FRONT_CONTROLLER_FILE works', $body);
    }
}
