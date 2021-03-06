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

namespace Treo\Core\EntryPoints;

use Espo\Core\Acl;
use Espo\Core\Utils\ClientManager;
use Espo\Core\Utils\DateTime;
use Espo\Core\Utils\NumberUtil;
use Espo\Entities\User;
use Treo\Core\Container;
use Treo\Core\ORM\EntityManager;
use Treo\Core\ServiceFactory;
use Treo\Core\Utils\Config;
use Treo\Core\Utils\File\Manager;
use Treo\Core\Utils\Language;
use Treo\Core\Utils\Metadata;

/**
 * Class AbstractEntryPoint
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
abstract class AbstractEntryPoint
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var bool
     */
    public static $authRequired = true;

    /**
     * @var bool
     */
    public static $notStrictAuth = false;

    /**
     * AbstractEntryPoint constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        return $this->getContainer()->get('user');
    }

    /**
     * @return Acl
     */
    protected function getAcl(): Acl
    {
        return $this->getContainer()->get('acl');
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->getContainer()->get('entityManager');
    }

    /**
     * @return ServiceFactory
     */
    protected function getServiceFactory(): ServiceFactory
    {
        return $this->getContainer()->get('serviceFactory');
    }

    /**
     * @return Config
     */
    protected function getConfig(): Config
    {
        return $this->getContainer()->get('config');
    }

    /**
     * @return Metadata
     */
    protected function getMetadata(): Metadata
    {
        return $this->getContainer()->get('metadata');
    }

    /**
     * @return DateTime
     */
    protected function getDateTime(): DateTime
    {
        return $this->getContainer()->get('dateTime');
    }

    /**
     * @return NumberUtil
     */
    protected function getNumber(): NumberUtil
    {
        return $this->getContainer()->get('number');
    }

    /**
     * @return Manager
     */
    protected function getFileManager(): Manager
    {
        return $this->getContainer()->get('fileManager');
    }

    /**
     * @return Language
     */
    protected function getLanguage(): Language
    {
        return $this->getContainer()->get('language');
    }

    /**
     * @return ClientManager
     */
    protected function getClientManager(): ClientManager
    {
        return $this->getContainer()->get('clientManager');
    }
}
