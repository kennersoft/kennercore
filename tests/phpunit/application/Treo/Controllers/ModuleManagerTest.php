<?php
/**
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2019 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2019 TreoLabs GmbH
 * Website: http://www.treopim.com
 *
 * TreoPIM as well as EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TreoPIM as well as EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
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
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word
 * and "TreoPIM" word.
 */

declare(strict_types=1);

namespace Treo\Controllers;

use PHPUnit\Framework\TestCase;

/**
 * Class ModuleManagerTest
 *
 * @author r.zablodskiy@treolabs.com
 */
class ModuleManagerTest extends TestCase
{
    /**
     * Test is actionList method exists
     */
    public function testIsActionListExists()
    {
        $mock = $this->createPartialMock(ModuleManager::class, []);

        // test
        $this->assertTrue(method_exists($mock, 'actionList'));
    }

    /**
     * Test is actionInstallModule method exists
     */
    public function testIsActionInstallModuleExists()
    {
        $mock = $this->createPartialMock(ModuleManager::class, []);

        // test
        $this->assertTrue(method_exists($mock, 'actionInstallModule'));
    }

    /**
     * Test is actionUpdateModule method exists
     */
    public function testIsActionUpdateModuleExists()
    {
        $mock = $this->createPartialMock(ModuleManager::class, []);

        // test
        $this->assertTrue(method_exists($mock, 'actionUpdateModule'));
    }

    /**
     * Test is actionDeleteModule method exists
     */
    public function testIsActionDeleteModuleExists()
    {
        $mock = $this->createPartialMock(ModuleManager::class, []);

        // test
        $this->assertTrue(method_exists($mock, 'actionDeleteModule'));
    }

    /**
     * Test is actionCancel method exists
     */
    public function testIsActionCancelExists()
    {
        $mock = $this->createPartialMock(ModuleManager::class, []);

        // test
        $this->assertTrue(method_exists($mock, 'actionCancel'));
    }

    /**
     * Test is actionLogs method exists
     */
    public function testIsActionLogsExists()
    {
        $mock = $this->createPartialMock(ModuleManager::class, []);

        // test
        $this->assertTrue(method_exists($mock, 'actionLogs'));
    }
}