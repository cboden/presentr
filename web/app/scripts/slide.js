(function(angular, undefined) {
'use strict';

if (typeof console == undefined) {
    console = {
        log: function() {}
      , warn: function() {}
      , error: function() {}
    };
}


angular.module('slides', [])

.run(function($window, $rootScope, live, slider) {
	angular.element($window).bind('keydown', function(e) {
        if ($rootScope.remote && 1 == $rootScope.remote) {
            return;
        }

		switch(e.keyCode) {
			case 37:
			    slider.previous();
				break;
			case 39:
			    slider.next();
				break;
		}
	});
})

.service('slider', function($window, $rootScope) {
    this.previous = function() {
		if ($rootScope.currentSlide > 1) {
			$rootScope.currentSlide--;
			$rootScope.$apply();
		}
    }
    
    this.next = function() {
    	if ($rootScope.currentSlide < $rootScope.slideCount) {
    		$rootScope.currentSlide++;
    		$rootScope.$apply();
    	}
    }
})

.service('live', function($rootScope, $location) {
    var conn;
    var currentTopic;

    var remoteSubscriptions = {};
    var appSubscriptions    = {};

    var callbackProxy = function(topic, data) {
        remoteSubscriptions[topic](topic, data);

        if (appSubscriptions[topic] && ('slide' + $rootScope.currentSlide) == topic) {
            appSubscriptions[topic](topic, data);
        }
    };

    this.connect = function() {
        if (conn && (conn._websocket && conn._websocket.readyState != 3)) {
            return;
        }

        try {
            conn = new ab.Session('ws://' + $location.host() + addPath, function() {
                $rootScope.$broadcast('event:live-connected');
            }, function(code) {
                $rootScope.$broadcast('event:live-disconnect');
            }, {
                'skipSubprotocolCheck': true
            });
        } catch (e) {
            // probably accessing from filesystem
            console.warn('Invalid WebSocket path');

            conn = {
                subscribe: function(topic, callback) {}
              , unsubscribe: function(topic) {}
            };
        }
    }

    this.subscribe = function(topic, callback) {
        remoteSubscriptions[topic] = callback;
        conn.subscribe(topic, callbackProxy);
    }

    this.stateSubscribe = function(topic, callback) {
        if (currentTopic) {
            try {
                conn.unsubscribe(currentTopic);
                delete remoteSubscriptions[topic];
            } catch (e) {
                // nope
            }
        }

        this.subscribe(topic, callback);
        currentTopic = topic;
    }

    this.publish = function(data) {
        conn.publish(currentTopic, data);
    }

    this.softSubscribe = function(topic, callback) {
        appSubscriptions[topic] = callback;
    }
})

/*
.service('slideInteractions', function() {
    this.listen
})
*/

.controller('OnlineCtrl', function($scope, $rootScope, live, slider, $location) {
    $scope.status = 'Connecting';
    $scope.connected  = 0;

    $rootScope.$on('event:live-connected', function() {
        $scope.status = 'Online';
        $scope.$apply();

        live.subscribe('ctrl:remote', function(topic, msg) {
            $scope.connected  = msg.peers;
            $scope.status     = (1 == msg.remote ? 'Controlled' : 'Online');
            $rootScope.remote = msg.remote;

            if (msg.command) {
                $rootScope.currentSlide = parseInt(msg.command, 10);
            }

            $rootScope.$apply();
        });
    });
    $rootScope.$on('event:live-disconnect', function() {
        $scope.status = 'Offline';
        $scope.connected  = 0;
        $scope.$apply();
        
        setTimeout(function() {
            $scope.status = 'Connecting';
            $scope.$apply();
            live.connect();
        }, 5000);
    });

    live.connect();
})

.controller('SlideCtrl', function($scope, $location, $rootScope, live) {
    $scope.host   = $location.host();
    $scope.ip     = $location.host();

	var oldHash = $location.hash();
	var oldCurrentSlide;

    var onSlideMessage = function(topic, msg) {
        if (undefined != msg.peers) { // fix
            $scope.peers = msg.peers;
            $scope.$apply();
        }
    }

    $scope.peers = 0;
	$rootScope.slideCount = 0;
	$rootScope.currentSlide = 1;

    $rootScope.$on('event:live-connected', function() {
        live.stateSubscribe('slide' + $rootScope.currentSlide, onSlideMessage);
    });

	if ($location.hash()) {
		$rootScope.currentSlide = oldCurrentSlide = parseInt($location.hash(), 10);
	} else if ($scope.slideCount > 0) {
		$rootScope.currentSlide = oldCurrentSlide = 1;
	}

	$rootScope.$watch('currentSlide', function() {
		if (($location.hash() || 1) == $rootScope.currentSlide) return;

		if ($location.hash() != oldHash) {
			$rootScope.currentSlide = parseInt($location.hash(), 10) || 1;
		} else if ($rootScope.currentSlide != oldCurrentSlide){
			$location.hash(($rootScope.currentSlide > 1) ? $rootScope.currentSlide : '');
		}

        // I wanted to use topic strings on slide to identify their topic subscriptions
        // but have been unsuccessful in binding a data attribute to scope
        // So I'm using the number to do so...which will cause problems if the slides get re-ordered...
		live.stateSubscribe('slide' + $rootScope.currentSlide, onSlideMessage);

		oldHash = $location.hash();
		oldCurrentSlide = $rootScope.currentSlide;
	}, true);
})

.controller('slideDraw', function($scope, live) {
    this.serverDraw = function(topic, data) {
        if (!Array.isArray(data)) {
            return;
        }

        $scope.compDraw.push(data[0]);
        $scope.compCount++;
        $scope.$apply();
    }

    // If things break look at this hard coded number!!!
    live.softSubscribe('slide32', this.serverDraw);
/*
    setTimeout(function() {
        $scope.compDraw.push([200, 400, 22, 44]);
        $scope.compDraw.push([50, 50, 500, 500]);
        $scope.compCount += 2;
        $scope.$apply();
    }, 2000);

    setTimeout(function() {
        $scope.compDraw.push([40, 20, 200, 20]);
        $scope.compCount++;
        $scope.$apply();
    }, 4000);
*/

    $scope.$watch('userCount', function() {
        if (0 == $scope.userDraw.length) {
            return;
        }

        live.publish($scope.userDraw);
        $scope.userDraw = [];
    });
})

/*
.controller('slideIntro', function($scope, live) {
    live.singleSubscribe('intro', function() {
        console.log('message from intro channel');
    });
})
*/

;})(angular);
