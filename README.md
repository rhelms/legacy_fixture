# LegacyFixture

LegacyFixture provides a CakePHP bake shell that allows you to generate fixtures for existing models and odd table names.

## Requirements

* CakePHP 2.2.0 or greater
* PHP 5.3.0 or greater

## Installation

* Clone/Copy the files in this directory into `app/Plugin/LegacyFixture`
* Ensure the plugin is loaded in `app/Config/bootstrap.php` by calling `CakePlugin::load('LegacyFixture');`
	* It is recommended that this plugin is only loaded when debug is greater than 0

	if (Configure::read('debug') > 0) {
		CakePlugin::load('LegacyFixture');
	}


* Set debug mode to at least 1.

## Versions

LegacyFixture currently only has one release, compatible with CakePHP 2.2.0. It is untested with other versions of CakePHP.

## Usage

To run the `LegacyFixtureBakeShell`, do the following from the `app` directory:

	$ ./Console/cake LegacyFixture.legacyFixtureBake

You will be given an interactive console from which you can select from existing models to generate fixtures for odd
table names.

Alternatively, you can run the shell for a specific model or table name.

	$ ./Console/cake LegacyFixture.legacyFixtureBake legacy_fixture BakeOdd

or

	$ ./Console/cake LegacyFixture.legacyFixtureBake legacy_fixture bake_odd
