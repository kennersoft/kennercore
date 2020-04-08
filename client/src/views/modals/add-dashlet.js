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


Espo.define('views/modals/add-dashlet', 'views/modal', function (Dep) {

    return Dep.extend({

        cssName: 'add-dashlet',

        template: 'modals/add-dashlet',

        fitHeight: true,

        data: function () {
            return {
                dashletList: this.dashletList,
            };
        },

        events: {
            'click button.add': function (e) {
                var name = $(e.currentTarget).data('name');
                this.trigger('add', name);
                this.close();
            },
        },

        buttonList: [
            {
                name: 'cancel',
                label: 'Cancel'
            }
        ],

        setup: function () {
            this.header = this.translate('Add Dashlet');

            var dashletList = Object.keys(this.getMetadata().get('dashlets') || {}).sort(function (v1, v2) {
                return this.translate(v1, 'dashlets').localeCompare(this.translate(v2, 'dashlets'));
            }.bind(this));

            this.dashletList = [];

            dashletList.forEach(function (item) {
                var aclScope = this.getMetadata().get('dashlets.' + item + '.aclScope') || null;
                if (aclScope) {
                    if (!this.getAcl().check(aclScope)) {
                        return;
                    }
                }
                var accessDataList = this.getMetadata().get(['dashlets', item, 'accessDataList']) || null;
                if (accessDataList) {
                    if (!Espo.Utils.checkAccessDataList(accessDataList, this.getAcl(), this.getUser())) {
                        return false;
                    }
                }
                this.dashletList.push(item);
            }, this);
        },
    });
});


