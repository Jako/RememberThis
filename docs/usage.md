# Usage

The package contains two snippets and a formit hook, that use the following 
parameters[^1]

[^1]: Most of the snippet templating settings should be made in the MODX system settings, since they are used by the snippets and with ajax.

### RememberThisAdd

This snippet will display an add button. It has the following properties:

Property | Description | Default
-------- | ----------- | -------
addTpl | Template for the add link/form. | tplRememberThisAdd
addId | This xPDO Object referenced by this ID is added to the list.[^2] | Current resource id

[^2]: If a xPDO object package and classname is used, the object is referenced by the column with the `keyname` system setting.

The add template could contain a link or a form to add xPDO objects as element 
to the list. The form could contain form fields and the link could contain 
parameters to set properties of the remembered element. The field names and url 
parameters have to be prefixed with `[[+rememberqueryadd]]property_`.

#### Placeholders

The following placeholders are available in the **addTpl** template chunk:

Placeholder | Description
-------- | -----------
rememberurl | Link to add an xPDO object as element to the list.
rememberid | The identifier to reference the added element.
rememberqueryadd | The query parameter to add an element to the list[^3].

[^3]: The placeholder rememberqueryadd is filled with the `addQuery` system setting.

### RememberThisList

This snippet will display the remembered list. It has the following properties:

Property | Description | Default
-------- | ----------- | -------
rowTpl | Row template for the list output. | tplRememberThisRow
outerTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
wrapperTpl | Wrapper template for the outer output or the empty output. | tplRememberThisWrapper
noResultsTpl | Template that is displayed, if the list is empty. | tplRememberThisNoResults
jsonList | Output a JSON encoded object of element identifiers[^4] and element itemproperties. | 0 (No) 

The snippet will try to display a list from the database, when the request parameter `rememberthis` is not empty.

[^4]: The identifier is set by the `keyname` system setting, if the xPDO object package and classname is used.

#### Placeholders

The following placeholders are available in the **rowTpl** template chunk:

Placeholder | Description
-------- | -----------
identifier | The identifier of the added element.
properties | The properties for the added element.
itemtitle | The title of the added element. This title is rendered with the chunk referenced by the `itemTitleTpl` system setting[^5]
deleteurl | The url to delete this element from the list.
deleteid | The row ID to delete this element from the list.

The following placeholders are available in the **outerTpl** template chunk:

Placeholder | Description
-------- | -----------
count | The count of added elements in the list.
wrapper | The wrapper containing all rows of the list.

[^5]: In the `itemTitleTpl` template chunk all column names of the xPDO object could be referenced by a placeholder with this name. Also all properties could be referenced with the property name, i.e. a property set by `[[+rememberqueryadd]]property_test` could be referenced with the placeholder `[[+test]]`.

### RememberThisHook (FormIt hook)

The FormIt hook sets some placeholders in FormIt. You have to call the hook like this: 

```
[[!FormIt?
&hooks=`...,RememberThisHook,...`
&rememberOuterTpl=`tplRememberOuterMail`
&rememberRowTpl=`tplRememberRowMail`
]]
```

The hook has the following properties:

Property | Description | Default
-------- | ----------- | -------
rememberRowTpl | Row template for the list output. | tplRememberThisRow
rememberOuterTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
rememberWrapperTpl | Wrapper template for the outer output or the empty output. | tplRememberThisWrapper
noResultsTpl | Template that is displayed, if the list is empty. | tplRememberThisNoResults
jsonList | Output a JSON encoded array of associative arrays of element identifiers[^4] and element itemproperties. | 0 (No)
clearList | Clear the list after running the hook. | 0 (No)
saveList | Save the list with a hash in the database. | 0 (No)

#### Placeholders

The following placeholders are available in the **rowTpl** template chunk:

Placeholder | Description
-------- | -----------
identifier | The identifier[^4] of the added element.
properties | The properties for the element.
itemtitle | The title of the added element. This title is rendered with the chunk referenced by the `itemTitleTpl` system setting[^5]
itemcount | The value of an input field with the name `count_[[+rowid]]`, if the form is posted. 
rowid | The row ID of the element.

#### FormIt Placeholders

The following FormIt placeholders (`[[!+fi. ...]]`)  are set by the hook:

Placeholder | Description
-------- | -----------
rememberthis | The output of the rememberWrapperTpl template chunk (or the JSON encoded array of associative arrays of element identifiers[^4] and element itemproperties in the remembered list, if **jsonList** property is enabled).
rememberthis.list | An array of associative arrays of element identifiers and element itemproperties in the remembered list.
rememberthis.count | The count of added elements in the list
rememberthis.hash | The hash of the saved list in the database. The RememberThisList snippet could be triggered by the request parameter `rememberthis` containing this hash. That way remembered lists could be mailed to other users.

### System Settings

RememberThis uses the following system settings in the namespace `rememberthis`.

Setting | Description | Default
------- | ----------- | -------
rowTpl | Row template for the list output. | tplRememberThisRow
outerTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
wrapperTpl | Wrapper template for the outer output or the empty output. | tplRememberThisWrapper
addTpl | Template for the add link. | tplRememberThisAdd
noResultsTpl | Template that is displayed, if the list is empty. | tplRememberThisNoResults
scriptTpl | Template for the javascript call. | tplRememberThisScript
showZeroCount | Show Zero Values in template. | Yes
itemTitleTpl | Template for one list item. | tplRememberThisItemTitle
ajaxLoaderImg | Image file, that is shown during AJAX requests. | FontAwesome fa-refresh
tvPrefix | Prefix for template variables in template chunks. | tv.
queryAdd | Query key, used to add xPDO objects as elements to the list. | add
queryDelete | Query key, used to remove elements from the list. | delete
language | Snippet language. | current **cultureKey**
tplPath | Base path for template chunks using @FILE binding. | `{assets_path}elements/chunks/`
packagename | xPDO package name where the added data is retreived from. If empty, the data is retrieved from resources. The data row is selected by the id of the resource or by the `keyname` column of the `classname` xPDO class. | -
classname | xPDO classname where the added data is retreived from. | -
keyname | xPDO class keyname to retrieve one data row. | id
joins | Joins defined in the xPDO class, to retreive the added data. | -
jQueryPath | Path to jQuery script. | -
includeScripts | Include javascripts (at the end of the body). | Yes
includeCss | Include css (at the end of the head). | Yes
useCookie  | Save the remembered data in a cookie. | No
cookieName  | The name of the cookie. | rememberlist
cookieExpireDays  | The expiration time of the cookie (in days). | 90
useDatabase  | Save the remembered list in the database (only if the frontend user is logged into the site). | No
debug | Display debug informations. | No

!!! caution
    If you display the RememberThisList snippet call multiple on the page, all snippet calls have to be different, to show the debug information.

The following templating system settings could be overridden by snippet 
properties: **rowTpl**, **outerTpl**, **addTpl**, **noResultsTpl**

In the **rowTpl** template chunk all resource fields could be used as 
placeholder (template variables have to be prefixed using the prefix set in 
**tvPrefix**), if no xPDO package/classname is used. If a xPDO 
package/classname is used, all fields of the xPDO class could be used as 
placeholder.
