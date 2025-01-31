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

Espo.define('treo-core:views/header', 'class-replace!treo-core:views/header', function (Dep) {

    return Dep.extend({

        template: 'treo-core:header',

        baseOverviewFilters: [
            {
                name: 'fieldsFilter',
                view: 'treo-core:views/fields/overview-fields-filter'
            },
            {
                name: 'localesFilter',
                view: 'treo-core:views/fields/overview-locales-filter'
            }
        ],

        updatedOverviewFilters: [],

        events: _.extend({
            'click a:not([data-action])': function(e){
                let path = e.currentTarget.getAttribute("href");
                e.preventDefault();
                this.getRouter().checkConfirmLeaveOut(function () {
                    this.getRouter().navigate(path, {trigger: true});
                }.bind(this), this, false);
            }
        }, Dep.prototype.events),

        data() {
            let data = Dep.prototype.data.call(this);
            data.overviewFilters = this.updatedOverviewFilters.map(filter => filter.name);
            return data;
        },

        setup() {
            Dep.prototype.setup.call(this);

            if (this.model && !this.model.isNew() && this.getMetadata().get(['scopes', this.scope, 'advancedFilters'])) {
                this.createOverviewFilters();
            }
        },

        setupFinal() {
            Dep.prototype.setupFinal.call(this);

            this.bindFixedHeaderOnScroll();
        },

        bindFixedHeaderOnScroll() {
            let $window = $(window);
            this.listenToOnce(this, 'remove', () => {
                $window.off('scroll.fixed-header')
            });
            const updateHeaderWidth = () => {
                let scrollTop = $window.scrollTop();
                let header = this.$el.find('.header-breadcrumbs');
                let navBarRight = $('#header .navbar-right');
                let width = $('#header ul.navbar-right > li').get().reduce((prev, curr) => {
                    return prev - $(curr).outerWidth()
                }, navBarRight.outerWidth() - 30);
                if (scrollTop > this.$el.find('.page-header').outerHeight() && !$('#header .navbar .menu').hasClass('open-menu')) {
                    header.addClass('fixed-header-breadcrumbs')
                        .css('width', width + 'px');
                } else {
                    header.removeClass('fixed-header-breadcrumbs')
                        .css('width', 'auto');
                }
            };
            this.listenTo(this, 'after:render', () => {
                updateHeaderWidth();
                $window.off('scroll.fixed-header');
                $window.on('scroll.fixed-header', () => updateHeaderWidth());
            });
        },

        createOverviewFilters() {
            this.updatedOverviewFilters = this.filterOverviewFilters();

            (this.updatedOverviewFilters || []).forEach(filter => {
                this.createView(filter.name, filter.view, {
                    el: `${this.options.el} .field[data-name="${filter.name}"]`,
                    model: this.model,
                    name: filter.name,
                    storageKey: 'overview-filters',
                    modelKey: 'advancedEntityView'
                }, view => view.render());
            });
        },

        filterOverviewFilters() {
            return (this.baseOverviewFilters || []).filter(filter => {
                if (filter.name === 'localesFilter') {
                    return this.getConfig().get('isMultilangActive') && (this.getConfig().get('inputLanguageList') || []).length
                }
                return true;
            });
        }

    });
});

