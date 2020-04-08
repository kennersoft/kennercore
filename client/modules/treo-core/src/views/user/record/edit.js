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

Espo.define('treo-core:views/user/record/edit', ['views/user/record/edit', 'views/user/record/detail', 'views/record/edit'], function (Dep, Detail, MainDep) {

    return Dep.extend({

        setup: function () {
            MainDep.prototype.setup.call(this);

            this.setupNonAdminFieldsAccess();

            if (this.model.id == this.getUser().id) {
                this.listenTo(this.model, 'after:save', function () {
                    this.getUser().set(this.model.toJSON());
                }, this);
            }

            this.hideField('sendAccessInfo');

            var passwordChanged = false;

            this.listenToOnce(this.model, 'change:password', function (model) {
                passwordChanged = true;
                if (model.get('emailAddress')) {
                    this.showField('sendAccessInfo');
                    this.model.set('sendAccessInfo', true);
                }
            }, this);

            this.listenTo(this.model, 'change:emailAddress', function (model) {
                if (passwordChanged) {
                    if (model.get('emailAddress')) {
                        this.showField('sendAccessInfo');
                        this.model.set('sendAccessInfo', true);
                    } else {
                        this.hideField('sendAccessInfo');
                        this.model.set('sendAccessInfo', false);
                    }
                }
            }, this);

            Detail.prototype.setupFieldAppearance.call(this);

            this.hideField('passwordPreview');
            this.listenTo(this.model, 'change:passwordPreview', function (model, value) {
                value = value || '';
                if (value.length) {
                    this.showField('passwordPreview');
                } else {
                    this.hideField('passwordPreview');
                }
            }, this);
        },

    });
});
