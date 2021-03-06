<div class="button-container">
    <div class="btn-group">
    {{#each buttonList}}
        {{button name label=label scope='Admin' style=style}}
    {{/each}}
    </div>
</div>

<style>
    #layout ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    #layout ul > li {
        background-color: #FFF;
    }
    #layout ul.panels > li {
        padding: 5px 10px;
        margin-bottom: 20px;
        min-height: 80px;
        border: 1px solid #CCC;
        list-style: none;
    }
    #layout ul.rows {
        min-height: 80px;
    }
    #layout ul.rows > li  {
        list-style: none;
        border: 1px solid #CCC;
        margin: 8px 0;
        padding: 5px;
        height: 72px;
    }

    #layout ul.cells  {
        min-height: 30px;
        margin-top: 12px;
    }
    #layout ul.panels ul.cells > li {
        width: 46%;
        float: left;
    }
    #layout ul.panels ul.cells > li[data-full-width="true"] {
        width: 94%;
    }
    #layout ul.panels ul.cells[data-cell-count="1"] > li {
        width: 94%;
        a[data-action="minusCell"] {
            display: none;
        }
    }
    #layout ul.panels ul.cells[data-cell-count="2"] > li {
        width: 46%;
    }
    #layout ul.panels ul.cells[data-cell-count="3"] > li {
        width: 30%;
    }
    #layout ul.panels ul.cells[data-cell-count="4"] > li {
        width: 22%;
    }
    #layout ul.cells > li {
        list-style: none;
        border: 1px solid #CCC;
        margin: 5px;
        padding: 5px;
        height: 32px;
    }
    #layout ul.cells > li.ui-state-hover {
        border-color: #c78c8c;
    }
    #layout  ul.cells.disabled > li {
        margin: 5px 0;
    }
    #layout ul.rows > li > div {

    }
    #layout ul.disabled {
        min-height: 200px;
        width: 100%;
    }
    #layout ul.disabled > li a {
        display: none;
    }
    #layout header {
        font-weight: 600;
        margin-bottom: 10px;
    }
    #layout ul.panels > li label {
        display: inline;
    }
    #layout ul.panels > li header a {
        font-weight: normal;
    }
    #layout ul.panels > li > div {
        width: auto;
        text-align: left;
        margin-left: 5px;
    }

    ul.cells li.cell a.remove-field {
        display: none;
    }
    ul.cells li.cell:hover a.remove-field {
        display: block;
    }
    ul.cells[data-cell-count="1"] li.cell:hover a.remove-field[data-action="minusCell"] {
        display: none;
    }
    ul.panels > li a.remove-panel {
        display: none;
    }
    ul.panels > li:hover a.remove-panel {
        display: block;
    }
    ul.rows > li a.remove-row {
        display: none;
        width: 5px;
        height: 5px;
    }
    ul.rows > li:hover a.remove-row {
        display: block;
    }
    ul.rows > li a.add-cell {
        display: none;
        position: relative;
        width: 5px;
        height: 5px;
        top: 19px;
        right: 7px;
    }
    ul.rows > li:hover a.add-cell {
        display: block;
    }
    ul.rows > li[data-cell-count="4"]:hover a.add-cell {
        display: none;
    }
    ul.panels > li a.edit-panel-label {
        display: none;
    }
    ul.panels > li:hover a.edit-panel-label {
        display: inline-block;
    }
    div.row-actions {
        float: right;
        height: 50px;
        width: 12px;
    }
    .cell.ui-draggable-dragging {
        opacity: 0.7;
    }
</style>

<div id="layout" class="row">
    <div class="col-md-8">
        <div class="well">
            <header>{{translate 'Layout' scope='LayoutManager'}}</header>
            <ul class="panels">
            {{#each panelDataList}}
            <li data-number="{{number}}" class="panel-layout">
            {{{var viewKey ../this}}}
            </li>
            {{/each}}
            </ul>

            <div><a href="javascript:;" data-action="addPanel">{{translate 'Add Panel' scope='Admin'}}</a></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="well">
            <header>{{translate 'Available Fields' scope='Admin'}}</header>
            <ul class="disabled cells clearfix">
                {{#each disabledFields}}
                <li class="cell" data-name="{{./this}}">
                    <div class="left" style="width: calc(100% - 14px);">
                        {{translate this scope=../scope category='fields'}}
                    </div>
                    <div class="right" style="width: 14px;">
                        <a href="javascript:" data-action="removeField" class="remove-field"><i class="fas fa-times"></i></a>
                    </div>
                </li>
                {{/each}}
            </ul>
        </div>
    </div>
</div>

<div id="layout-row-tpl" style="display: none;">
    <li data-cell-count="{{columnCount}}">
        <div class="row-actions clear-fix">
            <a href="javascript:" data-action="removeRow" class="remove-row"><i class="fas fa-times"></i></a>
            <a href="javascript:" data-action="plusCell" class="add-cell"><i class="fas fa-plus"></i></a>
        </div>
        <ul class="cells" data-cell-count="{{columnCount}}">
            <% for (var i = 0; i < {{columnCount}}; i++) { %>
                <li class="empty cell">
                <div class="right" style="width: 14px;">
                    <a href="javascript:" data-action="minusCell" class="remove-field"><i class="fas fa-minus"></i></a>
                </div>
                </li>
            <% } %>
        </ul>
    </li>
</div>

<div id="empty-cell-tpl" style="display: none;">
    <li class="empty cell disabled">
        <div class="right" style="width: 14px;">
            <a href="javascript:" data-action="minusCell" class="remove-field"><i class="fas fa-minus"></i></a>
        </div>
    </li>
</div>
