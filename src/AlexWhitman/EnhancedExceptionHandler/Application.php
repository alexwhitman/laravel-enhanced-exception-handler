<?php namespace AlexWhitman\EnhancedExceptionHandler;

use Illuminate\Foundation\Application as BaseApplication;
use AlexWhitman\EnhancedExceptionHandler\EnhancedExceptionHandlerServiceProvider;

class Application extends BaseApplication {

	/**
	 * Register the exception service provider.
	 *
	 * @return void
	 */
	protected function registerExceptionProvider()
	{
		$this->register(new EnhancedExceptionHandlerServiceProvider($this));
	}

}
