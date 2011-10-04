<?php

/**
 * ISocket interface
 *
 * @author Steve Found (DnSMedia)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface ISocket {

    /**
     * Issues a PUT request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request
     * @param array $data Array of PUT data keys and values.
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request
     * @access public
     */
    public function put($uri = null, $data = array(), $request = array());

    /**
     * Issues a DELETE request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request
     * @param array $data Query to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request
     * @access public
     */
    public function delete($uri = null, $data = array(), $request = array());

    /**
     * Issues a GET request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request. Either a string uri, or a uri array
     * @param array $query Querystring parameters to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function get($uri=NULL, $query = array(), $request = array());

    /**
     * Issues a POST request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request.
     * @param array $data Array of POST data keys and values.
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function post($uri = null, $data = array(), $request = array());
}

abstract class BaseSocket implements ISocket {

    /**
     * The socket we are wrapping
     * @var ISocket $_socket;
     */
    private $_socket;

    public function  __construct( ISocket $socket ) {
        $this->_socket = $socket;
    }

    /**
     * Issues a DELETE request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request
     * @param array $data Query to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request
     * @access public
     */
    public function  delete($uri = null, $data = array(), $request = array()) {
        return $this->_socket->delete($uri,$data,$request);
    }

    /**
     * Issues a POST request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request.
     * @param array $data Array of POST data keys and values.
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function post($uri = null, $data = array(), $request = array()) {
        return $this->_socket->post($uri,$data,$request);
    }

    /**
     * Issues a GET request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request. Either a string uri, or a uri array
     * @param array $query Querystring parameters to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function get($uri = NULL, $query = array(), $request = array()) {
        return $this->_socket->get($uri,$query,$request);
    }

    /**
     * Issues a PUT request to the specified URI, query, and request.
     *
     * @param mixed $uri URI to request
     * @param array $data Array of PUT data keys and values.
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request
     * @access public
     */
    public function put($uri = null, $data = array(), $request = array()) {
        return $this->_socket->put($uri,$data,$request);
    }
}
?>
