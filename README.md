# Qore

Qore is a lightweight framework for PHP, built to make it simple to design and send websites to production.

This documentation currently represents the target functionality.  

## Installation

Qore is ideally used as a standalone framework, so cloning this git repo is the simplest way to get a project started.  

> *****@todo***** Setup Qore in a more generic way such that it can be included, and updated, via composer.
> *****@todo***** This repo should be converted to be a skeleton app, with proper composer setup for dependencies.

### Adding Modules

Modules should be added via composer, and installed via `composer update`.  Modules must be given an active status in Qore's master module list in config.php.

### Finalizing Installation

Once the code is in place, Qore's CLI script should be used to run the proper install routines for each module.

```bash
php qore-cli install
```

# Building Modules for Qore

Qore comes with 2 primary modules.

* **Qore_Framework** which builds the MVC architecture that is used throughout the rest of the application.
* **Qore_Admin** which constructs the admin area that other apps can add management areas to.

Any amount of modules can be added.  After any modules are added, `qore-cli install` should be run.

## Structure

Modules can contain many pieces to define them.  Only one piece is required:

* Module/**config.json**: defines any routes and configuration elements that the module requires.

#### config.json

If this file is not present, the module will not install (and will throw an error).  It should be a single JSON object containing four child objects ("metadata", "configuration", "routes", and "install") that will be parsed by the `qore-cli install` method, and executed as described below by the class `Qore\Framework\Utility\Installer`.

##### Metadata Section

The metadata section should contain information about the module itself.  The only required keys are **name** and **version**.  Everything else is optional, and will appear labeled by its key in the admin area.

###### Example metadata section
```javascript
metadata: {
  name: "Example Qore Module",
  version: 1,
  "Author": "Authors Name",
  "Support URL": "http://mywebsite.com/",
  "Release Date": "March 14, 2017"
}
```

##### Configuration Section

The configuration object represents environmental and setting data for the module.  Both user-configurable and backend-only settings are defined here.  During installation, any user-configurable settings here will use the values defined here as the default, and telling a setting to restore it's default setting will load from this section.

###### Example configuration section
```javascript
configuration: {
  api_url: {
    configurable: false,
    value: "https://myapiurl.com/endpoint"
  },
  connection_enabled: {
    configurable: true,
    value: 0,
    label: "API Connection Enabled",
    render: "yesno",
    required: true
  },
  api_username: {
    configurable: true,
    label: "API Username",
    render: "text",
    validate: "",
    required: false,
    depends: ["connection_enabled"]
  },
  api_password: {
    configurable: true,
    label: "API Password",
    render: "password",
    validate: "",
    required: false,
    depends: ["connection_enabled"],
    load: "\Qore\Framework\Utility\Password::decodeConfigurableField",
    save: "\Qore\Framework\Utility\Password::encodeConfigurableField"
  },
  calculated_private_value: {
    configurable: false,
    value: "",
    load: "Model\Config::calculatePrivateValue"
  }
}
```

> *****@todo***** Add readme info about individual configuration options

When using Qore's config method to retrieve this data, all settings will be namespaced using the namespace defined in the metadata section.

If the setting "configurable" is set to false, the only required setting is "value".  If "configurable" is true, both "label" and "render" are required, but "value" is not (and will default to null).

All methods called during any configuration will have no arguments passed to them.

##### Routes Section

Qore uses `c9s/Pux` as it's routing handler.

> *****@todo***** Add documentation for how the routing JSON object should be built.

###### Example routes section
```javascript
routes: {

}
```

##### Installer Section

The install object represents what commands build any additional file or data systems required by the module before proper use.  There is also an ability to add scripts that will run on install.  Installation will always execute in the order: filesystem, database, scripts (for each version).

###### Example installer section
```javascript
install: {
  1: {
    'filesystem': {
      'mkdir': ['module-storage','module-cache'],
      'touch': 'special-module-readme.md'
    },
    'database': {
      'module_information': [{
        pgsql: 'CREATE TABLE IF NOT EXISTS ? ( id int(10) auto_increment constraint pk_id primary key )',
        mysql: 'CREATE TABLE IF NOT EXISTS ? ( id int(10) auto_increment, primary key ( id ) )'
      }]
    },
    'scripts': ['Model\Install::extraInstall','Model\Install::requestUserInformation']
  }
}
```

Each entry under the install object must be keyed for the version of the module that each install routine is for.  The install routines will be run in sequence, or (in the case of a module update) only for uninstalled versions.

* **filesystem** entries are a whitelist of commands that can be run.  All paths are relative to the root directory, which should be the same directory qore-cli is run from.  Allowed commands: mkdir, touch
* **database** entries are database updates.  Each entry should be keyed by tablename (so the query builder can properly attach any prefixes and events), with a query or array of queries with a blank binding ("?") for the tablename, optionally organized into objects with keys for each database type.  If a raw query is passed, it will always run regardless of database type.  If a keyed object is passed, and no matching key is found for the working database type, an error will be thrown.
* **scripts** are additional scripts that will run with the installation.  The format should be a class path with reference to a static method in that class.  If the path is not escaped to the root level (\), it will be parsed relative to the module being installed.  

All methods called during installation will have an instance of the Installer model passed as the only argument.

> *****@todo***** Update install method to properly accept tablename array arguments
> *****@todo***** Update whitelist of filesystem commands both in code and in readme
> *****@todo***** Change loader method to JSON
