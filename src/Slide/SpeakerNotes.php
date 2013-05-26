<?php
namespace Slide;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\Wamp\WampServerInterface;

class SpeakerNotes implements WampServerInterface, Controllable {
    public function onPublish(Conn $conn, $topic, $event, array $exclude = array(), array $eligible = array()) {
    }

    public function onCall(Conn $conn, $id, $topic, array $params) {
        $conn->callError($id, $topic, 'RPC not supported');
    }

    public function onOpen(Conn $conn) {
    }

    public function onClose(Conn $conn) {
    }

    public function onSubscribe(Conn $conn, $topic) {
        echo "Conn subscribed to speakerNotes:{$topic}\n";
    }

    public function onUnSubscribe(Conn $conn, $topic) {
    }

    public function onError(Conn $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
    }

    public function command($directive) {
        
    }

    public function enableRemote() {
        
    }
    
    public function disableRemote() {
        
    }
}
