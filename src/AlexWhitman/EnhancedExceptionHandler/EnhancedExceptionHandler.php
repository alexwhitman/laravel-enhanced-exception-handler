<?php namespace AlexWhitman\EnhancedExceptionHandler;

use Illuminate\Exception\Handler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class EnhancedExceptionHandler extends Handler {

	/**
	 * All of the seen exceptions.
	 *
	 * @var array
	 */
	protected $seen = array();

	/**
	 * Handle an exception for the application.
	 *
	 * @param  \Exception  $exception
	 * @return void
	 */
	public function handleException($exception)
	{
		$response = $this->callCustomHandlers($exception);

		// If one of the custom error handlers returned a response, we will send that
		// response back to the client after preparing it. This allows a specific
		// type of exceptions to handled by a Closure giving great flexibility.
		if ( ! is_null($response) && ! $response instanceof \Exception)
		{
			$response = $this->prepareResponse($response);
		}

		// If no response was sent by this custom exception handler, we will call the
		// default exception displayer for the current application context and let
		// it show the exception to the user / developer based on the situation.
		else
		{
			$response = $this->displayException($response);
		}

		return $this->prepareResponse($response);
	}

	/**
	 * Handle the given exception.
	 *
	 * @param  Exception  $exception
	 * @param  bool  $fromConsole
	 * @return void
	 */
	protected function callCustomHandlers($exception, $fromConsole = false)
	{

		// We'll keep track of the exceptions that we see. If we've already seen
		// an exception previously it indicates that we're in a recursive loop.
		// If this happens we'll just bail with a formatted exception.
		$class = get_class($exception);

		if (in_array($class, $this->seen))
		{
			$response = $this->formatException($exception);

			return $response;
		}

		$this->seen[] = $class;

		foreach ($this->handlers as $handler)
		{
			// If this exception handler does not handle the given exception, we will just
			// go the next one. A handler may type-hint an exception that it handles so
			// we can have more granularity on the error handling for the developer.
			if ( ! $this->handlesException($handler, $exception))
			{
				continue;
			}
			elseif ($exception instanceof HttpExceptionInterface)
			{
				$code = $exception->getStatusCode();
			}

			// If the exception doesn't implement the HttpExceptionInterface, we will just
			// use the generic 500 error code for a server side error. If it implements
			// the HttpException interfaces we'll grab the error code from the class.
			else
			{
				$code = 500;
			}

			// We will wrap this handler in a try / catch to attempt to handle any exceptions.
			try
			{
				$response = $handler($exception, $code, $fromConsole);
			}
			catch (\Exception $exception)
			{
				$response = $this->callCustomHandlers($exception, $fromConsole);
			}

			// If this handler returns a "non-null" response, we will return it so it will
			// get sent back to the browsers. Once the handler returns a valid response
			// we will cease iterating through them and calling these other handlers.
			if (isset($response) and ! is_null($response))
			{
				return $response;
			}
		}

		return $exception;
	}

}
