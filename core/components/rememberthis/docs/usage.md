#Usage

The package contains two snippets and a formit hook, that use the following 
parameters[^1]

[^1]: Most of the snippet templating settings should be made in the MODX system settings, since they are used by the snippets and with ajax.

###RememberThisAdd

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

###RememberThisList

This snippet will display the remembered list. It has the following properties:

Property | Description | Default
-------- | ----------- | -------
rowTpl | Row template for the list output. | tplRememberThisRow
outerTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
wrapperTpl | Wrapper template for the outer output or the empty output. | tplRememberThisWrapper
noResultsTpl | Template that is displayed, if the list is empty. | tplRememberThisNoResults
jsonList | Output a JSON encoded object of element identifiers[^4] and element itemproperties. | No 

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

###RememberThisHook (FormIt hook)

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
jsonList | Output a JSON encoded array of associative arrays of element identifiers[^4] and element itemproperties. | No 
clearList | Clear the list after running the hook. | No

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

###System Settings

The RememberThis system settings are prefixed by **rememberthis.** and are 
located in the rememberthis namespace.

Property | Description | Default
---- | ----------- | -------
rowTpl | Row template for the list output. | tplRememberThisRow
outerTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
addTpl | Template for the add link. | tplRememberThisAdd
noResultsTpl | Template that is displayed, if the list is empty. | tplRememberThisNoResults
scriptTpl | Template for the javascript call. | tplRememberThisScript
showZeroCount | Show Zero Values in template. | yes
itemTitleTpl | Template for one list item. | tplRememberThisItemTitle
ajaxLoaderImg | Image file, that is shown during AJAX requests. | FontAwesome fa-refresh
tvPrefix | Prefix for template variables in template chunks. | tv.
addQuery | Query key, used to add xPDO objects as elements to the list. | add
deleteQuery | Query key, used to remove elements from the list. | delete
language | Snippet language. | current **cultureKey**
tplPath | Base path for template chunks using @FILE binding. | `{assets_path}elements/chunks/`
packagename | xPDO package name where the added data is retreived from. If empty, the data is retrieved from resources. The data row is selected by the id of the resource or by the `keyname` column of the `classname` xPDO class. | -
classname | xPDO classname where the added data is retreived from. | -
keyname | xPDO class keyname to retrieve one data row. | id
joins | Joins defined in the xPDO class, to retreive the added data. | -
jQueryPath | Path to jQuery script. | -
includeScripts | Include javascripts (at the end of the body). | Yes
includeCss | Include css (at the end of the head). | Yes
debug | Display debug informations[^6]. | No

[^6]: **CAUTION**: If you display the RememberThisList snippet call multiple on the page, all snippet calls have to be different, to show the debug information.

The following templating system settings could be overridden by snippet 
properties: **rowTpl**, **outerTpl**, **addTpl**, **noResultsTpl**

In the **rowTpl** template chunk all resource fields could be used as 
placeholder (template variables have to be prefixed using the prefix set in 
**tvPrefix**), if no xPDO package/classname is used. If a xPDO 
package/classname is used, all fields of the xPDO class could be used as 
placeholder.

<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//piwik.partout.info/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 19]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//piwik.partout.info/piwik.php?idsite=19" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
