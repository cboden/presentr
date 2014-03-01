<?php

    require __DIR__ . '/../../vendor/autoload.php';

    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:5555");

    $socket->send($_POST['msg']);
