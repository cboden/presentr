<?php
namespace Slide;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
 
class Chat implements MessageComponentInterface {
    protected $conns;
    public function __construct() {
        $this->conns = new \SplObjectStorage;
    }
 
    function onOpen(ConnectionInterface $conn) {
        $this->conns->attach($conn);
    }
 
    function onMessage(ConnectionInterface $conn, $msg) {
        foreach ($this->conns as $to) // Sorry for excluding the "{"
            if ($conn != $to)         // Ran out of vertical space
                $to->send($msg);      // Or how about "Pythonic"?
    }
 
    function onClose(ConnectionInterface $conn) {
        $this->conns->detach($conn);
    }
 
    function onError(ConnectionInterface $conn, \Exception $e) { }
}
