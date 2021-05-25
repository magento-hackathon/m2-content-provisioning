# CHANGELOG

## 1.3.5

* *Improvement:* Add support for full-width cms pages

## 1.3.4

* *Bugfix:* Set correct XSD URN
* *Bugfix:* Pin composer to Version 1

## 1.3.3

* *Bugfix:* Fix return type of convert on error

## 1.3.2

* *Bugfix:* Fix xml schema reference to pass magento static tests

## 1.3.0 / 1.3.1

* *Feature:* Add Magento 2.4 support
* *Feature:* Add phpUnit 9 support

## 1.2.4

* *Feature:* Enable PHP 7.2 support
* *Feature:* Enable PHP 7.3 support
* *Feature:* Add command to add blocks and pages

## 1.2.2 / 1.2.3

* *Bugfix:* `composer.json` blocked installation of the module in Magento 2.2
  ** Change `composer.json` dependency definition
  ** Remove some type and return type hints in order to support Magento's code generation in older versions

## 1.2.1

* *Improvement:* Better extensibility for fetching media files from content (PR from @roma-glushko)

## 1.2.0

* *Feature:* Enables module to provide media files
  ** Introduce optional XML node for pages and blocks: `media_directory`
  ** Media files, which are used in the content will be copied to Magento's `pub/media` directory if they are present in
  the defined source media directory

## 1.1.1

* *Bugfix:* In some cases `bin/magento` execution was not able at all during the initial Magento install

## 1.1.0

* *Feature:* Introduce further interfaces and refactor config reader structure, in order to improve extensibility

## 1.0.4

* *Bugfix:* Backport bugfix from version 1.1.1

## 1.0.3

* *Bugfix:* Fix issue with missing `store_id` values while persisting block entries.

## 1.0.2

* *Bugfix:* Extend schema in order to allow `.` for `key` like it is shown in the examples

## 1.0.1

* *Bugfix:* Add PHP 7.1 compatibility

## 1.0.0

* *Feature:* Notification in Magento backend (admin), for editors - if the content entry is maintained by code
* *Improvement:* Introduce `key` attribute for configured entries, in order to improve merging of all configurations
* *Refactoring:* Improve query for fetching existing cms entities by configured entries

## 0.1.0

* *Feature:* Implement initial functionality
  ** Configuration for pages
  ** Configuration for blocks
  ** Recurring setup installer for pages
  ** Recurring setup installer for blocks
