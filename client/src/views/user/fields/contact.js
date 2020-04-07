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

Espo.define('views/user/fields/contact', 'views/fields/link', function (Dep) {

    return Dep.extend({

        select: function (model) {
            Dep.prototype.select.call(this, model);

            var attributes = {};

            if (model.get('accountId')) {
                var names = {};
                names[model.get('accountId')] = model.get('accountName');
                attributes.accountsIds = [model.get('accountId')],
                attributes.accountsNames = names;

            }

            attributes.firstName = model.get('firstName');
            attributes.lastName = model.get('lastName');
            attributes.salutationName = model.get('salutationName');

            attributes.emailAddress = model.get('emailAddress');
            attributes.emailAddressData = model.get('emailAddressData');

            attributes.phoneNumber = model.get('phoneNumber');
            attributes.phoneNumberData = model.get('phoneNumberData');

            if (this.model.isNew() && !this.model.get('userName') && attributes.emailAddress) {
                attributes.userName = attributes.emailAddress;
            }

            this.model.set(attributes);
        }

    });

});
