<?php
require __DIR__.'/../../vendor/autoload.php';

$loop   = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$conns  = new \SplObjectStorage();

$socket->on('connection', function ($conn) use ($conns) {
    $conns->attach($conn);

    $conn->on('data', function ($data) use ($conns, $conn) {
        foreach ($conns as $current) {
            if ($conn === $current) {
                continue;
            }

            $current->write($conn->getRemoteAddress().': ');
            $current->write($data);
        }
    });

    $conn->on('end', function () use ($conns, $conn) {
        $conns->detach($conn);
    });
});

$socket->listen(9000);

$sock2_electrib_boogaloo = new React\Socket\Server($loop);

$sock2_electrib_boogaloo->on('connection', function($conn) use ($conns) {
    $conn->on('data', function($data) use ($conns, $conn) {
        foreach ($conns as $a_first_conn) {
            $a_first_conn->write("Msg from {$conn->getRemoteAddress()}:9001 -> {$data}");
        }
    });
});

$sock2_electrib_boogaloo->listen(9001);

$loop->run();
