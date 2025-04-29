<div class="container content">
    <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
        <div id="login" class="panel panel-default">
            <div class="panel-heading">
                <div class="logo-container">
                    <img src="{{logoSrc}}" class="logo">
                </div>
            </div>
            <div class="panel-body">
                {{#if isDemo}}
                <div style="background-color: rgba(255,203,0,0.37); padding: 5px">
                    <p><span style="font-weight: bold">{{translate 'demoModeText'}}</span></p>
                    <p>{{translate 'Username'}}: <span style="font-weight: bold">{{login}}</span></p>
                    <p>{{translate 'Password'}}: <span style="font-weight: bold">{{password}}</span></p>
                </div>
                <hr>
                {{/if}}
                <div>
                    <form id="login-form" onsubmit="return false;">
                        <div class="form-group">
                            <label for="field-username">{{translate 'Username'}}</label>
                            <input type="text" name="username" id="field-userName" class="form-control" autocapitalize="off" autocorrect="off" tabindex="1" value="{{login}}">
                        </div>
                        <div class="form-group">
                            <label for="login">{{translate 'Password'}}</label>
                            <input type="password" name="password" id="field-password" class="form-control" tabindex="2" value="{{password}}">
                        </div>
                        <div class="form-group">
                            <label for="language">{{translate 'language' category='fields' scope='Settings'}}</label>
                            <select class="form-control" name="language" id="language">
                                {{#each locales}}
                                <option value="{{value}}" {{#if selected}}selected{{/if}}>{{label}}</option>
                                {{/each}}
                            </select>
                        </div>
                        <div>
                            <a href="javascript:" class="btn btn-link pull-right" data-action="passwordChangeRequest" tabindex="4">{{translate 'Forgot Password?' scope='User'}}</a>
                            <button type="submit" class="btn btn-primary" id="btn-login" tabindex="3">{{translate 'Login' scope='User'}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="container">{{{footer}}}</footer>
