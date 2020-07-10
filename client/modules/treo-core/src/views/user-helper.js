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
 * KennerCcore as well as TreoCore and EspoCRM is distributed in the hope that
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

Espo.define('treo-core:views/user-helper', ['view', 'lib!BootstrapTour'],
    Dep => Dep.extend({

        _template: '',

        controller: null,

        scope: null,

        modalOrder: [],

        modalTours: {},

        initHelpers(controller) {
            this.removingListeners();
            this.prepareBaseParams(controller);

            localStorage.removeItem('tour_end');

            this.initMainTour(controller);
            this.listenTo(Backbone, 'modal-shown', modal => this.initModalTour(modal));
        },

        initMainTour() {
            const steps = this.getTourSteps();
            if (steps.length) {
                this.tour = new Tour({
                    steps: steps,
                    template: this.getHelperTemplate(),
                    onEnd: this.endTour.bind(this),
                    onShown: this.reflexStepWaiter
                });
                this.tour.init();
                this.checkStepsToDisplay(this.tour);
            }
        },

        initModalTour(modal) {
            this.endPrevTour();

            const key = `${modal.cssName}-${modal.scope}`;
            const modalSteps = this.getModalSteps(key);
            if (modalSteps.length) {
                const tour = this.modalTours[modal.cid] = new Tour({
                    name: key,
                    steps: modalSteps,
                    template: this.getHelperTemplate(),
                    onEnd: this.endTour.bind(this),
                    onShown: this.reflexStepWaiter
                });
                tour.init();
                this.checkStepsToDisplay(tour);
                this.modalOrder.push(modal.cid);
            }

            modal.listenToOnce(modal, 'close', () => {
                const tour = this.modalTours[modal.cid];
                if (tour) {
                    tour.end();
                    delete this.modalTours[modal.cid];
                    this.modalOrder.pop();
                }
                this.startPrevTour();
            });
        },

        endPrevTour() {
            if (this.modalOrder.length) {
                const cid = this.modalOrder[this.modalOrder.length - 1];
                this.modalTours[cid].end();
            } else {
                this.tour.end();
            }
        },

        startPrevTour() {
            if (!this.getPreferences().get('disableUserHelper')) {
                const prev = this.modalTours[this.modalOrder[this.modalOrder.length - 1]] || this.tour;
                prev.start();
                prev.next();
            }
        },

        checkStepsToDisplay(tour) {
            const updateStepStates = () => {
                let notUpdated = true;
                this.visibleSteps = [];
                this.orphanSteps = [];
                tour._options.steps.forEach((item, i) => {
                    const step = tour.getStep(i);
                    tour._isOrphan(step) ? this.orphanSteps.push(step) : this.visibleSteps.push(step);
                });

                const currStepInd = tour.getCurrentStep();
                const step = tour.getStep(currStepInd);
                if (currStepInd === null && this.visibleSteps.length || currStepInd !== null && tour._isOrphan(step)) {
                    const newStep = this.visibleSteps[0];
                    if (newStep.i !== currStepInd) {
                        tour.setCurrentStep(newStep.i);
                        tour.showStep(newStep.i);
                        notUpdated = false;
                    }
                } else if (currStepInd !== null && !tour._isOrphan(step)) {
                    tour.showStep(currStepInd);
                    notUpdated = false;
                }
                return notUpdated;
            };

            if (updateStepStates()) {
                tour.checkStepsInverval = window.setInterval(updateStepStates, 1000);
            }
        },

        reflexStepWaiter(tour) {
            let wasHidden = false;

            const checkStep = () => {
                const currentStep = tour.getCurrentStep();
                const step = tour.getStep(currentStep);
                const isOrphan = tour._isOrphan(step);

                if (isOrphan) {
                    if ($('.popover[class*="tour-"]').hasClass('in')) {
                        if (wasHidden) {
                            window.clearInterval(tour.reflexStepInterval);
                            tour.next();
                        } else {
                            wasHidden = true;
                            tour.hideStep(currentStep, null);
                        }
                    }
                } else {
                    if (!$('.popover[class*="tour-"]').hasClass('in')) {
                        tour.showStep(currentStep);
                    }
                }
                return isOrphan;
            };

            if (checkStep()) {
                tour.reflexStepInterval = window.setInterval(checkStep, 500);
            }
        },

        prepareBaseParams(controller) {
            const routeParts = Backbone.history.fragment.split('/');

            this.controller = this.controller || controller;
            this.scope = this.scope || this.options.scope || (controller || {}).name;
            this.action = routeParts[1] || (controller || {}).defaultAction || 'list';
        },

        removingListeners() {
            this.listenToOnce(this.getBaseController(), 'action', () => {
                [this.tour, ...Object.values(this.modalTours)].forEach(item => item && item.end());
                this.remove();
            });

            this.listenToOnce(this, 'remove', () => this.clearIntervals());
        },

        clearIntervals() {
            [this.tour, ...Object.values(this.modalTours)].forEach(item => {
                if (item) {
                    window.clearInterval(item.checkStepsInverval);
                    window.clearInterval(item.reflexStepInterval);
                }
            });
        },

        endTour(tour) {
            this.clearIntervals();

            const check = $('input[data-role="disable"]');
            if (check.get().length) {
                const end = check[0].checked;
                if (!end) {
                    localStorage.removeItem('tour_end');
                    tour._removeState('end');
                }

                const changed = !_.isEqual(this.getPreferences().get('disableUserHelper'), end);
                if (changed) {
                    this.getPreferences().set({disableUserHelper: end});
                    this.getPreferences().save({disableUserHelper: end}, {
                        patch: true
                    });
                }
            }
        },

        getTourSteps() {
            return (this.getMetadata().get(['userHelper', this.scope, this.action, 'steps']) || []).map((step, i) => {
                step.title = (this.translate(`step-${i}`, 'steps', this.scope) || {}).title || step.title;
                step.content = (this.translate(`step-${i}`, 'steps', this.scope) || {}).content || step.content;
                return step;
            });
        },

        getModalSteps(key) {
            return this.getMetadata().get(['userHelper', this.scope, this.action, 'modals', key, 'steps']) || [];
        },

        getHelperTemplate() {
            const checked = this.getPreferences().get('disableUserHelper') ? 'checked' : '';

            return `<div class="popover" role="tooltip">
                        <div class="arrow"></div>
                        <h3 class="popover-title"></h3>
                        <div class="popover-content"></div>
                        <div class="popover-navigation">
                            <label class="popover-label">
                                <input type="checkbox" data-role="disable" ${checked}>
                                <span>${this.translate('doNotShowAgain', 'buttons', 'Tour')}</span>
                            </label>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-default" data-role="prev">&laquo; ${this.translate('Prev', 'buttons', 'Tour')}</button>
                                <button class="btn btn-sm btn-default" data-role="next">${this.translate('Next', 'buttons', 'Tour')} &raquo;</button>
                                <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">
                                    ${this.translate('Pause', 'buttons', 'Tour')}
                                </button>
                            </div>
                            <button class="btn btn-sm btn-default" data-role="end">${this.translate('End tour', 'buttons', 'Tour')}</button>
                        </div>
                    </div>`;
        }

    })
);