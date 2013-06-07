<?php
namespace MODULEWork;
/*===================================================
*
*
*
* Name: Cache Class
* Version: 1.1
* License: Apache 2.0
* Author: Christian Gärtner
* Author URL: christiangaertner.github.io
* Project URL: https://github.com/ChristianGaertner/MODULEWork/tree/master/CACHEWork
* Description: This class can cache any value (even whole functions) to a flatfile database and can retrieve the values
*
*
*
===================================================*/


/**
* Caches a certain function
*/
class Cache
{
	/**
	* The path for the class to cache
	*/
	protected static $_path;

	/**
	* Sets important variables, e.g. the cache directory
	* @param $path (with trailing slash)
	* @return void
	*/
	public static function init($path) {
		$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR; //remove every slash and then add one, so there is one ALWAYS
		$abs = realpath(dirname(__FILE__)); //get absolute path, so the class can be used from anywhere
		static::$_path = $abs . DIRECTORY_SEPARATOR . $path; //save the new path to the static
	}
	
	/**
	* Cached the given value to disk with the given key.
	* @param string;  unique key needed for retrieving the value
	* @param mixed (closures possible); This will get stored to disk
	* @param boolean default:true; This will tell whether we should override existing files
	* @return void
	*/
	public function put($key, $value, $serialize = true, $override = true)
	{
		$key = md5($key);


		if (is_callable($value)) {
			$data = $value();
		} elseif ($serialize) {
			$date = serialize($value);
		} else {
			$data = $value;
		}
		
		$put = false; //this will tell later on, if the value should be stored to disk!
		if (file_exists(self::$_path . $key) && $override) {
			$put = true;

		} elseif (!file_exists(self::$_path . $key)) {
			$put = true;
		}

		if ($put) {
			file_put_contents(self::$_path . $key, $data);
		}

	}

	/**
	* Retrieves the value for the given key.
	* @param string; the unique key used in the put() method
	* @param int; the maxium time the cache item is allowd to exists, in seconds
	* @param mixed default:NULL; the return value, if the key doesn' t exists or is outdated
	* @return mixed; content of the cache OR if no cache exists or outdated: NULL
	*/
	public function get($key, $expire, $default = null)
	{
		$key = md5($key);
		if (static::validateItem($key, $expire)) {
			return file_get_contents(self::$_path . $key);
		}


		return $default; //if file doesn' t exist return the default
	}

	/**
	* Deletes the cache item for the given key.
	* @param string; the unique key used in the put() or get() method
	* @return void
	*/
	public function forget($key, $pre_hash=false)
	{
		if (!$pre_hash) {
			$key = md5($key);
		}
		if (file_exists(self::$_path . $key)) {
			unlink(self::$_path . $key);
		}
	}

	/**
	* Combines put() and get() automagiclly into one. If a doesn' cache exists it will get created
	* @param string;  unique key needed for retrieving the value
	* @param mixed (closures possible); This will get stored to disk
	* @param int; how old can be the cache in order to get returned in seconds
	* @param boolean default:true; This will tell whether we should override existing files
	* @return mixed; content of the cache OR if no cache exists or outdated: NULL
	*/
	public function remember($key, $value, $expire, $serialize = true, $override = true)
	{
		if (self::get($key, $expire) == null) {
			self::put($key, $value, $serialize, $override);
		}
		return self::get($key, $expire);
		
		
	}
	/**
	* Cleares all cached item older then $expire seconds
	* @param int $expire in seconds
	*/
	public function clear($expire)
	{
		foreach (glob(static::$path) as $item) {
			if (static::validateItem($item, $expire)) {
				static::forget($item, true);
			}
		}
	}

	/**
	 * Checks if the cache item is "valid"
	 * @param  string $key    The key of the item in the cache
	 * @param  int $expire 		Max age for the item in seconds
	 * @return boolean         TRUE if the item is valid
	 */
	protected static function validateItem($key, $expire)
	{
		if (file_exists(self::$_path . $key) && (time() - @filemtime(self::$_path . $key) < $expire)) { // @filemtime so we do not get any erros if the file doesn' t exists
			return true;
		} else {
			return false;
		}
	}
	
}
