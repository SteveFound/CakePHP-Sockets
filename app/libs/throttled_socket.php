<?php
App::import('Lib', 'ISocket');

/**
 * Some API's such as Amazon require that your application limits the number of
 * requests it makes. For example no more than 1 per second. If you have a
 * website with more than one client active this can be a problem since many
 * clients could be making simultaneous requests. 
 * This class limits the number of requests by use of a scheduling file.
 * The file is created in the tmp directory of the CakePHP application that is
 * using the object so all clients will access the same file. When file is
 * accessed, it is locked causing any other clients trying to access the file to 
 * wait. The file contains the timestamp of the next request slot. If the
 * timestamp is in the future then there is a queue so the time is incremented
 * and written back to the file. This is the time that the active client can
 * send it's request. If the time is in the past, then the current time
 * is written back to the file and the request can be made immediately. The
 * client then sleeps until it is time to make the request then wakes up to
 * continue with the request.
 *
 * I do dislike the file approach intensely and would have preferred a more
 * elegant solution using a system wide singleton to marshall the requests but
 * since PHP is spawned at the request level, singletons are only singletons to
 * the client, not to the server. It could have been done through serialising
 * the singleton but that would still have involved file locking and would
 * therefore be no more elegant than this.
 *
 * @author Steve Found (DnSMedia)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ThrottledSocket extends BaseSocket implements ISocket {

    /**
     * Filename used for synchronisation
     * @var string
     * @access private
     */
    private $_filename;

    /**
     * Create a Throttled Socket.
     *
     * @param ISocket $socket the socket we are throttling
     */
    public function __construct(ISocket $socket) {
        parent::__construct($socket);
        $this->_filename = ROOT . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'throttle.dat';
        if (!file_exists($this->_filename)) {
            file_put_contents($this->_filename, time());
        }
    }

    /**
     * Issues a GET request to the specified URI, query, and request.
     * @param mixed $uri URI to request. Either a string uri, or a uri array
     * @param array $query Querystring parameters to append to URI
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function get($uri=NULL, $query = array(), $request = array()) {
        $delay = $this->throttle();
        if ($delay > 0) {
            sleep($delay);
        }
        return parent::get($uri, $query, $request);
    }

    /**
     * Issues a POST request to the specified URI, query, and request.
     * @param mixed $uri URI to request. See HttpSocket::_parseUri()
     * @param array $data Array of POST data keys and values.
     * @param array $request An indexed array with indexes such as 'method' or uri
     * @return mixed Result of request, either false on failure or the response to the request.
     * @access public
     */
    public function post($uri = null, $data = array(), $request = array()) {
        $delay = $this->throttle();
        if ($delay > 0) {
            sleep($delay);
        }
        return parent::post($uri, $data, $request);
    }

    /**
     * Introduce a delay. Requests are only allowed to be sent
     * once a second. We achieve this by using a file timestamp which
     * obviously exists system wide. We simply read the timestamp from
     * file and if it is the same as the current time meaning it has been
     * updated within the last second, we go to sleep for a second. The
     * timestamp is then updated by touching the file.
     * @return the number of seconds to wait before request can be made.
     * @access private
     */
    private function throttle() {
        $curtime = time();
        $filetime = $curtime;
        $fp = fopen($this->_filename, "r+");
        if (flock($fp, LOCK_EX)) {
            $nbr = fread($fp, filesize($this->_filename));
            $filetime = intval(trim($nbr));
            $curtime = time();
            if ($curtime > $filetime) {
                $filetime = $curtime;
            } else {
                $filetime++;
            }
            rewind($fp);
            ftruncate($fp, 0);
            fprintf($fp, "%d", $filetime);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return $filetime - $curtime;
    }

}
?>
