mt2App.controller( 'HelperController' , [ '$location' , '$window' , '$log' , function ( $location , $window , $log ) {
    var self = this;

    self.redirect = function ( redirectURL ) {
        $log.log( 'Redirecting to ' + redirectURL );
        $location.url( redirectURL );
        $window.location.href = redirectURL;
    };
} ] );
