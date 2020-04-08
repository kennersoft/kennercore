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

namespace Espo\Services;

use \Espo\ORM\Entity;

class LastViewed extends \Espo\Core\Services\Base
{
    protected function init()
    {
        parent::init();
        $this->addDependency('serviceFactory');
        $this->addDependency('metadata');
    }

    public function get()
    {
        $entityManager = $this->getInjection('entityManager');

        $maxSize = $this->getConfig()->get('lastViewedCount', 20);

        $actionHistoryRecordService = $this->getInjection('serviceFactory')->create('ActionHistoryRecord');

        $scopes = $this->getInjection('metadata')->get('scopes');

        $targetTypeList = array_filter(array_keys($scopes), function ($item) use ($scopes) {
            return !empty($scopes[$item]['object']);
        });

        $collection = $this->getEntityManager()->getRepository('ActionHistoryRecord')->where(array(
            'userId' => $this->getUser()->id,
            'action' => 'read',
            'targetType' => $targetTypeList
        ))->order(3, true)->limit(0, $maxSize)->select([
            'targetId', 'targetType', 'MAX:number'
        ])->groupBy([
            'targetId', 'targetType'
        ])->find();

        foreach ($collection as $i => $entity) {
            $actionHistoryRecordService->loadParentNameFields($entity);
            $entity->id = $i;
        }

        return array(
            'total' => count($collection),
            'collection' => $collection
        );
    }
}

