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

return array (    'defaultPermissions' =>
    array (
        'dir' => '0775',
        'file' => '0664',
        'user' => '',
        'group' => '',
    ),

    'permissionMap' => array(

        /** array('0664', '0775') */
        'writable' => array(
            'data',
            'custom',
        ),

        /** array('0644', '0755') */
        'readable' => array(
            'api',
            'application',
            'client',
            'vendor',
            'index.php',
            'cron.php',
            'rebuild.php',
            'main.html',
            'reset.html',
        ),
    ),
    'jobMaxPortion' => 15, /** Max number of jobs per one execution. */
    'jobPeriod' => 7800, /** Max execution time (in seconds) allocated for a sinle job. If exceeded then set to Failed.*/
    'jobPeriodForActiveProcess' => 36000, /** Max execution time (in seconds) allocated for a sinle job with active process. If exceeded then set to Failed.*/
    'jobRerunAttemptNumber' => 1, /** Number of attempts to re-run failed jobs. */
    'cronMinInterval' => 4, /** Min interval (in seconds) between two cron runs. */
    'crud' => array(
        'get' => 'read',
        'post' => 'create',
        'put' => 'update',
        'patch' => 'patch',
        'delete' => 'delete',
    ),
    'systemUser' => array(
        'id' => 'system',
        'userName' => 'system',
        'firstName' => '',
        'lastName' => 'System',
    ),
    'systemItems' =>
    array (
        'systemItems',
        'adminItems',
        'configPath',
        'cachePath',
        'database',
        'crud',
        'logger',
        'isInstalled',
        'defaultPermissions',
        'systemUser',
        'permissionMap',
        'permissionRules',
        'passwordSalt',
        'cryptKey',
        'restrictedMode',
        'userLimit',
        'portalUserLimit',
        'stylesheet',
        'userItems',
        'internalSmtpServer',
        'internalSmtpPort',
        'internalSmtpAuth',
        'internalSmtpUsername',
        'internalSmtpPassword',
        'internalSmtpSecurity',
        'internalOutboundEmailFromAddress'
    ),
    'adminItems' =>
    array (
        'devMode',
        'smtpServer',
        'smtpPort',
        'smtpAuth',
        'smtpSecurity',
        'smtpUsername',
        'smtpPassword',
        'jobMaxPortion',
        'jobPeriod',
        'jobRerunAttemptNumber',
        'cronMinInterval',
        'authenticationMethod',
        'adminPanelIframeUrl',
        'ldapHost',
        'ldapPort',
        'ldapSecurity',
        'ldapAuth',
        'ldapUsername',
        'ldapPassword',
        'ldapBindRequiresDn',
        'ldapBaseDn',
        'ldapUserLoginFilter',
        'ldapAccountCanonicalForm',
        'ldapAccountDomainName',
        'ldapAccountDomainNameShort',
        'ldapAccountFilterFormat',
        'ldapTryUsernameSplit',
        'ldapOptReferrals',
        'ldapPortalUserLdapAuth',
        'ldapCreateEspoUser',
        'ldapAccountDomainName',
        'ldapAccountDomainNameShort',
        'ldapUserNameAttribute',
        'ldapUserFirstNameAttribute',
        'ldapUserLastNameAttribute',
        'ldapUserTitleAttribute',
        'ldapUserEmailAddressAttribute',
        'ldapUserPhoneNumberAttribute',
        'ldapUserObjectClass',
        'maxEmailAccountCount',
        'massEmailMaxPerHourCount',
        'personalEmailMaxPortionSize',
        'inboundEmailMaxPortionSize',
        'authTokenLifetime',
        'authTokenMaxIdleTime',
        'ldapUserDefaultTeamId',
        'ldapUserDefaultTeamName',
        'ldapUserTeamsIds',
        'ldapUserTeamsNames',
        'ldapPortalUserPortalsIds',
        'ldapPortalUserPortalsNames',
        'ldapPortalUserRolesIds',
        'ldapPortalUserRolesNames',
        'cleanupJobPeriod',
        'cleanupActionHistoryPeriod',
        'adminNotifications',
        'adminNotificationsNewVersion',
        'adminNotificationsCronIsNotConfigured',
        'adminNotificationsNewExtensionVersion',
        'leadCaptureAllowOrigin'
    ),
    'userItems' =>
    array (
        'outboundEmailFromAddress',
        'outboundEmailFromName',
        'integrations',
        'googleMapsApiKey'
    ),
    'isInstalled' => false,
    'ldapUserNameAttribute' => 'sAMAccountName',
    'ldapUserFirstNameAttribute' => 'givenName',
    'ldapUserLastNameAttribute' => 'sn',
    'ldapUserTitleAttribute' => 'title',
    'ldapUserEmailAddressAttribute' => 'mail',
    'ldapUserPhoneNumberAttribute' => 'telephoneNumber',
    'ldapUserObjectClass' => 'person',
    'ldapPortalUserLdapAuth' => false,
);
