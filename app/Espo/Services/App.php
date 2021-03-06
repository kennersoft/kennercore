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

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\NotFound;

use \Espo\Core\Utils\Util;

class App extends \Espo\Core\Services\Base
{
    protected function init()
    {
        $this->addDependency('preferences');
        $this->addDependency('acl');
        $this->addDependency('container');
        $this->addDependency('entityManager');
        $this->addDependency('metadata');
        $this->addDependency('selectManagerFactory');
    }

    protected function getPreferences()
    {
        return $this->getInjection('preferences');
    }

    protected function getAcl()
    {
        return $this->getInjection('acl');
    }

    protected function getEntityManager()
    {
        return $this->getInjection('entityManager');
    }

    protected function getMetadata()
    {
        return $this->getInjection('metadata');
    }

    public function getUserData()
    {
        $preferencesData = $this->getPreferences()->getValueMap();
        unset($preferencesData->smtpPassword);

        $user = $this->getUser();
        if (!$user->has('teamsIds')) {
            $user->loadLinkMultipleField('teams');
        }
        if ($user->get('isPortalUser')) {
            $user->loadAccountField();
            $user->loadLinkMultipleField('accounts');
        }

        $userData = $user->getValueMap();

        $userData->emailAddressList = $this->getEmailAddressList();

        $settings = (object)[];
        foreach ($this->getConfig()->get('userItems') as $item) {
            $settings->$item = $this->getConfig()->get($item);
        }

        if ($this->getUser()->isAdmin()) {
            foreach ($this->getConfig()->get('adminItems') as $item) {
                if ($this->getConfig()->has($item)) {
                    $settings->$item = $this->getConfig()->get($item);
                }
            }
        }

        $settingsFieldDefs = $this->getInjection('metadata')->get('entityDefs.Settings.fields', []);
        foreach ($settingsFieldDefs as $field => $d) {
            if ($d['type'] === 'password') {
                unset($settings->$field);
            }
        }

        unset($userData->authTokenId);
        unset($userData->password);

        $language = \Espo\Core\Utils\Language::detectLanguage($this->getConfig(), $this->getPreferences());

        return [
            'user' => $userData,
            'acl' => $this->getAcl()->getMap(),
            'preferences' => $preferencesData,
            'token' => $this->getUser()->get('token'),
            'settings' => $settings,
            'language' => $language,
            'appParams' => [
                'maxUploadSize' => $this->getMaxUploadSize() / 1024.0 / 1024.0,
                'templateEntityTypeList' => $this->getTemplateEntityTypeList()
            ]
        ];
    }

    protected function getEmailAddressList() {
        $user = $this->getUser();

        $emailAddressList = [];
        foreach ($user->get('emailAddresses') as $emailAddress) {
            if ($emailAddress->get('invalid')) continue;
            if ($user->get('emailAddrses') === $emailAddress->get('name')) continue;
            $emailAddressList[] = $emailAddress->get('name');
        }
        if ($user->get('emailAddrses')) {
            array_unshift($emailAddressList, $user->get('emailAddrses'));
        }

        $entityManager = $this->getEntityManager();

        $teamIdList = $user->getLinkMultipleIdList('teams');
        $groupEmailAccountPermission = $this->getAcl()->get('groupEmailAccountPermission');
        if ($groupEmailAccountPermission && $groupEmailAccountPermission !== 'no') {
            if ($groupEmailAccountPermission === 'team') {
                if (count($teamIdList)) {
                    $selectParams = [
                        'whereClause' => [
                            'status' => 'Active',
                            'useSmtp' => true,
                            'smtpIsShared' => true,
                            'teamsMiddle.teamId' => $teamIdList
                        ],
                        'joins' => ['teams'],
                        'distinct' => true
                    ];
                    $inboundEmailList = $entityManager->getRepository('InboundEmail')->find($selectParams);
                    foreach ($inboundEmailList as $inboundEmail) {
                        if (!$inboundEmail->get('emailAddress')) continue;
                        $emailAddressList[] = $inboundEmail->get('emailAddress');
                    }
                }
            } else if ($groupEmailAccountPermission === 'all') {
                $selectParams = [
                    'whereClause' => [
                        'status' => 'Active',
                        'useSmtp' => true,
                        'smtpIsShared' => true
                    ]
                ];
                $inboundEmailList = $entityManager->getRepository('InboundEmail')->find($selectParams);
                foreach ($inboundEmailList as $inboundEmail) {
                    if (!$inboundEmail->get('emailAddress')) continue;
                    $emailAddressList[] = $inboundEmail->get('emailAddress');
                }
            }
        }

        return $emailAddressList;
    }

    private function getMaxUploadSize()
    {
        $maxSize = 0;

        $postMaxSize = $this->convertPHPSizeToBytes(ini_get('post_max_size'));
        if ($postMaxSize > 0) {
            $maxSize = $postMaxSize;
        }
        $attachmentUploadMaxSize = $this->getConfig()->get('attachmentUploadMaxSize');
        if ($attachmentUploadMaxSize && (!$maxSize || $attachmentUploadMaxSize < $maxSize)) {
            $maxSize = $attachmentUploadMaxSize;
        }

        return $maxSize;
    }

    private function convertPHPSizeToBytes($size)
    {
        if (is_numeric($size)) return $size;

        $suffix = substr($size, -1);
        $value = substr($size, 0, -1);
        switch(strtoupper($suffix)) {
            case 'P':
                $value *= 1024;
            case 'T':
                $value *= 1024;
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024;
            case 'K':
                $value *= 1024;
                break;
            }
        return $value;
    }

    protected function getTemplateEntityTypeList()
    {
        if (!$this->getAcl()->checkScope('Template')) {
            return [];
        }

        $list = [];

        $selectManager = $this->getInjection('selectManagerFactory')->create('Template');

        $selectParams = $selectManager->getEmptySelectParams();
        $selectManager->applyAccess($selectParams);

        $templateList = $this->getEntityManager()->getRepository('Template')
            ->select(['entityType'])
            ->groupBy(['entityType'])
            ->find($selectParams);

        foreach ($templateList as $template) {
            $list[] = $template->get('entityType');
        }

        return $list;
    }

    public function jobClearCache()
    {
        $this->getInjection('container')->get('dataManager')->clearCache();
    }

    public function jobRebuild()
    {
        $this->getInjection('container')->get('dataManager')->rebuild();
    }

    // TODO remove in 5.5.0
    public function jobPopulatePhoneNumberNumeric()
    {
        $numberList = $this->getEntityManager()->getRepository('PhoneNumber')->find();
        foreach ($numberList as $number) {
            $this->getEntityManager()->saveEntity($number);
        }
    }

    // TODO remove in 5.5.0
    public function jobPopulateArrayValues()
    {
        $scopeList = array_keys($this->getMetadata()->get(['scopes']));

        $sql = "DELETE FROM array_value";
        $this->getEntityManager()->getPdo()->query($sql);

        foreach ($scopeList as $scope) {
            if (!$this->getMetadata()->get(['scopes', $scope, 'entity'])) continue;
            if ($this->getMetadata()->get(['scopes', $scope, 'disabled'])) continue;

            $seed = $this->getEntityManager()->getEntity($scope);
            if (!$seed) continue;

            $attributeList = [];

            foreach ($seed->getAttributes() as $attribute => $defs) {
                if (!isset($defs['type']) || $defs['type'] !== \Espo\ORM\Entity::JSON_ARRAY) continue;
                if (!$seed->getAttributeParam($attribute, 'storeArrayValues')) continue;
                if ($seed->getAttributeParam($attribute, 'notStorable')) continue;
                $attributeList[] = $attribute;
            }
            $select = ['id'];
            $orGroup = [];
            foreach ($attributeList as $attribute) {
                $select[] = $attribute;
                $orGroup[$attribute . '!='] = null;
            }

            $sql = $this->getEntityManager()->getQuery()->createSelectQuery($scope, [
                'select' => $select,
                'whereClause' => [
                    'OR' => $orGroup
                ]
            ]);
            $sth = $this->getEntityManager()->getPdo()->prepare($sql);
            $sth->execute();

            while ($dataRow = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $entity = $this->getEntityManager()->getEntityFactory()->create($scope);
                $entity->set($dataRow);
                $entity->setAsFetched();

                foreach ($attributeList as $attribute) {
                    $this->getEntityManager()->getRepository('ArrayValue')->storeEntityAttribute($entity, $attribute, true);
                }
            }
        }
    }

    // TODO remove in 5.5.0
    public function jobPopulateNotesTeamUser()
    {
        $aclManager = $this->getInjection('container')->get('aclManager');

        $sql = $this->getEntityManager()->getQuery()->createSelectQuery('Note', [
            'whereClause' => [
                'parentId!=' => null,
                'type=' => ['Relate', 'CreateRelated', 'EmailReceived', 'EmailSent', 'Assign', 'Create'],
            ],
            'limit' => 100000,
            'orderBy' => [['number', 'DESC']]
        ]);
        $sth = $this->getEntityManager()->getPdo()->prepare($sql);
        $sth->execute();

        $i = 0;
        while ($dataRow = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $i++;
            $note = $this->getEntityManager()->getEntityFactory()->create('Note');
            $note->set($dataRow);
            $note->setAsFetched();

            if ($note->get('relatedId') && $note->get('relatedType')) {
                $targetType = $note->get('relatedType');
                $targetId = $note->get('relatedId');
            } else if ($note->get('parentId') && $note->get('parentType')) {
                $targetType = $note->get('parentType');
                $targetId = $note->get('parentId');
            } else {
                continue;
            }

            if (!$this->getEntityManager()->hasRepository($targetType)) continue;

            try {
                $entity = $this->getEntityManager()->getEntity($targetType, $targetId);
                if (!$entity) continue;
                $ownerUserIdAttribute = $aclManager->getImplementation($targetType)->getOwnerUserIdAttribute($entity);
                $toSave = false;
                if ($ownerUserIdAttribute) {
                    if ($entity->getAttributeParam($ownerUserIdAttribute, 'isLinkMultipleIdList')) {
                        $link = $entity->getAttributeParam($ownerUserIdAttribute, 'relation');
                        $userIdList = $entity->getLinkMultipleIdList($link);
                    } else {
                        $userId = $entity->get($ownerUserIdAttribute);
                        if ($userId) {
                            $userIdList = [$userId];
                        } else {
                            $userIdList = [];
                        }
                    }
                    if (!empty($userIdList)) {
                        $note->set('usersIds', $userIdList);
                        $toSave = true;
                    }
                }
                if ($entity->hasLinkMultipleField('teams')) {
                    $teamIdList = $entity->getLinkMultipleIdList('teams');
                    if (!empty($teamIdList)) {
                        $note->set('teamsIds', $teamIdList);
                        $toSave = true;
                    }
                }
                if ($toSave) {
                    $this->getEntityManager()->saveEntity($note);
                }
            } catch (\Exception $e) {}
        }
    }
}
