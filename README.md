# LocalGov Drupal Outpost integration

Provides integration with [Outpost platform Service Directory](https://outpost-platform.wearefuturegov.com/). A submodule provides a content type for data from an Outpost Service, such that it can be put into a LocalGov Directory. Another submodule can import and keep up to date this content using migrations for Services, Categories, Locations and Suitabilities from Outpost.

## Development Status

This is a module in active development. Additional fields will certainly be added. Other content types may also be developed. Use this in testing and development of your site. It is not production ready. As data structures might change treat it as alpha software.

Development tasks and issues are tracked [with the module GitHub repository](https://github.com/localgovdrupal/localgov_outpost).

## Installation

Include the module in your codebase.
```
composer require localgovdrupal/localgov_outpost
```
Prefix this, and further, commands with `ddev` or `lando` etc. as you would normally for your development environment.

If you have not already enable a Database index. LocalGov core includes the database index.
```
drush en localgov_directories_db
```

Enable these modules.
```
drush en localgov_outpost_connector localgov_outpost_service
```

Create a new directory for your Outpost Services. You do this at
**Admin > Content > Add Content > Directory > Add Directory Channel**
`/node/add/localgov_directory`

When creating the directory **Enabled Content types** should be **Outpost service**. With this checked you will see a **Outpost endpoint** field. Enter into this the Outpost Services API URL like `https://example.com/app/v1/services`.

To import, or update, content run
```
drush migrate:import --all --update
```
This may in time be automated.

You can track the status of imports with
```
drush migrate:status
```
If you do not see outpost migrations try clearing cache.
```
drush cr
```

## Submodules

### LocalGov Outpost Service

Provides a pre-configured Service content type to match Outpost. It does not yet have all the fields of outpost. You can also add any fields that are specific to your instance. This content type is automatically configured to be available to LocalGov Directories.

Further development will include more default fields.

### LocalGov Outpost Connector

Provides a default set of migrations which can be configured to collect from an Outpost URI placed on a LocalGov Directory Channel.

Further development will include ways to add mappings for custom fields if required; and possibly methods for automating running the migration.
