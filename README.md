Release Manager
===============

[![Build Status](https://secure.travis-ci.org/bonndan/ReleaseManager.png?branch=master)](https://travis-ci.org/bonndan/ReleaseManager)

Release Manager is a simple PHP tool to help releasing new semantic versions. It uses your composer
file to store and retrieve information.

You can define a list of actions that will be executed and before or after the release of a new version
 and where you want to store the version (in a changelog file, as a VCS tag, etc…).


This is a fork of Liip's Relase Management Tool [RMT](https://github.com/liip/RMT). Kudos to the original authors for this tool.



Installation
------------

In order to use RMT your project should use [Composer](http://getcomposer.org/) as RMT will be installed as a dev-dependency. Just go on your project root directory and execute:

    php composer.phar require --dev bonndan/ReleaseManager 0.2.*         # lastest stable
    # or
    php composer.phar require --dev bonndan/ReleaseManager dev-develop    # lastest unstable

Then you must initialize RMT by running the following command:

    php vendor/bonndan/ReleaseManager/command.php init

This command will create for you a `extra/rmt` section in your composer.json. You
should adapt the configuration to your needs. A good example is the [composer file
of this project] (https://github.com/bonndan/ReleaseManager/blob/master/composer.json).

From that point on you can start using it, just execute it:

    ./RMT


Usage
-----
Using RMT is very straightforward, you just have to run the command:

    ./RMT release

RMT will then do the following tasks:

* Execute the prerequisites checks
* Ask the user to answers potentials questions
* Generate a new version number
* Execute the pre-release actions
* Persist the new version number
* Execute the post-release actions

### Additional commands

The `release` command is the main behavior of the tool, but some extra commands are available:

* `current` will show your project current version number (alias version)
* `init` create rmt.json config file

Configuration
-------------

All RMT configuration have to be done in the `rmt.json`. The file is divided in 5 root elements:

* `vcs`: The type of VCS you are using, can be `git`, `svn` or `none`
* `prerequisites`: A list `[]` of prerequisites that must be matched before starting the release process
* `preReleaseActions`: A list `[]` of actions that will be executed before the release process
* `versionPersister`: The persister to use to store the versions (mandatory)
* `postReleaseActions`: A list `[]` of actions that will be executed after the release

All the entries of this config are working the same way: You have to specify the class you want
 to handle the action or provide an abbrevation for classes provided by ReleaseManager.:

* The config array, example:  `"versionPersister": {"name": "vcs-tag"}` when you have to provide parameters to the class.

### Semantic Version Generator

ReleaseManager only allows semantic versions without prefixes. See (Semantic versioning)[http://semver.org].
The release version can be increased by:

* major
* minor
* patch
* build number

### Version persister

Class is charged of saving/retrieving the version number

* vcs-tag: Save the version as a VCS tag
* changelog: Save the version in the changelog file
* composer: uses the version from the composer file

### Prerequisite actions

Prerequisite actions are executed before the interactive part.

* working-copy-check: Check that you don't have any VCS local changes before release
* display-last-changes: display your last changes before release

### Actions

Actions can be used for pre or post release parts.

* changelog-update: Update a changelog file
* vcs-commit: Process a VCS commit
* vcs-tag: Tag the last commit
* vcs-publish: Publish the changes (commit and tags)
* composer-update: Update the version number in a composer file

Extend it
---------

RMT is providing a large bunch of existing actions, generator and persister. But if you need, 
you can create your own. Make sure it implements Liip\RMT\Action\ActionInterface.


    "versionPersister": {"name": "\\My\\Namespace\\FooAction", "parameter1": "value1"}


Configuration examples
----------------------
Most of the time, it will be easier for you to pick up and example bellow and to adapt it to your needs.

### No VCS, changelog updater only

```
{
    "versionPersister": "changelog"
}
```

### Using Git tags, simple versioning and prerequisites
```
{
    "vcs": "git",
    "versionPersister": "vcs-tag",  
    "prerequisites": [
        "working-copy-check",
        "display-last-changes"
    ]
}
```

### Using Git tags with semantic versioning and pushing automatically
```
{
    "vcs": "git",
    "versionPersister": {
        "type" : "vcs-tag",
    },
    "postReleaseActions": [
       "vcs-publish"
    ],
}
```


Contributing
------------
If you would like to help, to submit one of your action script or just to report a bug:
 just go on the project page: https://github.com/bonndan/ReleaseManager

Requirements
------------

PHP 5.3
Composer

Authors
-------

* Laurent Prodon Liip AG
* David Jeanmonod Liip AG
* Daniel Pozzi

License
-------

RMT is licensed under the MIT License - see the LICENSE file for details
