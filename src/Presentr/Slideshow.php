<?php
namespace Presentr;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\Wamp\WampServerInterface;

class Slideshow implements WampServerInterface, Controllable {
    const TPC_REMOTE = 'ctrl:remote';

    protected $_controlled  = false;
    protected $_remoteTopic = null;
    protected $_numConns    = 0;

    public function onPublish(Conn $conn, $topic, $event, array $exclude = array(), array $eligible = array()) {
        $topic->broadcast($event);
    }

    public function onCall(Conn $conn, $id, $topic, array $params) {
        $conn->callError($id, $topic, 'RPC not supported');
    }

    public function onOpen(Conn $conn) {
        $this->_numConns++;
    }

    public function onClose(Conn $conn) {
        $this->_numConns--;
        $this->evRemote();
    }

    public function onSubscribe(Conn $conn, $topic) {
        echo "Conn {$conn->resourceId} subscribed to {$topic}\n";

        if (self::TPC_REMOTE == $topic->getId()) {
            if (null === $this->_remoteTopic) {
                $this->_remoteTopic = $topic;
            }

            return $this->evRemote();
        }

        $topic->broadcast(array('peers' => $topic->count() - 1));
    }

    public function onUnSubscribe(Conn $conn, $topic) {
        echo "unsub from topic {$topic}\n";
        if (self::TPC_REMOTE == $topic->getId()) {
            return $this->evRemote();
        }

        $topic->broadcast(array('peers' => $topic->count() - 1));
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

    public function onTelnetData($data) {
        // sanatize and redirect data to websocket topic
        echo "Telnet data: {$data}";
    }
    
    protected function evRemote($command = '') {
        if (!$this->_remoteTopic) {
            return;
        }

        $this->_remoteTopic->broadcast(array(
            'remote'  => (int)$this->_controlled
          , 'peers'   => $this->_numConns - 1
          , 'command' => $command
        ));
    }
}
