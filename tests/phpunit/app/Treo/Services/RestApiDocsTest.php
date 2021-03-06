<?php
/**
 * This file is part of EspoCRM and/or TreoCore, and/or KennerCore.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoCore is EspoCRM-based Open Source application.
 * Copyright (C) 2017-2020 TreoLabs GmbH
 * Website: https://treolabs.com
 *
 * KennerCore is TreoCore-based Open Source application.
 * Copyright (C) 2020 Kenner Soft Service GmbH
 * Website: https://kennersoft.de
 *
 * KennerCore as well as TreoCore and EspoCRM is free software:
 * you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * KennerCore as well as TreoCore and EspoCRM is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of
 * the "KennerCore", "EspoCRM" and "TreoCore" words.
 */
declare(strict_types=1);

namespace Treo\Services;

use Espo\Core\Utils\Config;
use PHPUnit\Framework\TestCase;
use Treo\Core\Container;

/**
 * Class RestApiDocsTest
 *
 * @author r.ratsun@zinitsolutions.com
 */
class RestApiDocsTest extends TestCase
{
    public function testIsGenerateDocumentationReturnTrue()
    {
        // create service
        $service = $this->createMockService(RestApiDocs::class, ['getHtml', 'setToFile']);
        $service
            ->expects($this->any())
            ->method('getHtml')
            ->willReturn('some html');
        $service
            ->expects($this->any())
            ->method('setToFile')
            ->willReturn(true);

        // test
        $this->assertTrue($service->generateDocumentation());
    }

    public function testIsGenerateDocumentationReturnFalse()
    {
        // create service
        $service = $this->createMockService(RestApiDocs::class, ['getHtml']);
        $service
            ->expects($this->any())
            ->method('getHtml')
            ->willReturn('');

        // test 1
        $this->assertFalse($service->generateDocumentation());

        // create service
        $service1 = $this->createMockService(RestApiDocs::class, ['getHtml', 'setToFile']);
        $service1
            ->expects($this->any())
            ->method('getHtml')
            ->willReturn('some html');
        $service1
            ->expects($this->any())
            ->method('setToFile')
            ->willReturn(false);

        // test 2
        $this->assertFalse($service1->generateDocumentation());
    }

    /**
     * Create mock service
     *
     * @param string $name
     * @param array  $methods
     *
     * @return mixed
     */
    protected function createMockService(string $name, array $methods = [])
    {
        // define path to core app
        if (!defined('CORE_PATH')) {
            define('CORE_PATH', dirname(dirname(dirname(__DIR__))));
        }

        $service = $this->createPartialMock($name, array_merge(['getContainer', 'getConfig'], $methods));
        $service
            ->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->getContainer());
        $service
            ->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->getConfig());

        return $service;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = $this->createPartialMock(Container::class, ['getConfig']);
        $container
            ->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->getConfig());

        return $container;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        $config = $this->createPartialMock(Config::class, ['set', 'get', 'save']);
        $config
            ->expects($this->any())
            ->method('set')
            ->willReturn(true);
        $config
            ->expects($this->any())
            ->method('get')
            ->willReturn(true);
        $config
            ->expects($this->any())
            ->method('save')
            ->willReturn(true);

        return $config;
    }
}
