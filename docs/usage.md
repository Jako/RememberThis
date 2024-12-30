# Usage

The package contains two snippets and a formit hook, that use the following 
parameters[^1]

[^1]: Most of the settings for the snippet templates should be done in the MODX system settings, as they are used by the snippets and with Ajax.

### RememberThisAdd

This snippet will display an add button. It has the following properties:

| Property | Description                                                                | Default             |
|----------|----------------------------------------------------------------------------|---------------------|
| addId    | This value (and the correspondenting item title) is added to the list.[^2] | Current resource id |
| addTpl   | Template for the add link.                                                 | (system setting)    |
| tplPath  | Base path for template chunks using @FILE binding.                         | (system setting)    |

[^2]: If an xPDO object package and a classname are used, the object is referenced by the column with the system setting `keyname`.

The Add template can contain a link or a form to add xPDO objects as an element
to the list. The form can contain form fields and the link can contain
parameters to set properties of the remembered element. The field names and url
parameters must be prefixed with `[[+rememberqueryadd]]property_`.

#### Placeholders

The following placeholders are available in the **addTpl** template chunk:

| Placeholder      | Description                                            |
|------------------|--------------------------------------------------------|
| rememberurl      | Link to add an xPDO object as element to the list.     |
| rememberid       | The identifier to reference the added element.         |
| rememberqueryadd | The query parameter to add an element to the list[^3]. |

[^3]: The placeholder rememberqueryadd is filled with the `addQuery` system setting.

### RememberThisList

This snippet will display the remembered list. It has the following properties:

| Property     | Description                                                                                              | Default          |
|--------------|----------------------------------------------------------------------------------------------------------|------------------|
| jsonList     | Output a JSON-encoded array of associative arrays of element identifiers[^4] and element itemproperties. | 0 (No)           |
| noResultsTpl | Template that is displayed, if the list is empty.                                                        | (system setting) |
| outerTpl     | Outer template for the output of the list, if the list is not empty.                                     | (system setting) |
| rowTpl       | Row template for the output of a list element.                                                           | (system setting) |
| tplPath      | Base path for template chunks using @FILE binding.                                                       | (system setting) |
| wrapperTpl   | Wrapper template for the outer output or the empty output.                                               | (system setting) |

The snippet tries to display a list from the database if the query parameter `rememberthis` is not empty.

[^4]: The identifier is set by the `keyname` system setting, if the xPDO object package and classname is used.

#### Placeholders

The following placeholders are available in the **rowTpl** template chunk:

| Placeholder | Description                                                                                                               |
|-------------|---------------------------------------------------------------------------------------------------------------------------|
| identifier  | The identifier of the added element.                                                                                      |
| properties  | The properties for the added element.                                                                                     |
| itemtitle   | The title of the added element. This title is rendered with the chunk referenced by the `itemTitleTpl` system setting[^5] |
| deleteurl   | The url to delete this element from the list.                                                                             |
| deleteid    | The row ID to delete this element from the list.                                                                          |

The following placeholders are available in the **outerTpl** template chunk:

| Placeholder | Description                                  |
|-------------|----------------------------------------------|
| count       | The count of added elements in the list.     |
| wrapper     | The wrapper containing all rows of the list. |

[^5]: In the `itemTitleTpl` template chunk all column names of the xPDO object can be referenced by a placeholder with this name. Also, all properties can be referenced with the property name, i.e. a property set by `[[+rememberqueryadd]]property_test` can be referenced with the placeholder `[[+test]]`.

### RememberThisHook (FormIt hook)

The FormIt hook sets some placeholders in FormIt. You have to call the hook like this: 

```html
[[!FormIt?
&hooks=`...,RememberThisHook,...`
&rememberOuterTpl=`tplRememberOuterMail`
&rememberRowTpl=`tplRememberRowMail`
]]
```

The hook has the following properties:

| Property             | Description                                                                                              | Default          |
|----------------------|----------------------------------------------------------------------------------------------------------|------------------|
| rememberClearList    | Clear the list after running the hook.                                                                   | 0 (No)           |
| rememberJsonList     | Output a JSON-encoded array of associative arrays of element identifiers[^4] and element itemproperties. | 0 (No)           |
| rememberNoResultsTpl | Template that is displayed, if the list is empty.                                                        | (system setting) |
| rememberOuterTpl     | Outer template for the output of the list, if the list is not empty.                                     | (system setting) |
| rememberRowTpl       | Row template for the output of a list element.                                                           | (system setting) |
| rememberSaveList     | Save the remembered list with a hash in the database and fill a placeholder with this hash.              | 0 (No)           |
| rememberTplPath      | Base path for template chunks using @FILE binding.                                                       | (system setting) |
| rememberWrapperTpl   | Wrapper template for the outer output or the empty output.                                               | (system setting) |

#### Placeholders

The following placeholders are available in the **rowTpl** template chunk:

| Placeholder | Description                                                                                                               |
|-------------|---------------------------------------------------------------------------------------------------------------------------|
| identifier  | The identifier[^4] of the added element.                                                                                  |
| properties  | The properties for the element.                                                                                           |
| itemtitle   | The title of the added element. This title is rendered with the chunk referenced by the `itemTitleTpl` system setting[^5] |
| itemcount   | The value of an input field with the name `count_[[+rowid]]`, if the form is posted.                                      |
| rowid       | The row ID of the element.                                                                                                |

#### FormIt Placeholders

The following FormIt placeholders (`[[!+fi. ...]]`)  are set by the hook:

| Placeholder        | Description                                                                                                                                                                                                              |
|--------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| rememberthis       | The output of the rememberWrapperTpl template chunk (or the JSON encoded array of associative arrays of element identifiers[^4] and element itemproperties in the remembered list, if **jsonList** property is enabled). |
| rememberthis.list  | An array of associative arrays of element identifiers and element itemproperties in the remembered list.                                                                                                                 |
| rememberthis.count | The count of added elements in the list                                                                                                                                                                                  |
| rememberthis.hash  | The hash of the saved list in the database. The RememberThisList snippet can be triggered by the request parameter `rememberthis` containing this hash. That way remembered lists can be mailed to other users.          |

### RememberThisClear (FormIt hook)

The FormIt hook clears the list and can be used as one of the last entries in the FormIt hook list.

### System Settings

RememberThis uses the following system settings in the namespace `rememberthis`.

| Key                           | Name                     | Description                                                                                               | Default                       |
|-------------------------------|--------------------------|-----------------------------------------------------------------------------------------------------------|-------------------------------|
| rememberthis.addTpl           | Add Template             | Template for the add link.                                                                                | tplRememberThisAdd            |
| rememberthis.ajaxLoaderImg    | AJAX Loader Image        | Image file, that is shown during AJAX requests.                                                           | -                             |
| rememberthis.classname        | Classname                | xPDO class name where the added data is retreived from.                                                   | -                             |
| rememberthis.cookieExpireDays | Cookie Expiration        | The expiration time of the cookie (in days).                                                              | 90                            |
| rememberthis.cookieName       | Cookie Name              | The name of the cookie.                                                                                   | rememberlist                  |
| rememberthis.debug            | Debug                    | Display debug information.                                                                                | No                            |
| rememberthis.fields           | Fields                   | Comma separated list of field names of the xPDO class, that are remembered in the list.                   | -                             |
| rememberthis.includeCss       | Include CSS              | Include css (at the end of the head).                                                                     | Yes                           |
| rememberthis.includeScripts   | Include Javascripts      | Include javascripts (at the end of the body).                                                             | Yes                           |
| rememberthis.itemTitleTpl     | Item Title Template      | Template for one list item.                                                                               | tplRememberThisItemTitle      |
| rememberthis.joins            | Joins                    | Joins defined in the xPDO class, to retreive the added data.                                              | -                             |
| rememberthis.jQueryPath       | Path to jQuery           | Path to jQuery script.                                                                                    | -                             |
| rememberthis.keyname          | Keyname                  | xPDO class keyname to retrieve one data row.                                                              | id                            |
| rememberthis.language         | Language                 | The frontend language.                                                                                    | -                             |
| rememberthis.noResultsTpl     | No Results Template      | Template that is displayed, if the list is empty.                                                         | tplRememberThisNoResults      |
| rememberthis.outerTpl         | Outer Template           | Outer template for the output of the list, if the list is not empty.                                      | tplRememberThisOuter          |
| rememberthis.packagename      | Packagename              | xPDO package name from which the added data is retrieved. If empty, the data is retrieved from resources. | -                             |
| rememberthis.queryAdd         | Add Query Key            | Query key, that is used to add elements to the list.                                                      | add                           |
| rememberthis.queryDelete      | Delete Query Key         | Query key, that is used to remove elements from the list.                                                 | delete                        |
| rememberthis.rowTpl           | Row Template             | Row template for the output of a list element.                                                            | tplRememberThisRow            |
| rememberthis.scriptTpl        | Script Template          | Template for the javascript call.                                                                         | tplRememberThisScript         |
| rememberthis.showZeroCount    | Show Zero Value          | Show Zero Values in template.                                                                             | Yes                           |
| rememberthis.tplPath          | Templates Path           | Base path for template chunks using @FILE binding.                                                        | {assets_path}elements/chunks/ |
| rememberthis.tvPrefix         | Template Variable Prefix | Prefix for template variables in template chunks.                                                         | tv.                           |
| rememberthis.useCookie        | Use Cookie               | Save the remembered data in a cookie.                                                                     | No                            |
| rememberthis.useDatabase      | Use Database             | Save the remembered list in the database (only if the frontend user is logged into the site).             | No                            |
| rememberthis.wrapperTpl       | Wrapper Template         | Wrapper template for the outer output or the empty output.                                                | tplRememberThisWrapper        |

!!! caution "Multiple snippet calls and debug output"

    If you display the RememberThisList snippet call multiple on the page, all snippet calls have to be different, to show the debug information.

The following templating system settings can be overridden by snippet 
properties: **rowTpl**, **outerTpl**, **addTpl**, **noResultsTpl**

In the **rowTpl** template chunk all resource fields can be used as 
placeholder (template variables have to be prefixed using the prefix set in 
**tvPrefix**), if no xPDO package/classname is used. If a xPDO 
package/classname is used, all fields of the xPDO class can be used as 
placeholder.
