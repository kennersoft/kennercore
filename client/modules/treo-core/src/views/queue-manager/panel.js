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

Espo.define('treo-core:views/queue-manager/panel', 'view', function (Dep) {

    return Dep.extend({

        interval: null,

        queueCheckInterval: 2,

        showDone: true,

        template: 'treo-core:queue-manager/panel',

        events: _.extend({
            'change input[name="showDone"]': function (e) {
                e.preventDefault();
                e.stopPropagation();

                this.showDone = this.$showDone.is(':checked');
                this.getStorage().set('list', 'showDone', this.showDone);
                this.collection.where = this.getWhere();
                this.collection.fetch();
            },
            'click [data-action="viewList"]': function (e) {
                e.preventDefault();
                e.stopPropagation();

                this.getRouter().navigate($(e.target).attr('href'), {trigger: true});
                this.trigger('closeQueue');
            }
        }, Dep.prototype.events),

        data() {
            return {
                showDone: this.showDone
            };
        },

        setup() {
            this.queueCheckInterval = this.getConfig().get('queueCheckInterval') || this.queueCheckInterval;

            this.showDone = !(this.getStorage().get('list', 'showDone') === 'false');

            this.wait(true);
            this.getCollectionFactory().create('QueueItem', collection => {
                this.collection = collection;
                this.collection.maxSize = 50;
                this.collection.url = 'QueueItem';
                this.collection.sortBy = 'sortOrder';
                this.collection.asc = false;
                this.collection.data.select = 'name,status,createdById,createdByName,sortOrder,createdAt';
                this.collection.where = this.getWhere();
                this.collection.whereAdditional = this.getWhereAdditional();

                this.listenTo(this.collection, 'reloadList', () => {
                    this.collection.fetch();
                });

                this.listenToOnce(this, 'after:render', () => this.initInterval());

                this.listenToOnce(this, 'remove', () => {
                    if (this.interval) {
                        window.clearInterval(this.interval);
                    }
                });

                this.wait(false);
            });
        },

        afterRender() {
            this.$showDone = this.$el.find('input[name="showDone"]');

            this.listenToOnce(this.collection, 'sync', () => {
                let viewName = 'views/record/list';
                this.createView('list', viewName, {
                    el: this.options.el + ' .list-container',
                    collection: this.collection,
                    rowActionsDisabled: true,
                    checkboxes: false,
                    headerDisabled: true,
                    layoutName: 'listInQueueManager',
                    showMore: false
                }, function (view) {
                    view.render();
                });
            });
            this.collection.fetch();
        },

        initInterval() {
            this.interval = window.setInterval(() => {
                if (!this.isQueueModalShowed()) {
                    this.collection.fetch();
                }
            }, 1000 * this.queueCheckInterval);
        },

        isQueueModalShowed() {
            return $(document).find('.queue-modal').length;
        },

        getWhere() {
            if (this.showDone) {
                return [];
            } else {
                return [
                    {
                        field: 'status',
                        type: 'in',
                        value: ['Running', 'Pending']
                    }
                ];
            }
        },

        getWhereAdditional() {
            return [
                {
                    field: 'status',
                    type: 'notIn',
                    value: ['Canceled', 'Closed']
                }
            ];
        }

    });

});
