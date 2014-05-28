# Nooku Composer Installer

This Composer plugin will install components into your Nooku Framework setup. 

## Usage

Create a composer.json manifest in your component's root folder. Set the type to `nooku-installer` and make sure it requires the `nooku/installer` package. Example:

```json
{
    	"name": "nooku/my-component",
    	"description": "My Nooku component!",
    	"type": "nooku-installer",
    	"require": {
        	"nooku/installer": "0.9.0"
    	}
}
```
Make sure to set the correct version! For example, if you are working on Nooku Framework v0.9.0, use `nooku/installer` of the same version.

Now add your `nooku/my-component` package to your [http://github.com/nooku/nooku-framework](Nooku Framework)'s composer.json file and execute `composer install`.

## Requirements

* Composer
* Nooku Framework version 0.9.0 and up.

## Contributing

Fork the project, create a feature branch, and send us a pull request.

## Authors

See the list of [contributors](https://github.com/nooku/nooku-composer/contributors).

## License

The `nooku/installer` plugin is licensed under the GPL v3 license - see the LICENSE file for details.
