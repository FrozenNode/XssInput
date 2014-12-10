# XssInput for Laravel

XssInput is a screamingly simple extension of Laravel's Input facade that somewhat mimics the XSS filtering of CodeIgniter's input library. In fact, underneath the hood, this package uses an altered form of CodeIgniter's Security library to filter inputs for XSS.

XSS filtering happens in one of two ways: by setting the `xss_filter_all_inputs` option in this package's config to `true`, or by passing true as the third option to `Input::get()` or as the only option for `Input::all()`.


- **Author:** Jan Hartigan
- **Website:** [http://frozennode.com](http://frozennode.com)
- **Version:** 1.1.0

## Version

Version 1.1.0 is compatible with Laravel 5.  If you are using Laravel 4, please use version 1.0.0.

## Composer

To install XssInput as a Composer package to be used with Laravel 4, simply add this to your composer.json:

```json
"frozennode/xssinput": "1.0.*"
```

Then run `composer update`. Once it's installed, you can register the service provider in `app/config/app.php` in the `providers` array:

```php
'providers' => array(
    'Frozennode\XssInput\XssInputServiceProvider',
)
```

..and change the `Input` alias to point to the facade for XssInput:

```php
'aliases' => array(
	'Input' => 'Frozennode\XssInput\XssInput'
)
```

You could also, instead of doing this, give the XssInput facade a separate alias.

Then publish the config file with `php artisan config:publish frozennode/xssinput`. This will add the file `app/config/packages/frozennode/xssinput/xssinput.php`, which you should look at and understand because it's one option long.

## Usage

It really is screamingly simple. If you've set the global xss filtering to `true`, then you can continue using the Input facade as you normally would:

```php
Input::get('some_var');
```

The same goes for getting all inputs:

```php
Input::all();
```

However, if you don't have global xss filtering on, you can pass in a third parameter to the `get()` method:

```php
Input::get('some_var', null, true);
```

Or pass in `true` to the `all()` method:

```php
Input::all(true);
```

If you have global filtering on, you can pass `false` in as these parameters to turn off filtering for that particular call to either method.

## Additional Functions

### hasXSS

```
bool Input::hasXss ( [string $key = null])
```

Version 1.1.0 adds a an `hasXss()` function, which is useful for testing whether a given input has potentially malicious content.
This could be used to adapt application-level firewall rules or perform custom logging/reporting.


```php
if (Input::hasXss('some_var'))
{
   // hmm... Input::get('some_var') looks fishy 
{
```

By omitting the `$key` parameter, you check all inputs:

```php
if (Input::hasXss())
{
   // hmm... something in Input::all() looks fishy 
{
```

Note: when `hasXSS` returns true, this is not a guarantee that the input data was malicious: it merely indicates that the input was filtered.  If the data had been entirely "safe", it would not have been filtered. 