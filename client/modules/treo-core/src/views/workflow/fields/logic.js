Espo.define('treo-core:views/workflow/fields/logic', ['views/fields/base'], function (Dep) {
    return Dep.extend({
        listTemplate: 'treo-core:workflow/fields/logic/base',

        detailTemplate: 'treo-core:workflow/fields/logic/base',

        editTemplate: 'treo-core:workflow/fields/logic/base',

        scope: "Product",
        conditionGroup: "conditionGroup",
        logicView: "views/admin/field-manager/fields/dynamic-logic-conditions",

        setup() {
            Dep.prototype.setup.call(this);
            this.scope = "Product";

            this.listenTo(this.model, 'change:targetEntityType', () => {
                this.setupNewScope();
            });
        },
        afterRender: function () {
            this.setupNewScope();
            Dep.prototype.afterRender.call(this);
        },
        setupNewScope() {
            if (this.model.get('targetEntityType')) {
                this.scope = this.model.get('targetEntityType');
                this.createNewView();
            }
        },
        createNewView() {
            this.clearView(this.conditionGroup);

            let params = {
                model: this.model,
                el: `${this.options.el} > .field[data-name="conditionGroup"]`,
                name: this.name,
                mode: this.mode,
                scope: this.scope,
            };

            this.createView(
                this.conditionGroup,
                this.logicView,
                params,
                (view) => {
                    view.render();
                },
            );
        },
        fetch() {
            let data = {};
            let view = this.getView(this.conditionGroup);
            if (view) {
                _.extend(data, view.fetch());
            }
            return data;
        },
        validate() {
            let validate = false;
            let view = this.getView(this.conditionGroup);
            if (view) {
                validate = view.validate();
            }
            return validate;
        },

        setMode(mode) {
            Dep.prototype.setMode.call(this, mode);

            let valueField = this.getView(this.conditionGroup);
            if (valueField) {
                valueField.setMode(mode);
            }
        }
    })
});
