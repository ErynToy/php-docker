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

require_once(__DIR__ . "/../build-scripts/src/DetectPhpVersion.php");

class DetectPhpVersionTest extends TestCase
{
    const PHP_73 = '7.3.28';
    const PHP_74 = '7.4.19';
    const PHP_80 = '8.0.6';
    const AVAILABLE_VERSIONS = [
        self::PHP_73,
        self::PHP_74,
        self::PHP_80,
    ];

    public function testDetectsPhpVersionFromComposer()
    {
        $version = DetectPhpVersion::versionFromComposer(__DIR__ . '/samples/oauth.json', self::AVAILABLE_VERSIONS);
        $this->assertEquals(self::PHP_80, $version);
    }

    public function testDetectsHighestVersion()
    {
        $version = DetectPhpVersion::version('^8', self::AVAILABLE_VERSIONS);
        $this->assertEquals(self::PHP_80, $version);
    }

    /**
     * @expectedException ExactVersionException
     */
    public function testExactVersionDirect()
    {
        $this->expectException(ExactVersionException::class);
        $version = DetectPhpVersion::version('8.0.3', self::AVAILABLE_VERSIONS);
    }

    /**
     * @expectedException InvalidVersionException
     */
    public function testInvalidVersionDirect()
    {
        $this->expectException(InvalidVersionException::class);
        $version = DetectPhpVersion::version('^8.0.100', self::AVAILABLE_VERSIONS);
    }

    /**
     * @expectedException ExactVersionException
     */
    public function testExactVersion()
    {
        $this->expectException(ExactVersionException::class);
        $version = DetectPhpVersion::versionFromComposer(__DIR__ . '/samples/exact.json', self::AVAILABLE_VERSIONS);
    }

    /**
     * @expectedException NoSpecifiedVersionException
     */
    public function testNoVersionString()
    {
        $this->expectException(NoSpecifiedVersionException::class);
        $version = DetectPhpVersion::versionFromComposer(__DIR__ . '/samples/no_version.json', self::AVAILABLE_VERSIONS);
    }

    /**
     * @expectedException InvalidVersionException
     */
    public function testInvalidVersion()
    {
        $this->expectException(InvalidVersionException::class);
        $version = DetectPhpVersion::versionFromComposer(__DIR__ . '/samples/invalid.json', self::AVAILABLE_VERSIONS);
    }
}
