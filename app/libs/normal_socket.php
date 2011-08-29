<?php
App::import('Lib', 'ISocket');
App::import('Lib', 'HttpSocket');

/**
 * A Simple wrapper for HttpSocket which implements our interface so we can
 * feed it to our other classes.
 *
 * @author Steve Found (DnSMedia)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class NormalSocket extends HttpSocket implements ISocket {

    public function __construct($config = array() ) {
        parent::__construct($config);
    }
}
?>