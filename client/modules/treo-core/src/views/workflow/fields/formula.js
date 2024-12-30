Espo.define('treo-core:views/workflow/fields/formula', 'views/fields/formula', function (Dep) {
    return Dep.extend({
        setup() {
            this.targetEntityType = "Product";
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:targetEntityType', () => {
                this.setUpNewFieldContext();
            });
        },
        afterRender: function () {
            this.setUpNewFieldContext();
            Dep.prototype.afterRender.call(this);
        },
        setUpNewFieldContext() {
            const targetEntityType = this.model.get('targetEntityType');
            if (targetEntityType) {
                this.targetEntityType = targetEntityType;
            }
        },
    })
});