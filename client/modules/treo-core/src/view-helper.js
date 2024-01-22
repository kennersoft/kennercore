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

Espo.define('treo-core:view-helper', 'view-helper', ViewHelper => {

    const registerHandlebarsHelpers = ViewHelper.prototype._registerHandlebarsHelpers;

    _.extend(ViewHelper.prototype, {

        _registerHandlebarsHelpers() {
            registerHandlebarsHelpers.call(this);

            Handlebars.unregisterHelper('complexText');
            Handlebars.registerHelper('complexText', text => {
                text = text || ''

                text = text.replace(this.urlRegex, '$1[$2]($2)');

                text = Handlebars.Utils.escapeExpression(text).replace(/&gt;+/g, '>');

                this.mdBeforeList.forEach(item => {
                    text = text.replace(item.regex, item.value);
                });

                text = marked(text);

                text = text.replace('[#see-more-text]', ' <a href="javascript:" data-action="seeMoreText">' + this.language.translate('See more')) + '</a>';
                text = text.replace('[#see-less-text]', ' <a href="javascript:" data-action="seeLessText">' + this.language.translate('See less')) + '</a>';
                return new Handlebars.SafeString(text);
            });
        }

    });

    return ViewHelper;

});