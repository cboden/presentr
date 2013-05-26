<?php
namespace Slide;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\WebSocket\WsServerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\WampServerInterface;

class RemoteControl implements MessageComponentInterface, WsServerInterface {
    const PASSWORD = 'password';

    protected $_thereCanOnlyBeOne = false;

    protected $_delegate;

    public function __construct(Controllable $delegate) {
        $this->_delegate = $delegate;
    }

    public function onOpen(Conn $conn) {
        if ($this->_thereCanOnlyBeOne) {
            echo "THERE CAN BE ONLY ONE!\n";
            return $conn->close(1008);
        }

        $pass = $conn->WebSocket->request->getHeader('Sec-WebSocket-Protocol', true);
        echo "User connected with password: {$pass}\n";

        if (static::PASSWORD != $conn->WebSocket->request->getHeader('Sec-WebSocket-Protocol', true)) {
            return $conn->close(1008);
        }
    
        $this->_delegate->enableRemote();
        $this->_thereCanOnlyBeOne = true;
    }
    
    public function onMessage(Conn $conn, $msg) {
        echo "Remote received message: '{$msg}'\n";
        $this->_delegate->command($msg);
    }

    public function onClose(Conn $conn) {
        $this->_delegate->disableRemote();
        $this->_thereCanOnlyBeOne = false;
    }

    public function onError(Conn $conn, \Exception $e) {
    }
    
    public function getSubProtocols() {
        return array(self::PASSWORD);
    }
}
