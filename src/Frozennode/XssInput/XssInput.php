<?php namespace Frozennode\XssInput;

class XssInput extends \Illuminate\Support\Facades\Input {

	/**
	 * Get an item from the input data.
	 *
	 * This method is used for all request verbs (GET, POST, PUT, and DELETE)
	 *
	 * @param  string $key
	 * @param  mixed  $default
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

}