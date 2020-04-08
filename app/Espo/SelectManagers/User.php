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

namespace Espo\SelectManagers;

class User extends \Espo\Core\SelectManagers\Base
{
    protected function access(&$result)
    {
        parent::access($result);

        if (!$this->getUser()->isAdmin()) {
            $result['whereClause'][] = array(
                'isActive' => true
            );
        }
        if ($this->getAcl()->get('portalPermission') !== 'yes') {
            $result['whereClause'][] = array(
                'OR' => [
                    ['isPortalUser' => false],
                    ['id' => $this->getUser()->id]
                ]
            );
        }
        $result['whereClause'][] = array(
            'isSuperAdmin' => false
        );
    }

    protected function filterActive(&$result)
    {
        $result['whereClause'][] = array(
            'isActive' => true,
            'isPortalUser' => false
        );
    }

    protected function filterActivePortal(&$result)
    {
        $result['whereClause'][] = array(
            'isActive' => true,
            'isPortalUser' => true
        );
    }

    protected function filterPortal(&$result)
    {
        $result['whereClause'][] = array(
            'isPortalUser' => true
        );
    }

    protected function filterInternal(&$result)
    {
        $result['whereClause'][] = array(
            'isPortalUser' => false
        );
    }

    protected function boolFilterOnlyMyTeam(&$result)
    {
        $this->addJoin('teams', $result);
        $result['whereClause'][] = array(
        	'teamsMiddle.teamId' => $this->getUser()->getLinkMultipleIdList('teams')
        );
        $this->setDistinct(true, $result);
    }

    protected function accessOnlyOwn(&$result)
    {
        $result['whereClause'][] = array(
            'id' => $this->getUser()->id
        );
    }

    protected function accessPortalOnlyOwn(&$result)
    {
        $result['whereClause'][] = array(
            'id' => $this->getUser()->id
        );
    }

    protected function accessOnlyTeam(&$result)
    {
        $this->setDistinct(true, $result);
        $this->addLeftJoin(['teams', 'teamsAccess'], $result);
        $result['whereClause'][] = array(
            'OR' => array(
                'teamsAccess.id' => $this->getUser()->getLinkMultipleIdList('teams'),
                'id' => $this->getUser()->id
            )
        );
    }
}

