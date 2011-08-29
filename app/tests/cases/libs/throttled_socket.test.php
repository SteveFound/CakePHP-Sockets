<?php
App::import('Lib', 'ISocket');
App::import('Lib', 'ThrottledSocket');

class mySocket implements ISocket {

    public function  delete($uri = null, $data = array(), $request = array()) {
        ;
    }

    public function  put($uri = null, $data = array(), $request = array()) {
        ;
    }
    
    public function  get($uri = NULL, $query = array(), $request = array()) {
        return( "<html>\n<body>\n</body>\n</html>\n" );
    }

    public function  post($uri = null, $data = array(), $request = array()) {
        return( "<html>\n<body>\n</body>\n</html>\n" );
    }
}

class ThrottledSocketTestCase extends CakeTestCase {

	function startTest() {
	}

	function endTest() {
		ClassRegistry::flush();
	}

    function testGet() {
        $rq = new ThrottledSocket( new mySocket());
        $response = $rq->get();
        $this->assertEqual($response, "<html>\n<body>\n</body>\n</html>\n");
    }

    function testGet10() {
        $rq = new ThrottledSocket( new mySocket());
        $start = time();
        for( $i = 0; $i < 5; $i++ ){
            $response = $rq->get();
            $this->assertEqual($response, "<html>\n<body>\n</body>\n</html>\n");
        }
        $end = time();
        $this->assertTrue($end >= $start+5, "Test should take 5 seconds or more.");
    }
}
?>