# Upgrade from 3.8.* to 4.0.0

This is mostly a cleanup release offering stronger type guarantees for integrators with the changes, but there is no big new feature for end users.
* The minimum supported PHP version is now 8.1.0.

* Updating from 4.2.6 to 5.0.0 doesn't have any impacts. Please read the apropriate UPGRADE-5.0.0 files for updates lower as [5.0.0](UPGRADE-5.0.0.md) to this version.

## Upgrade composer dependencies
replace dependency "behat/symfony2-extension" and "behat/mink-goutte-driver" with "friends-of-behat/mink-browserkit-driver" and
"friends-of-behat/symfony-extension"
```json
        "doctrine/dbal" from "2.5.*" to "^4.0.4",
        "pdepend/pdepend" from "^2.5.2" to "^2.16.2",
        "phpmd/phpmd" from "^2.11.0" to "^2.15.0",
        "phpunit/phpunit" from "^6.5.0|^8.0.0|~9.5.0" to "^11.2.5",
        "sebastian/phpcpd" from "~3.0|~4.0|~5.0|~6.0" to "^2.0.1",
        "squizlabs/php_codesniffer" from "~3.4.0|~3.6.0" to "^3.10.1",
        "consolidation/robo" from "~1.0" to "^4.0.2",
        "mikey179/vfsstream" from "~1.0" to "v1.6.11",
        "symfony/dotenv" from "~3.0|~4.0" to "v6.0.19",
        "symfony/http-kernel" from "~2.0|~3.0|~4.0" to "v4.4.51",
        "behat/mink-extension" from "2.3.*" to "^2.3.1",
        "dmore/chrome-mink-driver" from "2.7.*" to "^2.9.3",
        "dmore/behat-chrome-extension" from "1.3.*" to "^1.4.0",
        "behat/mink-goutte-driver" from "1.1.*" to "dev-master,
        "phpcompatibility/php-compatibility" from "*" to "^9.3.5"
```
### techdivision/import-category 

