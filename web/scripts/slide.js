'use strict';

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

.service('live', function($q, $rootScope) {
    var conn;
    var lastTopic;

    this.connect = function() {
        if (conn && (conn._websocket && conn._websocket.readyState != 3)) {
            return;
        }

        conn = new ab.Session('ws://localhost:8080', function() {
            $rootScope.$broadcast('event:live-connected');
        }, function(code) {
            $rootScope.$broadcast('event:live-disconnect');
        }, {
            'skipSubprotocolCheck': true
        });
    }

    this.subscribe = function(topic, callback) {
        conn.subscribe(topic, callback);
    }

    this.singleSubscribe = function(topic, callback) {
        if (lastTopic) {
            conn.unsubscribe(lastTopic);
        }

        conn.subscribe(topic, callback);
        lastTopic = topic;
    }
})

.controller('OnlineCtrl', function($scope, $rootScope, live, slider, $location) {
    $scope.status = 'Connecting';
    $scope.peers  = 0;

    $rootScope.$on('event:live-connected', function() {
        $scope.status = 'Online';
        $scope.$apply();

        live.subscribe('ctrl:remote', function(topic, msg) {
            $scope.peers      = msg.peers;
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
        $scope.peers  = 0;
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
	var oldHash = $location.hash();
	var oldCurrentSlide;

    var onSlideMessage = function(topic, msg) {
        $scope.peers = msg.peers;
        $scope.$apply();
    }

    $scope.peers = 0;
	$rootScope.slideCount = 0;
	$rootScope.currentSlide = 1;

    $rootScope.$on('event:live-connected', function() {
        live.singleSubscribe('slide' + $rootScope.currentSlide, onSlideMessage);
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
		live.singleSubscribe('slide' + $rootScope.currentSlide, onSlideMessage);

		oldHash = $location.hash();
		oldCurrentSlide = $rootScope.currentSlide;
	}, true);

})

/*
.controller('slideIntro', function($scope, live) {
    live.singleSubscribe('intro', function() {
        console.log('message from intro channel');
    });
})
*/

;