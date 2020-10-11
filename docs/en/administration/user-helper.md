# User Helper

It is created for showing additional tooltips for user and based on [Bootstrap Tour](https://bootstraptour.com/).

> Main view located at treo-core/src/views/user-helper.js

 ## Tour configuration
 
The configuration of all tours is saved in the metadata along path `app/Treo/Resources/metadata/userHelper/SCOPE.json`, where SCOPE is current entity for page.

> You can see full example of tour at app/Treo/Resources/metadata/userHelper/Home.json

First key in the configuration is action, for example, it could be "list", "view" or "edit". If action does not exist it will be an empty string.

In our case, the part of configuration will be as follows:
<pre>
"": {
   "steps": [
     {
       "element": ".nav.navbar-nav li.global-search-container",
       "title": "Global search",
       "content": "You can search all the records existing in the KennerCore system using the global search functionality. The list of entities available for search is configured by the administrator.",
       "smartPlacement": true
     },
     ...
   ]
}
</pre>

Since there is no action, it will be an empty string. So, let's describe the step parameters: 
* element - HTML element on which the step popover should be shown.
* title - Step title.
* content - Step content.
* smartPlacement - It dynamically reorients the popover by default by specifying auto for the placement.

> For attaching an handler on click on the step element to continue the tour you should use `reflex` parameter.

### Modal tours

Configuration for modals is on the same level as steps, under key "modals" and every modal key consist of its `css-name` and `scope`.

For example:

<pre>
"modals": {
  "edit-modal-Category": {
    "steps": [
      {
        "element": "[id^=category-edit-small] input[name=\"isActive\"]",
        "title": "Some title",
        "content": "Some content",
        "smartPlacement": true
      },
      ...
    ]
  }
}
</pre>
Modal key here is `edit-modal-Category`, where:
* `edit-modal` is `css-name`;
* `Category` is `scope`.

For `modal tours`, the configuration of the steps is similar main page tour.

