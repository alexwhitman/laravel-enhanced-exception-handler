<?php namespace AlexWhitman\EnhancedExceptionHandler;

use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Http\Request;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use AlexWhitman\EnhancedExceptionHandler\EnhancedExceptionHandlerServiceProvider;

class Application extends BaseApplication {

	/**
	 * Create a new Illuminate application instance.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	public function __construct(Request $request = null)
	{
		$this['request'] = $this->createRequest($request);

		// The exception handler class takes care of determining which of the bound
		// exception handler Closures should be called for a given exception and
		// gets the response from them. We'll bind it here to allow overrides.
		$this->register(new EnhancedExceptionHandlerServiceProvider($this));

		$this->register(new RoutingServiceProvider($this));

		$this->register(new EventServiceProvider($this));
	}

}
