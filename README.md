
# Security Audit Log Plugin

Simple plugin to log user activities like login in/out, post/page edit etc.


## Installation

Just copy the plugin directory to your WordPress must use plugins directory. In most cases it's `wp-content/mu-plugins`.

If your theme don't support autoload `mu-plugin` from subdirectory then create and use simple loader.

In the `mu-plugins` directory create file called `loader.php` and place this code inside of it.

```php
<?php include 'security-audit-log/security-audit-log.php'; ?>
```
Then the plugin should be activated and appear in the WordPress menu.
## Available events

- User Log-in
- User Log-out
- User post/page edit


## Additional features

- Search field
- Pagination
- Show X records per page

## Authors

- [@RichForever](https://github.com/RichForever)


## Changelog

- 1.0 - initial version


## License

[MIT](https://choosealicense.com/licenses/mit/)

