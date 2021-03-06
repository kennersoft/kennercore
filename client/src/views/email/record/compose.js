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

Espo.define('views/email/record/compose', ['views/record/edit', 'views/email/record/detail'], function (Dep, Detail) {

    return Dep.extend({

        isWide: true,

        sideView: false,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.initialBody = null;
            this.initialIsHtml = null;

            if (!this.model.get('isHtml') && this.getPreferences().get('emailReplyForceHtml')) {
                var body = (this.model.get('body') || '').replace(/\n/g, '<br>');
                this.model.set('body', body);
                this.model.set('isHtml', true);
            }

            if (this.model.get('body')) {
                this.initialBody = this.model.get('body');
                this.initialIsHtml = this.model.get('isHtml');
            }

            if (!this.options.signatureDisabled && this.hasSignature()) {
                var body = this.prependSignature(this.model.get('body') || '', this.model.get('isHtml'));
                this.model.set('body', body);
            }

            this.isBodyChanged = false;

            this.listenTo(this.model, 'change:body', function () {
                this.isBodyChanged = true;
            }, this);

            if (!this.options.removeAttachmentsOnSelectTemplate) {
                this.initialAttachmentsIds = this.model.get('attachmentsIds') || [];
                this.initialAttachmentsNames = this.model.get('attachmentsNames') || {};
            }

            this.listenTo(this.model, 'insert-template', function (data) {
                var body = this.model.get('body') || '';

                var bodyPlain = body.replace(/<br\s*\/?>/mg, '');
                bodyPlain = bodyPlain.replace(/<\/p\s*\/?>/mg, '');
                bodyPlain = bodyPlain.replace(/ /g, '');
                bodyPlain = bodyPlain.replace(/\n/g, '');

                var $div = $('<div>').html(bodyPlain);
                bodyPlain = $div.text();

                if (
                    bodyPlain !== '' &&
                    this.isBodyChanged
                ) {
                    this.confirm({
                        message: this.translate('confirmInsertTemplate', 'messages', 'Email'),
                        confirmText: this.translate('Yes')
                    }, function () {
                        this.insertTemplate(data);
                    }, this);
                } else {
                    this.insertTemplate(data);
                }

            }, this);

            if (this.options.selectTemplateDisabled) {
                this.hideField('selectTemplate');
            }
        },

        insertTemplate: function (data) {
            var body = data.body;
            if (this.hasSignature()) {
                body = this.appendSignature(body || '', data.isHtml);
            }

            if (this.initialBody) {
                var initialBody = this.initialBody;
                if (data.isHtml !== this.initialIsHtml) {
                    if (data.isHtml) {
                        initialBody = this.plainToHtml(initialBody);
                    } else {
                        initialBody = this.htmlToPlain(initialBody);
                    }
                }

                body += initialBody;
            }

            this.model.set('isHtml', data.isHtml);

            if (data.subject) {
                this.model.set('name', data.subject);
            }

            this.model.set('body', '');
            this.model.set('body', body);

            if (!this.options.removeAttachmentsOnSelectTemplate) {
                this.initialAttachmentsIds.forEach(function (id) {
                    if (data.attachmentsIds) {
                        data.attachmentsIds.push(id);
                    }
                    if (data.attachmentsNames) {
                        data.attachmentsNames[id] = this.initialAttachmentsNames[id] || id;
                    }
                }, this);
            }

            this.model.set({
                attachmentsIds: data.attachmentsIds,
                attachmentsNames: data.attachmentsNames
            });

            this.isBodyChanged = false;
        },

        prependSignature: function (body, isHtml) {
            if (isHtml) {
                var signature = this.getSignature();
                if (body) {
                    signature += '<br>';
                }
                body = '<p><br></p><br>' + signature + body;
            } else {
                var signature = this.getPlainTextSignature();
                if (body) {
                    signature += '\n';
                }
                body = '\n\n' + signature + body;
            }
            return body;
        },

        appendSignature: function (body, isHtml) {
            if (isHtml) {
                var signature = this.getSignature();
                body = body + '<p><br></p>' + signature;
            } else {
                var signature = this.getPlainTextSignature();
                body = body + '\n\n' + signature;
            }
            return body;
        },

        hasSignature: function () {
            return !!this.getPreferences().get('signature');
        },

        getSignature: function () {
            return this.getPreferences().get('signature') || '';
        },

        getPlainTextSignature: function () {
            var value = this.getSignature().replace(/<br\s*\/?>/mg, '\n');
            value = $('<div>').html(value).text();
            return value;
        },

        send: function () {
            Detail.prototype.send.call(this);
        },

        saveDraft: function () {
            var model = this.model;
            model.set('status', 'Draft');

            this.save();
        },

        htmlToPlain: function (text) {
            text = text || '';
            var value = text.replace(/<br\s*\/?>/mg, '\n');

            value = value.replace(/<\/p\s*\/?>/mg, '\n\n');

            var $div = $('<div>').html(value);
            $div.find('style').remove();
            $div.find('link[ref="stylesheet"]').remove();

            value =  $div.text();

            return value;
        },

        plainToHtml: function (html) {
            html = html || '';
            var value = html.replace(/\n/g, '<br>');
            return value;
        }

    });
});
