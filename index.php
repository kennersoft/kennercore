<?php
/**
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2018 Zinit Solutions GmbH
 * Website: http://www.treopim.com
 *
 * TreoPIM as well as EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TreoPIM as well as EspoCRM is distributed in the hope that it will be useful,
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
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word
 * and "TreoPIM" word.
 */

use Treo\Core\Application as App;
use Treo\Core\Portal\Application as PortalApp;

include "bootstrap.php";

// define gloabal variables
define('CORE_PATH', __DIR__);

// create  app
$app = new App();

if (!$app->isInstalled()) {
    $app->runInstaller();
    exit;
}

if (!empty($_GET['entryPoint'])) {
    $app->runEntryPoint($_GET['entryPoint']);
    exit;
}

if (!empty($id = PortalApp::getCallingPortalId())) {
    // create portal app
    $app = new PortalApp($id);
} elseif (!empty($uri = $_SERVER['REQUEST_URI']) && $uri != '/') {
    // if images path than call showImage
    if (preg_match_all('/^\/images\/(.*)\.(jpg|png|gif)$/', explode("?", $uri)[0], $matches)) {
        $app->runEntryPoint('TreoImage', ['id' => $matches[1][0], 'mimeType' => $matches[2][0]]);
    }

    // show 404
    header("HTTP/1.0 404 Not Found");
    exit;
}

$app->runClient();
