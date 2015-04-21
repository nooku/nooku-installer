CHANGELOG
=========

To get the diff for a specific change, go to https://github.com/nooku/nooku-installer/commit/xxx where xxx is the change hash.
To view the diff between two versions, go to https://github.com/nooku/nooku-installer/compare/v0.1.0...v0.1.1

## 1.0.4 (2015-04-21)

* Added - Support for `nooku-framework-joomla` package

## 1.0.3 (2015-03-04)

* Fixed - Fix call to undefined function Composer\Autoload\includeFile() error in Joomla 3.4

## 1.0.2 (2014-11-17)

* Fixed - Load Nooku Component name from koowa-component.xml manifest instead of parsing it out of the package name.

## 1.0.1 (2014-11-17)

* Fixed - Make sure to bootstrap the Koowa plugin after the framework installation.
* Removed - Do not enforce naming conventions on Composer package names.

## 1.0.0 (2014-09-02)

 * Improved - Always load the framework plugin if it is available when we install regular extensions.
 * Improved - Improved element name detection in the JoomlaExtension installer.
 * Added - Support `joomla-extension` package types to install any kind of Joomla extension.

## 0.2.0 (2014-08-29)

 * Improved - Implement support for multiple installers.
 * Added - Support `nooku-component` package types to install reusable Nooku Framework components.

## 0.1.0 (2014-08-28)

 * Added - Created first version of the Composer plugin, allowing you to install `nooku/nooku-framework` into Joomla applications.
