RememberThis
================================================================================

Remember list for MODX Revolution.

Features:
--------------------------------------------------------------------------------
RememberThis is small session based remembering snippet solution for MODX
Revolution. It has two display modes: The default one displays the session
based list and the other displays a link to add elements to the list.

The data added to the list could be retreived from MODX resources or from xPDO
packages. If the added data is retreived from xPDO package, the row is
referenced by the id column.

Adding and removing elements from the list could be made by url params or by
Ajax. A jQuery Ajax script is used by default.

Installation:
--------------------------------------------------------------------------------
MODX Package Management

Usage:
--------------------------------------------------------------------------------

Most of the snippet settings should be made in the MODX system settings and are
used in snippet and ajax mode:

Property | Description | Default
---- | ----------- | -------
rowTpl | Row templage for the list output. | @FILE components/rememberthis/templates/rowTpl.html
outerTpl | Outer templage for the list output, if the list is not empty. Will be surrounded by a div with class *rememberthis* | @FILE components/rememberthis/templates/outerTpl.html
addTpl | Template for the add link. | @FILE components/rememberthis/templates/addTpl.html
noResultsTpl | Template that is displayed, if the list is empty. | @FILE components/rememberthis/templates/noResultsTpl.html
itemTitleTpl | Template for one list item. | @FILE components/rememberthis/templates/itemTitleTpl.html
ajaxLoaderImg | Image file, that is shown during AJAX requests. | assets/components/rememberthis/ajax-loader.gif
tvPrefix | Prefix for template variables in template chunks. | tv.
language | Snippet language. | en
packagename | xPDO package name where the added data is retreived from. If empty, the data is retrieved from resources. The data row is selected by the id column of the package or ModResource. | -
classname | xPDO class name where the added data is retreived from. | -
joins | Joins defined in the xPDO class, to retreive the added data. | -
jQueryPath | Path to jQuery script. | -
includeScripts | Include javascripts (at the end of the body). | yes
includeCss | Include css (at the end of the head). | yes
debug | Display debug informations. | no

In template chunks all resource fields of the MODX resources could be used as
placeholder including template variables with the prefix 'tv.' could be used.
If a xPDO package is used, all resource fields of this package could be used as
placeholder.

The following template system settings could be overridden by snippet call:
rowTpl, outerTpl, addTpl, noResultsTpl, itemTitleTpl, tvPrefix, language

Some settings could be used only in snippet call:

Parameter | Description | Default
---- | ----------- | -------
mode | Output mode of the snippet (add or display). | display
addId | If the output mode is `add`, this value (and the correspondenting data) is added to the list. | Current resource id