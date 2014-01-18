<?php

    require __DIR__ . '/../../vendor/autoload.php';

$randomPromise = function(React\EventLoop\LoopInterface $loop) {
    $deferred = new React\Promise\Deferred();

    $loop->addTimer(1, function() use ($deferred) {
        $num = rand();
        if (0 === $num % 2) {
            return $deferred->resolve($num);
        }
        $deferred->reject($num);
    });

    return $deferred->promise();
};

$loop = React\EventLoop\Factory::create();
$randomPromise($loop)->then(function($num) {
    echo "Promise resolved with even {$num}\n";
}, function($num) {
    echo "Promise was rejected with odd {$num}\n";
});

$loop->run();
