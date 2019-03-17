# CHANGELOG

## 1.2.0
* *Feature:* Enables module to provide media files
** Introduce optional XML node for pages and blocks: `media_directory`
** Media files, which are used in the content will be copied to Magento's `pub/media` directory if they are
   present in the defined source media directory

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