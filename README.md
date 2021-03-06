# Nooku Composer Installer

This Composer plugin will deal with Nooku related packages. The following package types are supported: 

* [joomla-extension](#user-content-joomla-extension)
* [nooku-component](#user-content-nooku-component)
* [nooku-framework](#user-content-nooku-framework)

### Joomla Extension

If you set the type of your Composer package to `joomla-extension`, this plugin will attempt to install it into your Joomla installation as a regular extension. 

To make your extension installable through Composer, add a `composer.json` file with at least the following configuration values:

```json
{
    "name": "vendor/com_name",
    "type": "joomla-extension",
    "require": {
        "nooku/nooku-framework": "2.*"
    }
}
```

Note: the `nooku/nooku-framework` framework requirement will also install this installer plugin. If you did not build your package on top of the [Nooku Framework](http://github.com/nooku/nooku-framework), you can simply require this plugin instead: `"nooku/installer": "1.*"`.

#### Repository layout

To have Composer succesfully install your extension into Joomla, you need to make sure your repository layout resembles an installable Joomla package. This means that if you were to create an archive of your repository contents, that archive can be installed using the Joomla Extension Manager. 

This means that you need to add a [valid XML manifest](http://docs.joomla.org/Manifest_files) to the root directory and make sure it points to the correct paths. For a working example, you can always refer to our [todo](https://github.com/nooku/joomla-todo) example component!

#### Publishing

You can now publish your component on [Packagist](http://packagist.org) or [add your own repository](https://getcomposer.org/doc/05-repositories.md#vcs) to your Joomla's composer.json file.  Your component can then be installed using the `composer install` command.

### Nooku Component

Use the `nooku-component` type to install your reusable Nooku components into your Joomla setup or [Nooku Platform](http://www.nooku.org/platform) application. The Composer installer will take your code and place it inside the `/vendor` directory. For Joomla versions 3.4 and up, it will install into the `/libraries/vendor` folder. 

Your package's `composer.json` file should contain at least the following directives:

```json
{
    "name": "vendor/name-component",
    "type": "nooku-component",
    "license": "GPLv3",
    "require": {
        "nooku/installer": "1.*"
    },
    "autoload": {
        "files": ["autoload.php"]
    }
}

```

Place this `composer.json` file in the root folder of your component's repository. 

The following settings are required to make your component installable through Composer:

* The `type` directive must be set to `nooku-component`.
* You must make sure to require the `nooku/installer` package so that Composer knows how to handle your package.
* Every Nooku Component should include a `koowa-component.xml` manifest file. You can get an example from our [activities component](https://github.com/nooku/nooku-activities/blob/master/koowa-component.xml).
* You must include the `autoload` directive. You do not, however, need to include the `autoload.php` file yourself. The plugin will autogenerate it for you if it's not found in the repository.

You can now publish your component on [Packagist](http://packagist.org) or [add your own repository](https://getcomposer.org/doc/05-repositories.md#vcs) to your Joomla's composer.json file.  Your component can then be installed using the `composer install` command.

### Nooku Framework

This type is only meant for use by the [Nooku Framework](https://github.com/nooku/nooku-framework). This type will have Composer plugin install the framework into your Joomla setup and enable it. 

To install the framework, create a composer.json manifest in your Joomla installation's root folder and add the following:

```json
{
    "require": {        
        "nooku/nooku-framework": "2.*"
    },
    "minimum-stability": "dev"
}
```

Now execute `composer install` to install the framework. 

## Requirements

* [Composer](https://getcomposer.org/)
* [Joomla](http://www.joomla.org/) version 2.5.0 and up.

## Contributing

Nooku Installer is an open source, community-driven project. Contributions are welcome from everyone. 
We have [contributing guidelines](CONTRIBUTING.md) to help you get started.

## Contributors

See the list of [contributors](https://github.com/nooku/nooku-installer/contributors).

## License 

The `nooku-installer` plugin t is free and open-source software licensed under the [GPLv3 license](gplv3-license).

## Community

Keep track of development and community news.

* Follow [@joomlatoolsdev on Twitter](https://twitter.com/joomlatoolsdev)
* Join [joomlatools/dev on Gitter](http://gitter.im/joomlatools/dev)
* Read the [Joomlatools Developer Blog](https://www.joomlatools.com/developer/blog/)
* Subscribe to the [Joomlatools Developer Newsletter](https://www.joomlatools.com/developer/newsletter/)

[Joomlatools Framework]: http://www.joomlatools.com/developer/framework/
[gplv3-license]: https://github.com/nooku/nooku-framework/blob/master/LICENSE.txt
