<div class="input-group">
    <input type="text" class="form-control main-element" name="{{name}}" value="{{value}}" {{#if params.maxLength}} maxlength="{{params.maxLength}}"{{/if}}
           autocomplete="off" placeholder="{{translate 'typeAndPressEnter' category='messages' scope='Global'}}">
    <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-icon" data-name="{{name}}" data-action="removeField"><span class="fas fa-times"></span></button>
    </div>
</div>