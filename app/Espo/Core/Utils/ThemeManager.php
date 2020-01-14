<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
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
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Core\Utils;

use Espo\Entities\Preferences;

/**
 * Class ThemeManager
 * @package Espo\Core\Utils
 */
class ThemeManager
{
    protected $config;

    protected $metadata;

    protected $preferences;

    protected $defaultName = 'Espo';

    private $defaultStylesheet = 'Espo';

    /**
     * ThemeManager constructor.
     * @param Config $config
     * @param Metadata $metadata
     * @param Preferences|null $preferences
     */
    public function __construct(Config $config, Metadata $metadata, ?Preferences $preferences)
    {
        $this->config = $config;
        $this->metadata = $metadata;
        $this->preferences = $preferences;
    }

    /**
     * Get name theme
     *
     * @return string
     */
    public function getName()
    {
        return $this->preferences !== null && !empty($this->preferences->get('theme'))
            ? $this->preferences->get('theme')
            : $this->config->get('theme', $this->defaultName);
    }

    /**
     * Get stylesheet
     *
     * @return string
     */
    public function getStylesheet()
    {
        return $this->metadata->get('themes.' . $this->getName() . '.stylesheet', 'client/css/espo.css');
    }
}


