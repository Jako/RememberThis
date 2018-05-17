Changelog for RememberThis
==========================

- 2.1.0
    - The remembered list could be saved in the database (only if the frontend user is logged into the site and the useDatabase system setting is active).
    - Save the remembered list in a FormIt hook with a hash in the database and make it possible to mail remembered lists to other users.
- 2.0.2
    - Remember additional properties set in the data attributes in link mode.
- 2.0.1
    - Added itemcount placeholder in the row template.
- 2.0.0
    - Added wrapperTpl to RememberThisList snippet. The wrapper template contains the outer output or the empty output.
    - Added support for form POST. Example addTpl is available in tplRememberThisAddForm.
    - The javascript plugin could handle multiple lists (showing the same list elements). The callbacks parameters were changed for this reason.
    - The remembered XPDO object could contain properties.
    - The json encoded list and the rememberthis.list hook value contains an array of associative arrays of element identifiers and itemproperties.

- 1.1.7
    - @FILE/@INLINE/@CHUNK binding for template chunks
    - tplPath system setting
- 1.1.6
    - showZeroCount system setting
    - Default script template chunk contains an example for onAfterAdd callback
- 1.1.5
    - Bugfixes for snippet templating settings
- 1.1.4
    - clearList parameter in RememberThisHook
- 1.1.3
    - rememberthis.list set in RememberThisHook
- 1.1.2
    - jsonList parameter
- 1.1.1
    - Normalize AJAX result
    - Bugfix for cookies in AJAX
- 1.1.0
    - Optional cookie based remember list
    - Count list elements placeholder in outer template

- 1.0.1
    - Add/Remove query keys are changeable
- 1.0.0
    - Total rewrite of code
    - Snippet(s) renamed

- 0.6.0
    - Added parameter ajaxLoaderImg
    - Prepared rememberthis.js for callbacks
    - Each snippet call could use its own templates in display mode

- 0.5.0
    - Rewritten rememberthis.js
    - Use of revoChunkie class
    - Better templating

- 0.4.3
    - Fixed phpthumbof problem in ajax call
- 0.4.2
    - Session problem fixed
    - Javascript and styling issues
- 0.4.1
    - All template system settings could be overridden by snippet parameters
- 0.4.0
    - Initial public release for MODX Revolution