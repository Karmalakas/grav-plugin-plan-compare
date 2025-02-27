# Plan Compare Plugin

**This README.md file should be modified to describe the features, installation, configuration, and general usage of the plugin.**

The **Plan Compare** Plugin is an extension for [Grav CMS](https://github.com/getgrav/grav). Create tables to compare features among different service plans or products 

## Usage

When creating a page (normal or modular), select a `plan-compare` template, and you will have a "Plan compare" tab in the Admin panel. There you will see 2 more tabs:
- Lists - create your feature and plan lists
- Table - after saving the lists, you will see a table with inputs for comparison content

## Configuration

Before configuring this plugin, you should copy the `user/plugins/plan-compare/plan-compare.yaml` to `user/config/plugins/plan-compare.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
built_in_css: true
```

Note that if you use the Admin Plugin, a file with your configuration named plan-compare.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Installation

Installing the Plan Compare plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](https://learn.getgrav.org/cli-console/grav-cli-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav-installation, and enter:

    bin/gpm install plan-compare

This will install the Plan Compare plugin into your `/user/plugins`-directory within Grav. Its files can be found under `/your/site/grav/user/plugins/plan-compare`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `plan-compare`. You can find these files on [GitHub](https://github.com//grav-plugin-plan-compare) or via [GetGrav.org](https://getgrav.org/downloads/plugins).

You should now have all the plugin files under

    /your/site/grav/user/plugins/plan-compare
	
> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yaml-file on GitHub](https://github.com//grav-plugin-plan-compare/blob/main/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## To Do

- [ ] Responsive Admin grid

