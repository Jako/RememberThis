# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.1] - TBA

### Added

- The JSON in the `rememberthis.joins` system setting can contain a prefix that is prepended to the keys of the joined table

## [2.4.0] - 2024-12-30

### Added

- Add RememberThisClear hook to clear the list at the end of all hooks
- Add `rememberthis.fields` system setting to restrict the fields that are remembered in the list

### Changed

- The `rememberthis.joins` system setting can also use a JSON encoded array with class, alias and conditions for each join
- Use the Parse helper class
- Flatten the retrieved row by dot notation to access nested values directly

## [2.3.1] - 2023-01-11

### Fixed

- Fix wrong tplPath default in RememberThisHook

## [2.3.0] - 2022-01-18

### Changed

- Code refactoring
- Full MODX 3 compatibility
- Class based remember processor

## [2.2.3] - 2020-06-09

### Changed

- Prevent an error, when $modx->resource is not set
- Fix an issue showing not the last added element in AJAX Mode 

## [2.2.2] - 2019-07-18

### Changed

- Bugfix for hook compatibility with FormIt 4.2

## [2.2.1] - 2019-01-30

### Added

- Snippet properties are available in template chunks with the properties prefix
- Remove URL parameter with a properties prefix from the delete link URL

## [2.2.0] - 2018-07-05

### Added

- Global click/submit handling javascript methods

## [2.1.0] - 2018-05-17

### Added

- The remembered list could be saved in the database (only if the frontend user is logged into the site and the `rememberthis.useDatabase` system setting is active)
- Save the remembered list in a FormIt hook with a hash in the database and make it possible to mail remembered lists to other users

## [2.0.2] - 2017-09-01

### Added

- Remember additional properties set in the data attributes in link mode

## [2.0.1] - 2016-03-09

### Added

- Added itemcount placeholder in the row template

## [2.0.0] - 2016-02-13

### Added

- wrapperTpl RememberThisList snippet property
- Support for form POST
- The javascript plugin could handle multiple lists (showing the same list elements). The callback parameters were changed for this reason
- The remembered XPDO object could contain properties
- The json encoded list and the rememberthis.list hook value contains an array of associative arrays of element identifiers and itemproperties

## [1.1.7] - 2015-09-18

### Added

- @FILE/@INLINE/@CHUNK binding for template chunks
- Add `rememberthis.tplPath` system setting

## [1.1.6] - 2015-06-10

### Added

- Add `rememberthis.showZeroCount` system setting
- Default script template chunk contains an example for onAfterAdd callback

## [1.1.5] - 2015-05-05

### Changed

- Fix for snippet templating settings

## [1.1.4] - 2015-05-05

### Added

- clearList RememberThisHook snippet property

## [1.1.3] - 2015-05-01

### Added

- rememberthis.list set in RememberThisHook

## [1.1.2] - 2015-04-30

### Added

- jsonList RememberThisList snippet property

## [1.1.1] - 2015-04-24

### Added

- Normalize AJAX result

### Changed

- Bugfix for cookies in AJAX

## [1.1.0] - 2015-04-22

### Added

- Optional cookie based remember list
- Count list elements placeholder in outer template

## [1.0.1] - 2015-04-15

### Added

- Add/Remove query keys are changeable

## [1.0.0] - 2015-04-15

### Added

- Initial release for MODX Revolution
