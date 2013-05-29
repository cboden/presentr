<?php
namespace Presentr;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\Wamp\WampServerInterface;

class SpeakerNotes implements WampServerInterface, Controllable {
    const TPC_REMOTE = 'ctrl:remote';

    protected $_remoteTopic = null;
    protected $_controlled  = false;

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
        if (self::TPC_REMOTE == $topic->getId()) {
            if (null === $this->_remoteTopic) {
                $this->_remoteTopic = $topic;
            }

            return $this->evRemote();
        }
    }

    public function onUnSubscribe(Conn $conn, $topic) {
    }

    public function onError(Conn $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
    }

    public function command($directive) {
        $this->evRemote($directive);
    }

    public function enableRemote() {
        $this->_controlled = true;
        $this->evRemote();
    }
    
    public function disableRemote() {
        $this->_controlled = false;
        $this->evRemote();
    }

    protected function evRemote($command = '') {
        if (!$this->_remoteTopic) {
            return;
        }

        $this->_remoteTopic->broadcast(array(
            'remote'  => (int)$this->_controlled
          , 'peers'   => 0
          , 'command' => $command
        ));
    }
}