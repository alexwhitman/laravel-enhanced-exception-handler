<?php namespace AlexWhitman\EnhancedExceptionHandler;

use AlexWhitman\EnhancedExceptionHandler\EnhancedExceptionHandler;
use Illuminate\Exception\ExceptionServiceProvider;

class EnhancedExceptionHandlerServiceProvider extends ExceptionServiceProvider {

	public function boot()
	{
		$this->package('alexwhitman/enhanced-exception-handler');
		parent::boot();
	}

	/**
	 * Register the exception handler instance.
	 *
	 * @return void
	 */
	protected function registerHandler()
	{
		$this->app['exception'] = $this->app->share(function($app)
		{
			return new EnhancedExceptionHandler($app, $app['exception.plain'], $app['exception.debug']);
		});
	}

}
