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

Espo.define('treo-core:controller', 'class-replace!treo-core:controller', function (Controller) {

    _.extend(Controller.prototype, {

        userHelper(main) {
            if (!this.getPreferences().get('disableUserHelper')) {
                main.factory.create('treo-core:views/user-helper', {
                    scope: this.name
                }, view => view.initHelpers(this));
            }
        },

        main: function (view, options, prevCallback, useStored, storedKey) {
            var callback = function (main) {
                main.once('after:render', () => this.userHelper(main));

                if (prevCallback) {
                    prevCallback(main);
                } else {
                    main.render();
                }
            };

            var isCanceled = false;
            this.listenToOnce(this.baseController, 'action', function () {
                isCanceled = true;
            }, this);

            var view = view || 'views/base';
            var master = this.master(function (master) {
                if (isCanceled) return;

                master.showLoadingNotification();
                options = options || {};
                options.el = '#main';

                var process = function (main) {
                    if (isCanceled) return;

                    if (storedKey) {
                        this.storeMainView(storedKey, main);
                    }
                    main.once('render', function () {
                        main.updatePageTitle();
                        master.hideLoadingNotification();
                    });

                    main.listenToOnce(this.baseController, 'action', function () {
                        main.cancelRender();
                        isCanceled = true;
                    }, this);

                    if (master.currentViewKey) {
                        this.set('storedScrollTop-' + master.currentViewKey, $(window).scrollTop());
                        if (this.hasStoredMainView(master.currentViewKey)) {
                            master.unchainView('main');
                        }
                    }
                    master.currentViewKey = storedKey;
                    master.setView('main', main);

                    main.once('after:render', function () {
                        if (useStored && this.has('storedScrollTop-' + storedKey)) {
                            $(window).scrollTop(this.get('storedScrollTop-' + storedKey));
                        } else {
                            $(window).scrollTop(0);
                        }
                    }.bind(this));

                    if (isCanceled) return;

                    if (callback) {
                        callback.call(this, main);
                    } else {
                        main.render();
                    }
                }.bind(this);

                if (useStored) {
                    if (this.hasStoredMainView(storedKey)) {
                        var main = this.getStoredMainView(storedKey);

                        if (!main.lastUrl || main.lastUrl === this.getRouter().getCurrentUrl()) {
                            process(main);
                            if (main && typeof main.applyRoutingParams === 'function') {
                                main.applyRoutingParams(options.params || {});
                            }
                            return;
                        }
                    }
                }
                this.viewFactory.create(view, options, process);
            }.bind(this));
        }

    });

    return Controller;
});