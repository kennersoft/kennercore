<?php
/**
 * This file is part of EspoCRM and/or TreoCore.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2019 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoCore is EspoCRM-based Open Source application.
 * Copyright (C) 2017-2019 TreoLabs GmbH
 * Website: https://treolabs.com
 *
 * TreoCore as well as EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TreoCore as well as EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
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
 * and "TreoCore" word.
 */

namespace Treo\Core\Migration;

use PDO;
use Treo\Core\Container;
use Treo\Core\ORM\EntityManager;
use Treo\Core\Utils\Config;

/**
 * AbstractMigration class
 *
 * @author r.ratsun <r.ratsun@treolabs.com
 */
abstract class AbstractMigration
{
    /**
     * @var Container
     * @deprecated 23.12.2019. Remove it soon.
     */
    private $container;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param PDO    $pdo
     * @param Config $config
     */
    public function __construct(PDO $pdo, Config $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    /**
     * Up to current
     */
    public function up(): void
    {
    }

    /**
     * Down to previous version
     */
    public function down(): void
    {
    }

    /**
     * Set container
     *
     * @param Container $container
     *
     * @return AbstractMigration
     * @deprecated 23.12.2019. Remove it soon.
     */
    public function setContainer(Container $container): AbstractMigration
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get container
     *
     * @return Container
     * @deprecated 23.12.2019. Remove it soon.
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Run rebuild action
     *
     * @deprecated 23.12.2019. Remove it soon.
     */
    protected function runRebuild(): void
    {
        $this->getContainer()->get('dataManager')->rebuild();
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     * @deprecated 23.12.2019. Remove it soon.
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->getContainer()->get('entityManager');
    }

    /**
     * Get config
     *
     * @return Config
     */
    protected function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get PDO
     *
     * @return PDO
     */
    protected function getPdo(): PDO
    {
        return $this->pdo;
    }
}
