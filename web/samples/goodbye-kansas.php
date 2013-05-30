<?php
require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$socket->on('connection', function ($conn) {
    echo "{$conn->stream} connected\n";
    $conn->on('data', function ($data) use ($conn) {
        echo "{$conn->stream} says '{$data}'";
        $conn->write("You said: {$data}");
    });
});

$socket->listen(1337, '0.0.0.0');
$loop->run();
