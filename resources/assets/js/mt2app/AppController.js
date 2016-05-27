mt2App.controller( 'AppController' , [ '$rootScope' , '$location' , '$window' , '$log' , function ( $rootScope , $location , $window , $log ) {
    var self = this;

    self.fullMenu = false;

    self.redirect = function ( redirectURL ) {
        $log.log( 'Redirecting to ' + redirectURL );
        $location.url( redirectURL );
        $window.location.href = redirectURL;
    };

    self.toggleMenuSize = function () { self.fullMenu = !self.fullMenu; }

    self.openDropdownMenu = function( $mdOpenMenu , ev ) {
        originatorEv = ev;
        $mdOpenMenu( ev );
    };
} ] );
