# Nooku Composer Installer

This Composer plugin will deal with Nooku related packages. The following package types are supported: 

* [nooku-component](#user-content-nooku-component)
* [nooku-framework](#user-content-nooku-framework)

## Usage

### Nooku Component

Use the `nooku-component` type to install your reusable Nooku components into your Joomla setup. The Composer installer will take your code and place it inside the `/libraries/vendor` directory. For Joomla versions prior to 3.4, it will install them into the `/vendor` folder. 

Your package's `composer.json` file should contain at least the following directives:

```json
{
    "name": "vendor/name-component",
    "type": "nooku-component",
    "license": "GPLv3",
    "require": {
        "nooku/installer": "*"
    },
    "autoload": {
        "files": ["autoload.php"]
    }
}

```

Place this `composer.json` file in the root folder of your component's repository. 

The following settings are required to make your component installable through Composer:

* The `type` directive must be set to `nooku-component`.
* The `name` directive must end with the `-component` suffix.
* You must include the `autoload` directive. You do not, however, need to include the `autoload.php` file yourself. The plugin will autogenerate it for you if it's not found in the repository.

You can now publish your component on [Packagist](http://packagist.org) or [add your own repository](https://getcomposer.org/doc/05-repositories.md#vcs) to your Joomla's composer.json file.  Your component can then be installed using the `composer install` command.

### Nooku Framework

This type is only meant for use by the [Nooku Framework](https://github.com/nooku/nooku-framework). This type will have Composer plugin install the framework into your Joomla setup and enable it. 

To install the framework, create a composer.json manifest in your Joomla installation's root folder and add the following:

```json
{
    "require": {        
        "nooku/nooku-framework": "dev-develop"
    },
    "minimum-stability": "dev"
}
```

Now execute `composer install` to install the framework. 

## Requirements

* [Composer](https://getcomposer.org/)
* [Joomla](http://www.joomla.org/) version 2.5.0 and up.

## Contributing

Fork the project, create a feature branch, and send us a pull request.

## Authors

See the list of [contributors](https://github.com/nooku/nooku-installer/contributors).

## License

The `nooku/installer` plugin is licensed under the GPL v3 license - see the [LICENSE](https://github.com/nooku/nooku-installer/blob/master/LICENSE) file for details.
