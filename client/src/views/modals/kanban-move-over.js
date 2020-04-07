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

Espo.define('views/modals/kanban-move-over', 'views/modal', function (Dep) {

    return Dep.extend({

        template: 'modals/kanban-move-over',

        data: function () {
            return {
                optionDataList: this.optionDataList
            };
        },

        events: {
            'click [data-action="move"]': function (e) {
                var value = $(e.currentTarget).data('value');
                this.moveTo(value);
            }
        },

        setup: function () {
            this.scope = this.model.name;
            var iconHtml = this.getHelper().getScopeColorIconHtml(this.scope);

            this.statusField = this.options.statusField;

            this.header = '';
            this.header += this.getLanguage().translate(this.scope, 'scopeNames');
            if (this.model.get('name')) {
                this.header += ' &raquo; ' + Handlebars.Utils.escapeExpression(this.model.get('name'));
            }
            this.header = iconHtml + this.header;

            this.buttonList = [
                {
                    name: 'cancel',
                    label: 'Cancel'
                }
            ];

            this.optionDataList = [];

            (this.getMetadata().get(['entityDefs', this.scope, 'fields', this.statusField, 'options']) || []).forEach(function (item) {
                this.optionDataList.push({
                    value: item,
                    label: this.getLanguage().translateOption(item, this.statusField, this.scope)
                });
            }, this);
        },

        moveTo: function (status) {
            var attributes = {};
            attributes[this.statusField] = status;
            this.model.save(attributes, {patch: true}).then(function () {
                Espo.Ui.success(this.translate('Done'));
            }.bind(this));
            this.close();
        }

    });
});
