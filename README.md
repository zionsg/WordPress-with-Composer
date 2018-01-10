# WordPress with Composer

This is a sample WordPress skeleton application that demonstrates the use of Composer.

Ideally, WordPress, themes and plugins should be managed as dependencies instead of committing them to source control
and spending inordinate effort to maintain them and keep them in sync with their original source.

Only the child theme may be committed to source control and hence it is shown with this application.

## Requirements
- [PHP](http://php.net/) >= 7.0
- [Composer](https://getcomposer.org) >= 1.5.0

## References
- https://roots.io/using-composer-with-wordpress/
- https://davidwinter.me/install-and-manage-wordpress-with-composer/
- https://deliciousbrains.com/using-composer-manage-wordpress-themes-plugins/

## Installation
- Clone this repo.
- Run `composer install`.
  + WordPress will be installed in the `wp` directory. `wp/wp-config.php` will be created if it does not exist.
  + A `vendor` directory will be created by Composer for storing dependencies.
  + If not all the dependencies have been installed, run `composer install` again.
  + There are scripts run before and after `composer install` to copy out/back the child theme as the `wp` directory
    is overwritten during the process, removing the child theme.
  + Do NOT run `composer update` in production environment. The correct process is to run `composer update` in
    development, check that nothing breaks, commit `composer.lock` to the repository, have the production server pull
    the new `composer.lock` and run `composer install`.
    See https://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file
- Update the values in `wp/wp-config.php` accordingly.

## Caveat Emptor
- If WordPress, themes and plugins are updated via the WordPress admin interface, it will cause `composer.json` to
  be out of sync. Either avoid updating via the admin interface or manually update `composer.json` after updating via
  the admin interface.
- For themes and plugins pulled from repositories other than WordPress Packagist, e.g. from GitHub, the actual files
  for the theme/plugin may not reside in the root of the repository, e.g. in a subfolder. Also, the source code from the repository may need to be built, e.g. build CSS and JS assets or download frontend/backend dependencies.

## Adapting for your project
- WordPress install directory
  + Installed to `wp` directory by default.
  + Edit `.gitignore`.
    * Update all the lines starting with `wp/` and `!wp/` with the preferred directory name, e.g. replace
      `wp/*` with `my-wordpress-dir/*`.
  + Edit `composer.json`.
    * Find the section under the root key `extra`
      - Update `wordpress-install-dir` to the preferred directory name, e.g. `my-wordpress-dir`.
      - Update the paths in the keys under `installer-paths` so that they start with the
        preferred directory name, e.g. the installer path for themes would be changed to
        `"my-wordpress-dir/wp-content/themes/{$name}/": ["type:wordpress-theme"]`.
- WordPress version
  + Edit `composer.json`.
    * Update the version for `johnpbloch/wordpress` under the root key `require`, e.g. `4.*`.
- Child theme directory
  + Installed to `wp/wp-content/themes/child-theme` directory by default.
  + Edit `.gitignore`
    * Update the line `!wp/wp-content/themes/child-theme` with the preferred directory name,
      e.g. `!wp/wp-content/themes/my-grandchild-theme`.
  + Rename the directory `wp/wp-content/themes/child-theme`, e.g. to `wp/wp-content/themes/my-grandchild-theme`.
- Adding a theme
  + If the theme exists on WordPress.org, run `composer require wpackagist-theme/name-of-theme`.
  + If not, edit `composer.json`:
    * Refer to the sample entries for `zionsg/ZnWP-Bootstrap-Theme`. The following steps are to replace the sample
      entries. To add an additional theme, just copy, paste and update the sample entries.
    * Find the entry under the root key `require` for `zionsg/ZnWP-Bootstrap-Theme` and update both the key and value
      with the correct package name and version.
    * Find the repository under the root key `repositories` where `package.type` is `wordpress-theme`.
      - Change `package.name` and `package.dist.url` accordingly.
      - Update `package.version`. If no release version is available for the theme repository, `dev-master`
        can be used to indicate the `master` branch.
- Adding a plugin
  + If the plugin exists on WordPress.org, run `composer require wpackagist-plugin/name-of-plugin`.
  + If not, edit `composer.json`:
    * Refer to the sample entries for `zionsg/znwp-webservice-plugin`. The following steps are to replace the sample
      entries. To add an additional plugin, just copy, paste and update the sample entries.
    * Find the entry under the root key `require` for `zionsg/znwp-webservice-plugin` and update both the key and value
      with the correct package and version.
    * Find the repository under the root key `repositories` where `package.type` is `wordpress-plugin`.
      - Change `package.name` and `package.dist.url` accordingly.
      - Update `package.version`. If no release version is available for the plugin repository, `dev-master`
        can be used to indicate the `master` branch.
- Adding a must-use plugin (mu-plugin)
  + The steps are the same as that for adding a plugin, save that `package.type` is changed to `wordpress-muplugin`,
    which is supported by https://github.com/composer/installers.
  + See https://codex.wordpress.org/Must_Use_Plugins.
- Adding a drop-in
  + The steps are the same as that for adding a plugin, save that `package.type` is changed to `wordpress-dropin`,
    which is supported by https://github.com/composer/installers.
  + See http://wpengineer.com/2500/wordpress-dropins/.
