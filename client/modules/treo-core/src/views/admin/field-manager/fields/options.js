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

Espo.define('treo-core:views/admin/field-manager/fields/options', ['class-replace!treo-core:views/admin/field-manager/fields/options', 'views/fields/array'], function (Dep, Arr) {

    return Dep.extend({

        getItemHtml(value) {
            let valueSanitized = this.getHelper().stripTags(value);
            let translatedValue = this.translatedOptions[value] || valueSanitized;

            translatedValue = translatedValue.replace(/"/g, '&quot;').replace(/\\/g, '&bsol;');

            let valueInternal = valueSanitized.replace(/"/g, '-quote-').replace(/\\/g, '-backslash-');

            return `
                <div class="list-group-item link-with-role form-inline" data-value="${valueInternal}">
                    <div class="pull-left" style="width: 92%; display: inline-block;">
                        <input name="translatedValue" data-value="${valueInternal}" class="role form-control input-sm pull-right" value="${translatedValue}">
                        <div>${valueSanitized}</div>
                    </div>
                    <div style="width: 8%; display: inline-block;">
                        <a href="javascript:" class="pull-right" data-value="${valueInternal}" data-action="removeValue"><span class="fas fa-times"></a>
                    </div>
                    <br style="clear: both;" />
                </div>`;
        },

        fetch() {
            let data = Arr.prototype.fetch.call(this);

            if (!data[this.name].length) {
                data[this.name] = false;
                data.translatedOptions = {};
                return data;
            }

            data.translatedOptions = {};
            (data[this.name] || []).forEach(value => {
                let valueSanitized = this.getHelper().stripTags(value);
                let valueInternal = valueSanitized.replace(/"/g, '-quote-').replace(/\\/g, '-backslash-');
                let translatedValue = this.$el.find('input[name="translatedValue"][data-value="'+valueInternal+'"]').val() || value;
                data.translatedOptions[value] = translatedValue.toString();
            });

            return data;
        },

        fetchFromDom() {
            var selected = [];
            this.$el.find('.list-group .list-group-item').each((i, el) => {
                var value = $(el).data('value').toString();
                value = value.replace(/-quote-/g, '"').replace(/-backslash-/g, '\\');
                selected.push(value);
            });
            this.selected = selected;
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.mode === 'edit') {
                if (!this.params.options) {
                    this.$select.off('keypress');
                    this.$select.on('keypress', function (e) {
                        if (e.keyCode === 13) {
                            let value = this.$select.val().toString();
                            if (this.noEmptyString) {
                                if (value === '') {
                                    return;
                                }
                            }
                            value = value.trim();
                            this.addValue(value);
                            this.$select.val('');
                        }
                    }.bind(this));
                }
            }
        }

    });

});
