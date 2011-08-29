<?php

App::import('Core', 'Cache');
App::import('Lib', 'ISocket');

/**
 * CachedSocket is a cacheing front end to an HttpSocket or any class
 * that is derived from it. The idea is that the throttled socket requests
 * which can only occur at a slow frequency are cached. This means that if
 * the same request is made again within the time of the cache timeout, the
 * cached response is returned immediately without the need to do any internet
 * requests.
 *
 * @author Steve Found (DnSMedia)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class CachedSocket extends BaseSocket implements ISocket {

    /**
     * The cache key
     * @var string _cacheKey
     */
    private $_cacheKey;
    
    /**
     * The duration of the cache in seconds
     * @var int _cacheDuration
     */
    private $_cacheDuration;


    /**
     * Create the object and assign a cache key.
     *
     * @param ISocket $socket the socket that we are cacheing
     * @param string $key cache key to use for cached items
     * @param int $duration number of seconds the cached item is valid for
     * @access public
     */
    public function __construct(ISocket $socket, $key, $duration=3600) {
        parent::__construct($socket);
        $this->_cacheKey = $key;
        $this->_cacheDuration = $duration;
    }

    /**
     * Set the number of seconds for which responses should be cached.
     * @param int $duration The cache duration in seconds
     * @access public
     */
    public function setCacheDuration($duration) {
        $this->_cacheDuration = $duration;
    }

    /**
     * Set the cache key
     * @param string $key
     * @access public
     */
    public function setCacheKey($key) {
        $this->_cacheKey = $key;
    }

    /**
     * GET Request a URL.
     * @param mixed $uri URI to request. Either a string uri, or a uri array
     * @param array $query Querystring parameters to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function get($uri=NULL, $query = array(), $request = array()) {
        $response = Cache::read($this->_cacheKey);
        if ($response === false) {
            $response = parent::get($uri, $query, $request);
            if ($response) {
                Cache::set(array('duration' => '+' . $this->_cacheDuration . ' seconds'));
                Cache::write($this->_cacheKey, $response);
            }
        }
        return $response;
    }

    /**
     * POST Request a URL.
     * @param mixed $uri URI to request. Either a string uri, or a uri array
     * @param array $query Querystring parameters to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function post($uri=NULL, $query = array(), $request = array()) {
        $response = Cache::read($this->_cacheKey);
        if ($response === false) {
            $response = parent::post($uri, $query, $request);
            if ($response) {
                Cache::set(array('duration' => '+' . $this->_cacheDuration . ' seconds'));
                Cache::write($this->_cacheKey, $response);
            }
        }
        return $response;
    }

}
?>
