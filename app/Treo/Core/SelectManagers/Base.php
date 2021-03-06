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

namespace Treo\Core\SelectManagers;

/**
 * Class Base
 *
 * @author r.ratsun r.ratsun@zinitsolutions.com
 */
class Base extends \Espo\Core\SelectManagers\Base
{
    /**
     * @param array $result
     */
    protected function accessOnlyOwn(&$result)
    {
        if ($this->hasAssignedUsersField()) {
            $this->setDistinct(true, $result);
            $this->addLeftJoin('assignedUsers', $result);
            $result['whereClause'][] = array(
                'assignedUsers.id' => $this->getUser()->id
            );
            return;
        }

        if ($this->hasOwnerUserField()) {
            $d['ownerUserId'] = $this->getUser()->id;
        }
        if ($this->hasAssignedUserField()) {
            $d['assignedUserId'] = $this->getUser()->id;
        }
        if ($this->hasCreatedByField() && !$this->hasAssignedUserField() && !$this->hasOwnerUserField()) {
            $d['createdById'] = $this->getUser()->id;
        }

        $result['whereClause'][] = array(
            'OR' => $d
        );
    }

    /**
     * @param array $result
     */
    protected function accessOnlyTeam(&$result)
    {
        if (!$this->hasTeamsField()) {
            return;
        }

        $this->setDistinct(true, $result);
        $this->addLeftJoin(['teams', 'teamsAccess'], $result);

        if ($this->hasAssignedUsersField()) {
            $this->addLeftJoin(['assignedUsers', 'assignedUsersAccess'], $result);
            $result['whereClause'][] = array(
                'OR' => array(
                    'teamsAccess.id'         => $this->getUser()->getLinkMultipleIdList('teams'),
                    'assignedUsersAccess.id' => $this->getUser()->id
                )
            );
            return;
        }

        $d = array(
            'teamsAccess.id' => $this->getUser()->getLinkMultipleIdList('teams')
        );

        if ($this->hasOwnerUserField()) {
            $d['ownerUserId'] = $this->getUser()->id;
        }

        if ($this->hasAssignedUserField()) {
            $d['assignedUserId'] = $this->getUser()->id;
        }

        if ($this->hasCreatedByField() && !$this->hasAssignedUserField() && !$this->hasOwnerUserField()) {
            $d['createdById'] = $this->getUser()->id;
        }

        $result['whereClause'][] = array(
            'OR' => $d
        );
    }

    /**
     * @return bool
     */
    protected function hasOwnerUserField()
    {
        if ($this->getMetadata()->get('scopes.' . $this->getEntityType() . '.hasOwner')) {
            return true;
        }
    }

    /**
     * @return bool
     */
    protected function hasAssignedUsersField()
    {
        if ($this->getMetadata()->get('scopes.' . $this->getEntityType() . '.hasAssignedUser')
            && $this->getSeed()->hasRelation('assignedUsers')
            && $this->getSeed()->hasAttribute('assignedUsersIds')) {
            return true;
        }
    }

    /**
     * @return bool
     */
    protected function hasAssignedUserField()
    {
        if ($this->getMetadata()->get('scopes.' . $this->getEntityType() . '.hasAssignedUser')) {
            return true;
        }
    }

    /**
     * OnlyActive filter
     *
     * @param array $result
     */
    protected function boolFilterOnlyActive(&$result)
    {
        $result['whereClause'][] = [
            'isActive' => true
        ];
    }
}
