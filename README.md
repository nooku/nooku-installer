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

We appreciate any contribution, whether it is related to bugs, grammar, or simply a suggestion or
improvement. We ask that any contribution follows a few simple guidelines in order to be properly received.

We follow the [GitFlow][gitflow-model] branching model, from development to release. If you are not familiar with it,
there are several guides and tutorials online to learn about it.

There are a few things you must know before submitting a pull request:

- All changes need to be made against the `develop` branch. However, it is very well appreciated and highly suggested to
start a new feature branch from `develop` and make your changes in this new branch. This way we can just checkout your
feature branch for testing before merging it into `develop`.
- We will not consider pull requests made directly to the `master` branch.

## Authors

See the list of [contributors](https://github.com/nooku/nooku-installer/contributors).

## License

The `nooku-installer` plugin t is free and open-source software licensed under the [GPLv3 license](gplv3-license).

[gitflow-model]: http://nvie.com/posts/a-successful-git-branching-model/
[gplv3-license]: https://github.com/nooku/nooku-framework/blob/master/LICENSE.txt
