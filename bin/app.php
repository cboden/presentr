<?php
use React\EventLoop\Factory;
use React\Socket\Server as Reactor;

    require __DIR__ . '/../vendor/autoload.php';

    $loop = Factory::create();

    $show  = new Slide\Slideshow;
    $notes = new Slide\SpeakerNotes;

    $delegates = (new Slide\ControllableComposite);
    $delegates->add($show)->add($notes);

    $app = new Ratchet\App('localhost', 8080, '127.0.0.1', $loop);
    $app->route('/', $show);
    $app->route('/notes', $notes);
    $app->route('/control', new Slide\RemoteControl($delegates));

    $telnet = new Reactor($loop);
    $telnet->listen(1337, '0.0.0.0');
    $telnet->on('connection', function($conn) use ($show) {
        $conn->on('data', function($data) use ($show) {
            $show->onTelnetData($data);
        });
    });


    $app->run();
