<?php namespace Frozennode\XssInput;

class XssInput extends \Illuminate\Support\Facades\Input {

	/**
	 * Get an item from the input data.
	 *
	 * This method is used for all request verbs (GET, POST, PUT, and DELETE)
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @param  mixed $cleanse true = filter, false = don't filter, null = defer to config
	 * @return mixed
	 */
	public static function get($key = null, $default = null, $cleanse = null)
	{
		$value = static::$app['request']->input($key, $default);
		$global_cleanse = static::$app['config']->get('xssinput::xssinput.xss_filter_all_inputs');

		if ( $cleanse === true || ($cleanse === NULL && $global_cleanse) )
		{
			$value = Security::xss_clean($value);
		}

		return $value;
	}

	/**
	 * Get all of the input and files for the request.
	 *
	 * @param  bool		$cleanse
	 *
	 * @return array
	 */
	public static function all($cleanse = null)
	{
		$all = static::$app['request']->all();
		$global_cleanse = static::$app['config']->get('xssinput::xssinput.xss_filter_all_inputs');

		if ( $cleanse === true || ($cleanse === NULL && $global_cleanse) )
		{
			foreach ($all as &$value)
			{
				$value = Security::xss_clean($value);
			}
		}

		return $all;
	}

	/**
	 * Test whether any malicious content was passed as input
	 *
	 * @param null $key
	 *
	 * @return string
	 */
	public static function hasXss($key = null)
	{
		if ($key)
		{
			if (!$raw_value = static::$app['request']->input($key, null)){
				return false; // no input means no XSS!
			}
			$filtered_value = Security::xss_clean($raw_value);
			if ($filtered_value != $raw_value){
				return true;
			}

			return false;
		}

		$all = static::$app['request']->all();
		foreach ($all as $raw_value)
		{
			$filtered_value = Security::xss_clean($raw_value);
			if ($filtered_value != $raw_value){
				return true; // quit at first problem
			}
		}
		return false;
	}

}