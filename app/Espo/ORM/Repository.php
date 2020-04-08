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

namespace Espo\ORM;

abstract class Repository
{
    /**
     * @var EntityFactory EntityFactory object.
     */
    protected $entityFactory;

    /**
     * @var EntityManager EntityManager object.
     */
    protected $entityManager;


    /**
     * @var iModel Seed entity.
     */
    protected $seed;

    /**
     * @var string Class Name of aggregate root.
     */
    protected $entityClassName;

    /**
     * @var string Model Name of aggregate root.
     */
    protected $entityType;

    public function __construct($entityName, EntityManager $entityManager, EntityFactory $entityFactory)
    {
        $this->entityName = $entityName;
        $this->entityFactory = $entityFactory;
        $this->seed = $this->entityFactory->create($entityName);
        $this->entityClassName = get_class($this->seed);
        $this->entityManager = $entityManager;
    }

    protected function getEntityFactory()
    {
        return $this->entityFactory;
    }

    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    abstract public function get($id = null);

    abstract public function save(Entity $entity);

    abstract public function remove(Entity $entity);

    abstract public function find(array $params);

    abstract public function findOne(array $params);

    abstract public function getAll();

    abstract public function count(array $params);
}

