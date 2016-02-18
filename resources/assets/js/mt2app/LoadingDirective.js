mt2App.service("ajaxIndicator", ["$rootScope"], function($rootScope) {
    "use strict";

    $rootScope.ajaxActive = false;

    function indicate(promise) {
        if( !$rootScope.ajaxActive ) {
            $rootScope.ajaxActive = true;
            $rootScope.$broadcast("ajax.active"); // OPTIONAL
            if( typeof(promise) === "object" && promise !== null ) {
                if( typeof(promise.always) === "function" ) promise.always(finished);
                else if( typeof(promise.then) === "function" ) promise.then(finished,finished);
                else if( typeof(promise.$then) === "function" ) promise.$then(finished,finished);
            }
        }
    }

    function finished() {
        $rootScope.ajaxActive = false;
    }

    return {
        indicate: indicate,
        finished: finished
    };
});