<?php
App::import('Lib', 'ISocket');
App::import('Lib', 'CachedSocket');

class firstSocket implements ISocket {
    public function  get($uri = NULL, $query = array(), $request = array()) {
        return( "first socket" );
    }

    public function  post($uri = null, $data = array(), $request = array()) {
        return( "first socket" );
    }

    public function  delete($uri = null, $data = array(), $request = array()) {
        ;
    }

    public function  put($uri = null, $data = array(), $request = array()) {
        ;
    }

}

class secondSocket implements ISocket {
    public function  get($uri = NULL, $query = array(), $request = array()) {
        return( "second socket" );
    }

    public function  post($uri = null, $data = array(), $request = array()) {
        return( "second socket" );
    }

    public function  delete($uri = null, $data = array(), $request = array()) {
        ;
    }

    public function  put($uri = null, $data = array(), $request = array()) {
        ;
    }
}

class CachedSocketTestCase extends CakeTestCase {

	function startTest() {
	}

	function endTest() {
		ClassRegistry::flush();
  	}

    function testGet() {
        /* Response should be 'first socket' */
        $rq1 = new CachedSocket( new firstSocket(), 'CacheTest', 2);
        $response = $rq1->get();
        $this->assertEqual($response, "first socket");
        /* Response should still be 'first socket' since it is cached */
        $rq2 = new CachedSocket( new secondSocket(), 'CacheTest', 2 );
        $response = $rq2->get();
        $this->assertEqual($response, "first socket");
        /* allow the cache to timeout */
        sleep(3);
        /* and the response should now be 'second socket' */
        $response = $rq2->get();
        $this->assertEqual($response, "second socket");
    }
}
?>
