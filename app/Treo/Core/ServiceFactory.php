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

namespace Treo\Core;

use Espo\Core\Exceptions\Error;
use Espo\Core\Interfaces\Injectable;
use Treo\Core\Interfaces\ServiceInterface;

/**
 * ServiceFactory class
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class ServiceFactory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $classNames;

    /**
     * ServiceFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->classNames = $this->container->get('metadata')->get(['app', 'services'], []);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function checkExists(string $name): bool
    {
        try {
            $className = $this->getClassName($name);
        } catch (Error $e) {
            $className = null;
        }

        return !empty($className);
    }

    /**
     * @param string $name
     *
     * @return ServiceInterface
     * @throws Error
     */
    public function create(string $name): ServiceInterface
    {
        if (!isset($this->services[$name])) {
            /** @var string $className */
            $className = $this->getClassName($name);

            // create service
            $service = new $className();

            if (!$service instanceof ServiceInterface) {
                throw new Error("Service '$name' doesn't support");
            }

            if ($service instanceof Injectable) {
                foreach ($service->getDependencyList() as $name) {
                    $service->inject($name, $this->container->get($name));
                }
            }
            if ($service instanceof \Treo\Services\AbstractService) {
                $service->setContainer($this->container);
            }

            $this->services[$name] = $service;
        }

        return $this->services[$name];
    }

    /**
     * @param string $name
     *
     * @return string
     * @throws Error
     */
    protected function getClassName(string $name): string
    {
        if (!isset($this->classNames[$name])) {
            /** @var string $module */
            $module = $this->container->get('metadata')->get(['scopes', $name, 'module'], 'Espo');

            // prepare module name for Treo services
            if ($module == 'TreoCore') {
                $module = 'Treo';
            }
            // prepare module name for Custom services
            if ($module == 'Custom') {
                $module = 'Espo\\Custom';
            }

            $this->classNames[$name] = "\\$module\\Services\\$name";
        }

        if (!class_exists($this->classNames[$name])) {
            throw new Error("Service '$name' was not found");
        }

        return $this->classNames[$name];
    }
}
