VAssetManager ZF2 Module
***************************

Allows to manage the assets and layouts for web application.

Contains also EdpModuleLayouts from https://github.com/EvanDotPro/EdpModuleLayouts

Provides following functionality:
- setting up templates for modules
- setting up CSS and JS files loaded in module headers and footers
- providing CSS and JS files using controller/action (not from public/ directory)



SETTING UP MODULE LAYOUTS

@TODO



SETTING UP CSS AND JS LINKS

Module allows to setup what kind of JS and CSS files needs to be loaded on every page.
This is configured using module.config.php (or any other, customized config) file
in the ['head_values'][modulename] key (where modulename is actual name of the module
for which configuration is stored - e.g. Application) and allows to setup following options:
- title - title that will be setup for every page provided by the module - e.g. 'Application'
- inherit_stylesheets - allows to add modules from which stylesheets should be inherited. These
	stylesheets will be be added as first ones. The value needs to be an array of modules, even
	if there's only one of them - e.g.
	array('Application')
	will inherit stylesheets from Application module
- stylesheets - array of links of stylesheets to be loaded on the page - e.g.
	array(
		'https://fonts.googleapis.com/icon?family=Material+Icons',
		'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
	)
	will load fonts and bootstrap CSS files
- inherit_scripts - allows to add modules from which scripts should be inherited. These scripts
	will be added as first ones. The value needs to be an array of modules, even if there's only
	one of them - e.g.
	array('Application')
	will inherit scripts from Application module
- scripts - array of links to scripts to be loaded on the page - e.g.
	array(
		'https://code.jquery.com/jquery-3.2.0.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js',
		'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
	)
	will load jquery and bootstrap files




SETTING UP ASSETS MANAGEMENT

@TODO