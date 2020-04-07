/*
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

Espo.define('views/dashlets/records', 'views/dashlets/abstract/record-list', function (Dep) {

    return Dep.extend({

        name: 'Records',

        scope: null,

        rowActionsView: 'views/record/row-actions/view-and-edit',

        listView: 'views/email/record/list-expanded',

        init: function () {
            Dep.prototype.init.call(this);
            this.scope = this.getOption('entityType');
        },

        getSearchData: function () {
            var data = {
                primary: this.getOption('primaryFilter')
            };

            var bool = {};
            (this.getOption('boolFilterList') || []).forEach(function (item) {
                bool[item] = true;
            }, this);

            data.bool = bool;

            return data;
        },

        setupActionList: function () {
            var scope = this.getOption('entityType');
            if (scope && this.getAcl().checkScope(scope, 'create')) {
                this.actionList.unshift({
                    name: 'create',
                    html: this.translate('Create ' + scope, 'labels', scope),
                    iconHtml: '<span class="fas fa-plus"></span>',
                    url: '#' + scope + '/create'
                });
            }
        },

    });
});

