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
 * Copyright (C) 2020 KenerSoft Service GmbH
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

use Espo\ORM\Entity;
use Espo\Core\Exceptions\Error;
use Cron\CronExpression;

/**
 * ScheduledJob service
 *
 * @author r.ratsun@zinitsolutions.com
 */
class ScheduledJob extends \Espo\Core\Templates\Services\Base
{
    /**
     * @param Entity $entity
     * @param        $data
     *
     * @throws Error
     */
    protected function beforeCreateEntity(Entity $entity, $data)
    {
        $this->isScheduledValid($entity);

        parent::beforeCreateEntity($entity, $data);
    }

    /**
     * @param Entity $entity
     * @param        $data
     *
     * @throws Error
     */
    protected function beforeUpdateEntity(Entity $entity, $data)
    {
        $this->isScheduledValid($entity);

        parent::beforeUpdateEntity($entity, $data);
    }

    /**
     * Is scheduled valid
     *
     * @param Entity $entity
     *
     * @return bool
     */
    protected function isScheduledValid(Entity $entity): bool
    {
        if (!empty($entity->get('scheduling'))) {
            try {
                $cronExpression = CronExpression::factory($entity->get('scheduling'));
            } catch (\Exception $e) {
                // prepare key
                $key = 'Wrong crontab configuration';

                // prepare message
                $message = $this
                    ->getInjection('language')
                    ->translate($key, 'exceptions', 'ScheduledJob');

                throw new Error($message);
            }
        }

        return true;
    }

    /**
     * Init
     */
    protected function init()
    {
        parent::init();

        $this->addDependency('language');
    }
}
