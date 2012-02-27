# FuelPHP 2.0-alpha

This is very early alpha development of the next generation of FuelPHP. Almost nothing is fixed at this point thus it
is very much not recommended for production use.
While this is written anew from the ground up, it still very much relies on v1.x for much of its functionality.

## Important changes

Below I'll keep a list of important techniques/patterns implemented.

### Everything is a Package and they'll be routable

In v1.x the Application, Core and Oil packages weren't normal packages and didn't follow the same rules as other
packages. As of 2.0 the Core will be divided up into the absolute minimum one needs to run Fuel (the 'Kernel', without
any dependencies) and the Core that will contain important extensions and utility classes. The Kernel could be
considered a micro-framework upon which the Core is build.

Applications will have wrapper classes that get instantiated to work with the Package as an Application instead of a
normal package. All packages that are loaded into an Application may be routable within that application, but only
if you mark them as such.
Defining of the routes has also been moved outside the config dir and into the Application class.

### Unified and Package based Class and File loading

Instead of separate loaders for classes and files have these been replaced by Package Loader objects. These will handle
both the classloading of the autoloader and the fileloads.

### Dependency Injection Containers and class extensibility

First of all we'll keep the global namespace clean from now on (except when you load legacy support). But we like the
way you can alias classes to global from the Core and extend them in a Package later without editing the Application.
For that reason we still provide these aliases but they're aliased to a `Classes\` namespace.

Next we implemented a DiC. This will be available globally and Application specific, where the Application DiC will
fallback to the global DiC when something unknown is requested. Below are the three most important methods:

```php
<?php

$session_classname = $dic->get_class('Session');
// Can return whatever you configure as the default Session driver, for example 'Fuel\\Core\\Session\\Cookie'.
// When the class is unknown it returns the given classname unmodified.

$session = $dic->forge('Session');
// Returns an instance of the class that get_class() returns
// Has a global function _forge() available that calls this on the active Application's DiC

$session = $dic->get_object('Session', 'name');
// Retrieves an object from the DiC for a specific class, without the 'name' param it will
// create a default instance when one isn't available yet. Named objects must be registered
// using $dic->set_object($class, $name) or they'll throw an Exception
```

### Environment superobject

The initialization of Fuel has been divided up into setting up the Environment and only after that is the Application
initialized. The environment settings entail all those settings that should not or cannot be changed while an
Application is running but should instead be considered fixed.

### Simplified Modules and possible in all packages

The concept of Modules has been simplified into a way of structuring your files inside the Package directory. All Views,
Configs and Language files from a Module are considered the same as those from the parent Package (the latter taking
precedence). The concept of an 'active' Module has been removed (of which the filepath took precedence over the
Application).
They're also no longer limited to the Application directory and can be put into any Package. They must be a
subnamespace inside that Package and be a subdirectory in the `modules` directory of that Package.

### Oil reimplemented as an Application with Tasks as specialized Controllers

Oil is now an Application within the new setup. This means it has an Application wrapper class and its own internal
routing. Its own Front Controller is still named `oil` (without extension) and has almost no differences with the
normal `index.php` FC.

Tasks have been reimplemented as specialized Controllers which means their methods require an 'action_' prefix and
they're inside a subnamespace of the Application. The routing to these Tasks works through normal routing means but
requires specialized Routes that route to Tasks instead of Controllers.
All Oil command-classes have been reimplemented as Tasks.

### File structure reordering

The number of subdirectories per package has been increasing and we felt this has become less clear. We have decided
to move the less edited/used files into a 'resources' subdirectory.

* In the package folder itself you'll find: classes, config, languages, modules, resources and views.
* In the resources folder you'll find: migrations, tests and vendor. In Application Packages you'll also have: cache,
  logs and tmp.

### Decentralized Config and Language

Even though both Config and Language had a concept of 'groups' they were just 1 massive container per Application. This
is no longer the case. Each Application will have a main Config object that can access its 'children' much like
before, but those children will be separate objects which can be accessed on their own and are part of the class that
loaded them.
The Config and Language classes have also both become extensions of the new Data superclass as they share most of their
functionality.

### Adoption of industry standards like Composer/Packagist and PSR-0

By default Fuel will now use the PSR-0 standard for classloading. Its implementation is actually a superset, but it is
fully compatible with PSR-0. For convenience and speed each Package can be given a base namespace that will be required
and stripped before any class to path conversion takes place. The same goes for modules inside the Package, those
will have subnamespaces that are required and stripped before conversion as well.

Version 2.0 will also include Composer and will be able to include packages from Packagist. How this will end up
working is very much in development still.

### ViewModel becomes Presenter

As the name lead to much confusion and discussion about CamelCasing of the name we decided that it is time for a better
name. ViewModel is the name the concept has in MVVM (Model-View-ViewModel), but it is about as similar to the MVP
(Model-View-Presenter) concept and that provides better clarity about its function. They will remain very much optional
though and Fuel will remain MVC.
The Presenter class has also become a superset of the View class instead of wrapping a View object.

### Parser package integrated into the Kernel/Core

As the parsing of strings is necessary throughout Fuel and not just in Views this needs to be available everywhere for
both files and free input. This the new Parser classes concept into the Kernel which has just PHP support, the Core
however adds Markdown and Twig out of the box.

### Migrations as objects

Migrations will no longer be classes but instead they'll be objects. An example is given below:

```php
<?php

return _forge('Migration')
	->up(function() {
		// do some DB transactions
	})
	->down(function() {
		// undo some DB transactions
	});
```

## Legacy support

We'll provide as much backwards compatibility as is possible by offering an extra package that extends many classes to
mimic how they worked before or provides a static interface to a core class. Some things will need a bit of search &
replace, though doable project wide if your IDE/texteditor is capable of that. Other things will require a little bit
of rewriting, migrations as mentioned are an example of that.
And a few things will not be backwards compatible, especially core extensions are likely to break in a way that will
require extensive rewriting.

Once we go into beta we will provide two guides on updating:

1. With legacy package activated
2. Without the legacy features actived
