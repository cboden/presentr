<!doctype html>
<html lang="en" xmlns:ng="http://angularjs.org/" ng-app="slides">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>React</title>

    <style type="text/css">
        body slide { display: none; }
    </style>
</head>

<body ng-controller="SlideCtrl">

    <slide title="Reacting: Evented programming in PHP" class="react" topic="intro">
        <div class="center">



            <img src="img/logo.png" />

        





        <p class="right">by: Chris Boden</p>
        </div>
    </slide>
    

    <slide title="Hello World!"><center><img src="img/hello.png"></center>
        <p class="right smaller">But what if I told you...</p>
    </slide>

    <slide title="You are a slave" class="dark smaller">

        <center><img src="img/pills.png"></center>


        <p class="right">CGI is the world that has been pulled over your eyes to blind you from the truth</p>
    </slide>

    <slide title="What happens?">
        <center><img src="img/request-response.png"></center>
    </slide>

    <slide title="What's in a message?">
        <center><img src="img/chain.png"></center>
    </slide>

    <slide title="Follow the white rabbit (arrows)">
        <center><img src="img/cgi.png"></center>
    </slide>

    <slide title="Opening your eyes" class="terminal smaller">
        <p><span class="generated">$</span> curl -v http://{{host}}/hello.php</p>
        <pre class="smaller generated">* About to connect() to {{host}} port 80 (#0)
*   Trying {{host}}...
* connected
* Connected to {{host}} ({{host}}) port 80 (#0)
&gt; GET /hello.php HTTP/1.1
&gt; User-Agent: curl/7.24.0 OpenSSL/0.9.8r zlib/1.2.5
&gt; Host: {{host}}
&gt; Accept: */*
&gt; 
&lt; HTTP/1.1 200 OK
&lt; Content-Type: text/html
&lt; 
&lt;!DOCTYPE html&gt;
&lt;html&gt;&lt;body&gt;
&lt;div&gt;etc, etc
</pre>
    </slide>

    <slide title="Deeper into the rabbit hole" class="terminal">
        <pre><span class="generated">$</span> telnet {{host}} 80
<span class="generated">Trying {{ip}}...
Connected to {{host}}.
Escape character is '^]'.</span>
GET / HTTP/1.1
Host: {{host}}
⏎</pre>
    </slide>

    <slide title="PHP webserver" class="terminal smaller">
        <span class="generated">$</span> php -S 0.0.0.0:8000 alice.php


        <span class="generated">(in another term - server takes control of env)</span>
        <span class="generated">$</span> curl http://localhost:8000

        <pre class="generated">*   Trying 127.0.0.1...
* Connected to localhost (127.0.0.1) port 8000 (#0)
> GET / HTTP/1.1
> User-Agent: curl/7.27.0
> Host: localhost:8000
> 
&lt; HTTP/1.1 200 OK
&lt; Host: localhost:8000
&lt; Connection: close
&lt; Content-type: text/html
&lt; 
5.4.6-1ubuntu1.2
</pre>
    </slide>

    <slide title="Stop!">
        <div class="center">
            <img src="img/php-hammer.jpg">


            <h3>Hammer time!</h3>
        </div>
    </slide>

    <slide title="Synchronous/blocking processing">
        <div class="center">
            <img src="img/dmv.gif">
        </div>

        <p class="smaller">The server processes incoming requests one at a time, in a queue manner.</p>
    </slide>

    <slide title="The Unix Philosophy">



        <center>
        <blockquote>Rule of Composition: Developers should write programs that can communicate easily with other programs. This rule aims to allow developers to break down projects into small, simple programs rather than overly complex monolithic programs.</blockquote>
        </center>
    </slide>

    <slide title="In practice">
        <div class="center">
            <img src="img/assembly-line.jpg">
        </div>
    </slide>

<!--
    <slide title="Free your mind" class="terminal smaller">
        <span class="generated">$</span> mkfifo proxypipe
        <span class="generated">$</span> while true; do cat proxypipe | nc -l 0.0.0.0 8080 | tee -a in-proxy.txt | nc localhost 8000 | tee -a out-proxy.txt 1>proxypipe; done
    </slide>
-->

    <slide title="What is the Reactor pattern?" class="dark">
        <center><img src="img/telephone-operator.jpg"></center>
    </slide>

    <slide><img src="img/get-on-with-it.jpg" class="center"></slide>

    <slide title="getcomposer.org" class="smallerCode">
        <div class="half right"><img src="img/composer.png"></div>
<div class="half left">
        <pre class="code-only">{
    "require": {
        "react/react": "~0.3"
    }
}</pre>
</div>
    <div class="clear"></div>


    <p class="smaller terminal"><span class="generated">$</span> curl -sS https://getcomposer.org/installer | php
        <span class="generated">$</span> composer install
    </p>
    </slide>

    <slide title="Through the looking glass" class="smallerCode">
        <pre class="code-only">&lt;?php
require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$socket->on('connection', function ($conn) {
    $conn->on('data', function ($data) use ($conn) {
        $conn->write("You said: {$data}");
    });
});

$socket->listen(1337, '0.0.0.0');
$loop->run();</pre>
        <p class="terminal"><span class="generated">$</span> php goodbye-kansas.php</p>
    </slide>

    <slide title="pro&bull;to&bull;col">
    
    
        <center><blockquote>A standard procedure for regulating data transmission between computers.</blockquote></center>






        <p class="terminal">110111000101011101101000012</p>
    </slide>

    <div id="slideCounter">
                         <span ng-controller="OnlineCtrl" title="Live slideshow status">{{status}}</span> 
        &nbsp; -- &nbsp; <span title="Slide number/total">{{currentSlide}} / {{slideCount}}</span> 
        &nbsp; -- &nbsp; <span title="Peers on this slide/overall">{{peers}}/<span ng-controller="OnlineCtrl">{{peers}}</span></span>
    </div>
</body>
    <script>var addPath = '';</script>

    <script src="scripts/jquery-1.9.1.js"></script>
    <script src="scripts/autobahnjs/autobahn.js"></script>
    <script src="scripts/when/when.js"></script>
    <script src="scripts/angular.js" ng:autobind></script>
    <script src="syntaxhighlighter/shCore.js"></script>
    <script src="syntaxhighlighter/shBrushJScript.js"></script>
    <script src="syntaxhighlighter/shBrushXml.js"></script>

    <link rel="stylesheet" href="style.css" type="text/css">
    <link rel="stylesheet" href="css/shCore.css" type="text/css">
    <link rel="stylesheet" href="css/shThemeDefault.css" type="text/css">
    <link rel="stylesheet" href="css/presentation.css" type="text/css">

    <script src="scripts/slide.js"></script>
    <script src="scripts/slide-directives.js"></script>
</html>
