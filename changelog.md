# Changelog

## 0.2.4 - Changed package and repo name

* Changed to package name according to packagist.org conventions [c008dc0]

## 0.3.0 - Script execution action

* updated the readme [17e9b86]

## 0.4.0 - New changelog, new "changes" command, refactorings

* small markdown fix [fcfcbc8]
* fixed git command return value handling [20c03cc]
* Set back version in composer.file [30d1084]
* fixed the config [b9d89cb]
* Merge branch 'feature/xml_changelog' into develop [d6e9048]
* updated configuration [4baff48]
* renamed the default changelog file [4d919fb]
* changelog render action [ce65524]
* changelog formatter for simple markdown [771cc82]
* changelog provides convenience methods to extract data [b37a6b8]
* removed the semantic changelog formatter [49d28a6]
* example changelog.xml [2551dd9]
* refactored the changelog update action [0852644]
* typo fix [2ffa53a]
* typo fix [ff2abdd]
* code cleanup [b6355b3]
* removed the changelog manager and the functional test [6e6e8ff]
* changelog persister uses the changelog [4ada2d8]
* added assertion [27d75d8]
* changelog can return the current version [35e533d]
* added comment [8c97f13]
* xml based changelog changelog [5513b8d]
* xml test fixture [437a05e]
* Merge branch 'feature/display_last_changes' into develop [7b33ed1]
* Release of new version 0.3.0-1 [a5efefc]
* updated the readme [17e9b86]
* added functional test [b748267]
* registered the "changes" command [53ba699]
* command that shows the last changes [677a68f]
* code cleanup [dc1bddc]
* Merge branch 'feature/improved_autoloading' into develop [697d700]
* code cleanup [045a640]
* prerequisites are treated like normal actions [db5584d]
* Merge branch 'release/0.3.0' into develop [4ee3469]

## 0.4.1 - Fixed #3

* Merge branch 'feature/3_changelog_version_vcs' into develop [3be8844]
* redirected output in prerequisites test [fd864c9]
* #3 vcs exception is caught, commits not written to changelog [72debc6]
* Merge branch 'master' into develop [7f39753]
* Update README.md [84000ec]
* Merge branch 'release/0.4.0' into develop [dd7af21]

## 0.4.2 - Bugfix for #5

* Merge branch 'feature/5_no_release_exception' into develop [726ae9c]
* improved exception message [0527f10]
* fixed the project description [b4ce76b]
* fixed the composer persister [6d91e5e]
* using the version class in other classes [ff170cd]
* using the version class in other classes [198ddf9]
* using the version class with vcs [fe4a505]
* introducing a getter and setter for the new version [b46fbf6]
* introducing a version class [6598942]
* fixed indentation [51b9154]
* Merge branch 'release/0.4.1' into develop [908d65c]

## 0.4.3 - fixed #9

* fixed #9 [43765f8]

## 0.4.4 - fixed type hints

* fixed type hints [af13316]

## 0.4.5 - build fix

* added composer-update action [728a931]
* Build fix [b95f938]
* Merge branch 'master' into develop [14d49fb]
* Merge branch 'release/0.5.1' into develop [4625bb5]

## 0.5.4 - Workaround for #11

* Attempt to hide git flow output [cb0903b]
* #11 removed vcs-tag-action workaround, omitting tag message instead [4d1ee52]
* fixed finish command [3138a2b]
* #11 getopt bug workaround [6384119]
* version stamp action confirms success [fb8e003]
* improved a test [0daf13a]
* Merge branch 'feature/vcs_commit_check' into develop [b188910]
* Removed the -F option from release/hotfix finishing actions [a784337]
* fixed test [260dc6a]
* finish command test [73a3a20]

## 0.5.5 - bugfix

* #12 an existing post-release commit action is toggled to fail gracefully [bd222bd]
* #12 the automatically added post - release commit action fails gracefully [dc2325b]

## 0.5.6 - bugfixes

* Skipped a test [1b70bc4]
* Fixed a test mock issue [f21debc]
* test fix [8c993b0]
* Finish command throws exception of no difference can be detected [c89dcab]
* Fixed a test that sometimes fails [9b23f5f]
* fixed typo [7b79977]
* #15 start and hotfix commands throw exceptions if manual tagging is configured [de5fdd5]
* finish commands throws exceptions when manual tagging is active [7da81d4]
* extraced a command test case [06f0664]
* #12 all vcs-commit actions are set to fail gracefully [8f3b8f3]
* Merge branch 'hotfix/0.5.5' into develop [ee36938]
* Merge branch 'release/0.5.4' into develop [b2c12a7]
