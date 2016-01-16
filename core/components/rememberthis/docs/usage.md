#Usage

The package contains two snippets and a formit hook, that use the following parameters[^1]

[^1]: Most of the snippet templating settings should be made in the MODX system settings, since they are used by the snippets and with ajax.

###RememberThisAdd

This snippet will display an add button.

Property | Description | Default
-------- | ----------- | -------
addTpl | Template for the add link. | tplRememberThisAdd
addId | This value (and the correspondenting item title) is added to the list. | Current resource id

###RememberThisList

This snippet will display the remembered list.

Property | Description | Default
-------- | ----------- | -------
rowTpl | Row template for the list output. | tplRememberThisRow
outerTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
noResultsTpl | Template that is displayed, if the list is empty. | tplRememberThisNoResults
jsonList | Output a JSON encoded list of element `keyname` values. | no 

###RememberThisHook (FormIt hook)

The Formit placeholder rememberthis i.e. `[[+fi.rememberthis]]` will be filled with the content of the list. You have to call the hook like this: 

```
[[!FormIt?
&hooks=`...,RememberThisHook,...`
&rememberOuterTpl=`tplRememberOuterMail`
&rememberRowTpl=`tplRememberRowMail`
]]
```

Property | Description | Default
-------- | ----------- | -------
rememberRowTpl | Row template for the list output. | tplRememberThisRow
rememberOuterTpl | Outer template for the list output, if the list is not empty. | tplRememberThisOuter
jsonList | Fill the formit placeholder **rememberthis** with a JSON encoded list of element key/values pairs. | no 
clearList | Clear the list after running the hook. | no

###System Settings

All the system settings are prefixed by **rememberthis.** and are located in the rememberthis namespace.

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
addQuery | Query key, used to add elements to the list. | add
deleteQuery | Query key, used to remove elements from the list. | delete
language | Snippet language. | current **cultureKey**
tplPath | Base path for template chunks using @FILE binding. | `{assets_path}elements/chunks/`
packagename | xPDO package name where the added data is retreived from. If empty, the data is retrieved from resources. The data row is selected by the id column of the package or modResource. | -
classname | xPDO class name where the added data is retreived from. | -
joins | Joins defined in the xPDO class, to retreive the added data. | -
jQueryPath | Path to jQuery script. | -
includeScripts | Include javascripts (at the end of the body). | yes
includeCss | Include css (at the end of the head). | yes
debug | Display debug informations. | no

The following templating system settings could be overridden in the snippet calls:
**rowTpl**, **outerTpl**, **addTpl**, **noResultsTpl**

In template chunks all resource fields of the MODX resources could be used as placeholder including template variables with the prefix 'tv.' could be used. 

If a xPDO package is used, all fields of this package could be used as placeholder.


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
