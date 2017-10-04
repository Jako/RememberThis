## Install from MODX Extras

Search for RememberThis in the Package Manager of a MODX installation and
install it in there.

## Manual installation

If you can't access the MODX Extras Repository in your MODX installation, you
can manually install RememberThis.

* Download the transport package from [MODX Extras](http://modx.com/extras/package/rememberthis) (or one of the pre built transport packages in [_packages](https://github.com/Jako/RememberThis/tree/master/_packages))
* Upload the zip file to your MODX installation's `core/packages` folder.
* In the MODX Manager, navigate to the Package Manager page, and select 'Search locally for packages' from the dropdown button.
* RememberThis should now show up in the list of available packages. Click the corresponding 'Install' button and follow instructions to complete the installation.

## Build it from source

To build and install the package from source you could use [Git Package
Management](https://github.com/TheBoxer/Git-Package-Management). The GitHub
repository of RememberThis contains a
[config.json](https://github.com/Jako/RememberThis/blob/master/_build/config.json)
to build that package locally. Use this option, if you want to debug
RememberThis and/or contribute bugfixes and enhancements.
