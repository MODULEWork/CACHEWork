CACHEWork
==========

A simple way of caching information to disk


Installation:
-------------

* Place the cache.php file into your application folder
* Include it
```include_once 'cache.php'; ```
* and initate it: ```Cache::init('cache/');```    


Make sure that you enter the relative path to the cache directory from where the ```init()``` is called! The **MODULE** will do the work for you of determining the absolute path.
This way you can can cache items from everywhere and don' t need to worry of pathes.

* Make sure that PHP can write into the cache folder!
* Make sure that you are **NOT** creating objects of this class!
* Cache some stuff
* Do **NOT** re-init the class.

HowTo
---------

There are 5 methods available for you to interact with your cache.

*  ```put($key, $value, $serialize = true, $override = true)```
*  ```get($key, $value, $expire, $default = null)```
*  ```forget($key)```
*  ```remember($key, $value, $serialize = true, $override = true)```
*  ```clear($expire)```


Everywhere you find something like this ```$var = true```, it' s an optional value. If you don' t pass any information for this variable it will set it to the default value. The default values are the most commonly used.

PUT
---
```put($key, $value, $serialize = true, $override = true)```

This method will store a value into the cache for a undefined time.

* ```$key```: string: the unique key for this cache item. Used for retrieval.
* ```$value```: mixed: The value you want to cache. Objects, arrays, closures are possible.
* ```$serialize ```: bool: ```true``` will serialize the value (not when the value is callable!
* ```$override ```: bool: ```true``` will override any existing cache items.

**Example usage:**

    $variable = file_get_contents('http://google.com');
    Cache::put('key', $variable);

This will store the result of the ```file_get_contents()``` into the cache. But you can even store closures (functions) into the cache like this:


    Cache::put('get_google', function() {
         $localvar = file_get_contents('http://google.com');
         //complicated alogrithem
         return $result;
    });

The ```$result``` will get stored into the cache.

GET
---
```get($key, $expire, $default = null)```

This method will retrieve a value from the cache, but only if it is **NOT** older then ```$expire``` seconds

* ```$key```: string: the unique key for this cache item. Used in the ```put()``` method.
* ```$expire```: int: The time in seconds the file could be old.
* ```$default ```: mixed: The value will get returned if no cache item exists or the cache item is too old.

**Example usage:**

    $key = 'get_google';
    $expire = 60 * 60 * 24; // 1 day
    Cache::get('key', $expire);

This will return the result the closure (used in the previous example) as long as the ```put()``` was not before 1 day.


FORGET
------

This will remove the cache item for the given key.
```Cache::forget('get_google');```
This will remove the file from the disk, forever (a very long time).


REMEMBER
--------

This is the most used method from this class and combines ```put()``` and ```get()```.  
The syntax looks like that:

```Cache::remember($key, $value, $expire, $serialize = true, $override = true);```

As you can see it is using the same parameters as ```get()``` and ```put()```.

An example:

     Cache::remember('get_google', function() {
        return file_get_contents('http://google.com');
     }, 60 * 60 * 24); //1 day


This will save the the contents of ```http://google.com``` into the cache and refreshes the the result every 24 hours (if you are visting the site every 1 hour :D).
This is in most cases the most useful method, because you do not have two write your own refresh code.

CLEAR
-----

This function will clear all cached items older than ```$expire``` seconds.  
```Cache::clear($expire);```

Thats it! Simple and straight forward.


You can always have a look at the PHP doc for a brief explanation.



