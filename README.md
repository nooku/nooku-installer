# Nooku Composer Installer

This Composer plugin will install Nooku Framework into your Joomla setup. 

## Usage

Create a composer.json manifest in your Joomla installation's root folder and add the following requirement:

```json
{
    "require": {        
        "nooku/nooku-framework": "dev-develop"
    },
    "minimum-stability": "dev"
}
```

Now execute `composer install` and the framework will be installed and enabled. 

## Requirements

* Composer
* Joomla version 2.5.0 and up.

## Contributing

Fork the project, create a feature branch, and send us a pull request.

## Authors

See the list of [contributors](https://github.com/nooku/nooku-composer/contributors).

## License

The `nooku/installer` plugin is licensed under the GPL v3 license - see the LICENSE file for details.
