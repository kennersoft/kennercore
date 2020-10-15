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

Espo.define('treo-core:view', 'class-replace!treo-core:view',
    Dep => Dep.extend({

        pipelines: {},

        initialize(options) {
            if (options && options.helper) {
                this.loadPipelines(options.helper.metadata, () => {
                    Dep.prototype.initialize.call(this, options);
                });
            }
        },

        loadPipelines(metadata, callback) {
            let pipes = [];

            (Object.values(this.pipelines) || []).forEach(pipeline => {
                (metadata.get(pipeline) || []).forEach(pipe => {
                    pipes.push(pipe);
                });
            });

            if (pipes.length) {
                Espo.loader.require(pipes, () => {
                    callback();
                });
            } else {
                callback();
            }
        },

        runPipeline(pipeline, data) {
            if (this.pipelines[pipeline]) {
                let result = data;
                (this.getMetadata().get(this.pipelines[pipeline]) || []).forEach(current => {
                    this._factory.create(current, {}, view => {
                        result = view.runPipe.call(this, result);
                    });
                });
                return result;
            }
        },

        runPipelineAsync(pipeline, data) {
            if (this.pipelines[pipeline]) {
                return (this.getMetadata().get(this.pipelines[pipeline]) || []).reduce((prev, current) => {
                    return prev.then(result => {
                        return new Promise(resolve => {
                            this._factory.create(current, {}, view => {
                                resolve(view.runPipe.call(this, result));
                            });
                        });
                    });
                }, new Promise(resolve => resolve(data)));
            }
        },

        getParentView(level) {
            let parent = this;
            level = parseInt(level) || 1;

            for (let i = 0; i < level; i++) {
                parent = Dep.prototype.getParentView.call(parent);
            }

            return parent;
        }

    })
);
