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

namespace Espo\Core\Utils;
use Espo\Core\Exceptions\Error;

class PasswordHash
{
    private $config;

    /**
     * Salt format of SHA-512
     *
     * @var string
     */
    private $saltFormat = '$6${0}$';

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Get hash of a pawword
     *
     * @param  string $password
     * @return string
     */
    public function hash($password, $useMd5 = true)
    {
        $salt = $this->getSalt();

        if ($useMd5) {
            $password = md5($password);
        }

        $hash = crypt($password, $salt);
        $hash = str_replace($salt, '', $hash);

        return $hash;
    }

    /**
     * Get a salt from config and normalize it
     *
     * @return string
     */
    protected function getSalt()
    {
        $salt = $this->getConfig()->get('passwordSalt');
        if (!isset($salt)) {
            throw new Error('Option "passwordSalt" does not exist in config.php');
        }

        $salt = $this->normalizeSalt($salt);

        return $salt;
    }

    /**
     * Convert salt in format in accordance to $saltFormat
     *
     * @param  string $salt
     * @return string
     */
    protected function normalizeSalt($salt)
    {
        return str_replace("{0}", $salt, $this->saltFormat);
    }

    /**
     * Generate a new salt
     *
     * @return string
     */
    public function generateSalt()
    {
        return substr(md5(uniqid()), 0, 16);
    }
}