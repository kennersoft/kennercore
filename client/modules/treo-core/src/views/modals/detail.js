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

Espo.define('treo-core:views/modals/detail', 'class-replace!treo-core:views/modals/detail',
    Dep => Dep.extend({

        createRecordView: function (callback) {
            var model = this.model;
            var scope = this.getScope();

            this.header = '';
            var iconHtml = this.getHelper().getScopeColorIconHtml(this.scope);

            this.header += this.getLanguage().translate(scope, 'scopeNames');

            if (model.get('name')) {
                this.header += ' &raquo; ' + Handlebars.Utils.escapeExpression(model.get('name'));
            }
            if (!this.fullFormDisabled) {
                this.header = '<a href="#' + scope + '/view/' + this.id+'" class="action" title="'+this.translate('Full Form')+'" data-action="fullForm">' + this.header + '</a>';
            }

            this.header = iconHtml + this.header;

            if (!this.editDisabled) {
                var editAccess = this.getAcl().check(model, 'edit', true);
                if (editAccess) {
                    this.showButton('edit');
                } else {
                    this.hideButton('edit');
                    if (editAccess === null) {
                        this.listenToOnce(model, 'sync', function() {
                            if (this.getAcl().check(model, 'edit')) {
                                this.showButton('edit');
                            }
                        }, this);
                    }
                }
            }

            if (!this.removeDisabled) {
                var removeAccess = this.getAcl().check(model, 'delete', true);
                if (removeAccess) {
                    this.showButton('remove');
                } else {
                    this.hideButton('remove');
                    if (removeAccess === null) {
                        this.listenToOnce(model, 'sync', function() {
                            if (this.getAcl().check(model, 'delete')) {
                                this.showButton('remove');
                            }
                        }, this);
                    }
                }
            }

            var viewName =
                this.detailViewName ||
                this.detailView ||
                this.getMetadata().get(['clientDefs', model.name, 'recordViews', 'detailSmall']) ||
                this.getMetadata().get(['clientDefs', model.name, 'recordViews', 'detailQuick']) ||
                'views/record/detail-small';
            var options = {
                model: model,
                el: this.containerSelector + ' .record-container',
                type: 'detailSmall',
                layoutName: this.layoutName || 'detailSmall',
                columnCount: this.columnCount,
                buttonsDisabled: true,
                inlineEditDisabled: true,
                sideDisabled: this.sideDisabled,
                bottomDisabled: this.bottomDisabled,
                exit: function () {}
            };
            this.handleRecordViewOptions(options);
            this.createView('record', viewName, options, callback);
        },

        handleRecordViewOptions: function (options) {},

    })
);