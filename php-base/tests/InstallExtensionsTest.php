<?php
/**
 * Copyright 2017 Google Inc.
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

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../build-scripts/src/InstallExtensions.php");

class InstallExtensionsTest extends TestCase
{
    public function testDetectsPackagedExtensions()
    {
        $output = null;
        $retval = null;

        echo "Available PHP Modules \n";
        exec('php -m', $output, $retval);
        print_r($output);
        echo "\n";
        echo "Nginx Modules\n";
        exec('ls -al /usr/lib/nginx/modules', $output, $retval);
        print_r($output);

        $installer = new InstallExtensions(__DIR__ . '/samples/oauth.json');
        $this->assertEquals(['oauth'], $installer->extensions(), implode(',', $installer->errors()));
    }

    public function testDetectsSharedExtensions()
    {
        $installer = new InstallExtensions(__DIR__ . '/samples/shared.json');
        $this->assertEquals(['ev'], $installer->extensions());
    }

    public function testInstallsExtensions()
    {
        $output = tempnam("/tmp", "php.ini");
        $installer = new InstallExtensions(__DIR__ . '/samples/shared.json', $output);
        $this->assertTrue($installer->installExtensions());
        $this->assertEquals("extension=ev.so\n", file_get_contents($output));

        unlink($output);
    }

}
