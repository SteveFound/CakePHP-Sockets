When trying to use the Amazon API, I read a lot about the Amazon rule of only 
being allowed to make 1 request a second or your web site would be refused 
access to Amazon if this was ignored too often.

The obvious solution to this is to get your website to ensure that no more than
1 request a second is made. Normally, I would approach this through a system 
wide singleton object that marshalls all outgoing requests but as PHP is spawned
on a request level, the system wide singleton is not possible since each client
would have its own singleton. I therefore decided on a locked file approach that
would marshall the requests being made.

The system presented here consists of 3 classes and an interface. 

The interface ISocket simply outlines a get and post method as implemented by 
HttpRequest in CakePHP. It is a shame that HttpRequest does not already have an
interface that it implements... Interfaces are something that would be a nice 
addition to CakePHP 2.0 maybe.

The class NormalSocket simply extends HttpSocket and implements ISocket which
enables it to be used by ThrottledSocket and CachedSocket. Other than this, it
is identical to HttpSocket supplied by CakePHP.

`App::import( 'Lib', 'NormalSocket' );`

...

```php
/* Create a NormalSocket which has the same usage as HttpSocket */
$ns = new NormalSocket();
/* Requests to Throttled socket now happen only once per second */
$response = $ns->get($uri);
```

ThrottledSocket throttles another ISocket implementing object which is 
passed in the constructor. For example :-

```php
App::import( 'Lib', 'ThrottledSocket' );
App::import( 'Lib', 'NormalSocket' );
```

...

/* Create a NormalSocket which has the same usage as HttpSocket */
$ns = new NormalSocket();
/* Throttle the NormalSocket */
$ts = new ThrottledSocket($ns);
/* Requests to Throttled socket now happen only once per second */
$response = $ts->get($uri);



CachedSocket caches another ISocket implementing object which is passed
in the constructor. The class uses the standard CakePHP Cacheing 
mechanism to cache the responses returned by the wrapped socket.
For example:

```php
App::import( 'Lib', 'CachedSocket' );
App::import( 'Lib', 'NormalSocket' );
```

...

```php
/* Create a NormalSocket which has the same usage as HttpSocket */
$ns = new NormalSocket();
/* Cache the NormalSocket with the key 'CacheKey' for 1 hour */
$cs = new CachedSocket($ns, 'CacheKey', 3600 );
/* Requests to Cached socket now return the cached response for the next hour */
$response = $cs->get($uri);
```

Using this system of wrapping other Socket objects we can cache throttled requests
which is ideal for accessing Amazon. Once a request is made, the response will be
cached so amazon requests are only made when necessary and they will not occur more
than once per second.

```php
App::import( 'Lib', 'CachedSocket' );
App::import( 'Lib', 'ThrottledSocket' );
App::import( 'Lib', 'NormalSocket' );
```php

....

```php
function GetBookByAsin( $asin ) {
    $ts = new ThrottledSocket( new NormalSocket() );
    $amazonSocket = new CachedSocket( $ts, 'ASIN' . $asin, 3600 );
    /* Build amazon request URL */
    $response = $amazonSocket->get($url);
    /* Process XML returned by Amazon */
}
```

The first time the example function is called it will make a throttled
request to amazon and cache the response. For the next hour any requests
for the same book will return the cached response and not make any calls 
to amazon at all.

