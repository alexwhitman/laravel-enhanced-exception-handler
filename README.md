# Enhanced Exception Handler (for Laravel)

Provides a slightly enhanced exception handler to allow the re-throwing of exceptions.

## Why?

Imagine you have the following code:

```
App::error(function(Illuminate\Database\Eloquent\ModelNotFoundException $exception)
{
	throw new Symfony\Component\HttpKernel\Exception\HttpException(404, null, $exception);
});

App::error(function(Symfony\Component\HttpKernel\Exception\HttpException $exception)
{
	return View::make('404');
});

Route::get('/', function() {
	$user = User::findOrFail(100);
});

```

If user 100 doesn't exist an `Illuminate\Database\Eloquent\ModelNotFoundException` will be thrown and caught by the first exception handler.
The handler will then try to thrown a new `Symfony\Component\HttpKernel\Exception\HttpException` but the core framework will stop at this point
and echo the message "Error in exception handler".  This package changes the exception handling to allow throwing of exceptions from within a handler
and in the above example will result in the 404 view being returned.

### Recursion

By allowing recursive handling of exceptions it's possible to get stuck in a loop if the handler for `FooException` throws `BarException` and then the
handler for `BarException` throws `FooException`.  Because of this the handler will keep track of the exceptions it has previously seen and bail if the
same type of exception is seen more than once.

## Installation

Add `alexwhitman/enhanced-exception-handler` to the `require` section of your `composer.json` file.

`"alexwhitman/enhanced-exception-handler": "1.2.x"`

Run `composer update` to install the latest version.

## Setup

The core exception handler service provider is registered very early in the process and so can't be replaced by simply changing the service provider
registered in `app/config/app.php`.  Instead, a new `Application` object is required to register the new handler.

To use the new `Application` object, update `bootstrap/start.php` and replace
`$app = new Illuminate\Foundation\Application;` with `$app = new AlexWhitman\EnhancedExceptionHandler\Application;`.

## Changelog

### 1.2.0

- Update for Laravel 4.2

### 1.1.1

- Fix for laravel/framework >= 4.1.25

### 1.1.0

- Update for Laravel 4.1

### 1.0.0

- Initial release
