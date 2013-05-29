<?php
use React\EventLoop\Factory;
use React\Socket\Server as Reactor;

    require __DIR__ . '/../vendor/autoload.php';

    $loop = Factory::create();

    $show  = new Presentr\Slideshow;
    $notes = new Presentr\SpeakerNotes;

    $delegates = (new Presentr\ControllableComposite);
    $delegates->add($show)->add($notes);

    $app = new Ratchet\App('localhost', 8080, '127.0.0.1', $loop);
    $app->route('/', $show);
    $app->route('/notes', $notes);
    $app->route('/control', new Presentr\RemoteControl($delegates));
    $app->route('/chat', new Presentr\Chat);

    $echo = new Reactor($loop);
    $echo->listen(1337, '0.0.0.0');
    $echo->on('connection', function($conn) use ($show) {
        $conn->on('data', function($data) use ($show, $conn) {
            $conn->write("You said {$data}");
            $show->onTelnetData($data);
        });
    });

    $conns = new \SplObjectStorage();
    $chat  = new Reactor($loop);
    $chat->listen(9000, '0.0.0.0');
    $chat->on('connection', function($conn) use ($conns) {
        $conns->attach($conn);

        $conn->on('data', function($data) use ($conns, $conn) {
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

    $sock2_electric_boogaloo = new Reactor($loop);
    $sock2_electric_boogaloo->listen(9001, '0.0.0.0');
    $sock2_electric_boogaloo->on('connection', function($conn) use ($conns) {
        $conn->on('data', function($data) use ($conns, $conn) {
            foreach ($conns as $o_conn) {
                $from = $conn->getRemoteAddress();
                $o_conn->write("Msg from {$from}:9001 -> {$data}");
            }
        });
    });

    $app->run();
