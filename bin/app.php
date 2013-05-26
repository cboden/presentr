<?php
    require __DIR__ . '/../vendor/autoload.php';

    $show  = new Slide\Slideshow;
    $notes = new Slide\SpeakerNotes;

    $delegates = (new Slide\ControllableComposite);
    $delegates->add($show)->add($notes);

    $app = new Ratchet\App('localhost', 8080, '0.0.0.0');
    $app->route('/', $show);
    $app->route('/notes', $notes);
    $app->route('/control', new Slide\RemoteControl($delegates));
    $app->run();
