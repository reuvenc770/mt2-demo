mt2App.controller( 'AppController' , [ '$rootScope' , '$location' , '$window' , '$mdSidenav' , '$mdToast' , '$mdMedia' , '$cookies' , '$log' , function ( $rootScope , $location , $window , $mdSidenav , $mdToast , $mdMedia , $cookies , $log ) {
    var self = this;
    self.lockSidenav = true;

    self.sidenavClosedClass = 'mt2-nav-children-closed';
    self.sidenavOpenClass = 'mt2-nav-children-open';
    self.sidenavSectionClasses = {};
    self.sidenavSectionOpenStatus = {};
    self.sideNavMinimized = false;

    self.initSideNavMenu = function ( sectionName ) {
        self.sidenavSectionClasses[ sectionName ] = [ self.sidenavClosedClass ]; 
        self.sidenavSectionOpenStatus[ sectionName ] = false;
    };

    self.openSideNavMenu = function ( sectionName ) {
        angular.forEach( self.sidenavSectionClasses , function ( section , key ) {
            $log.info( key );
            self.sidenavSectionClasses[ key ] = [ self.sidenavClosedClass ];
            self.sidenavSectionOpenStatus[ key ] = false;
        } );

        self.sidenavSectionClasses[ sectionName ] = [ self.sidenavOpenClass ]; 
        self.sidenavSectionOpenStatus[ sectionName ] = true;
    };

    self.toggleNavSize = function () {
        self.sideNavMinimized = !self.sideNavMinimized; 
    };

    self.getBaseUrl = function () {
        return $location.protocol() + '://' + $location.host() + '/';
    };

    self.isMobile = function () {
        return !$mdMedia( 'gt-sm' );
    };

    self.largePageWidth = function () {
        return $mdMedia( 'gt-md' );
    };

    self.redirect = function ( redirectURL ) {
        $log.log( 'Redirecting to ' + redirectURL );
        $location.url( redirectURL );
        $window.location.href = redirectURL;
    };

    self.toggleMenu = function ( navId , event ) {
        if ( self.largePageWidth() ){
            self.lockSidenav = !self.lockSidenav;

            self.setSidenavCookie( event );
        } else {
            $mdSidenav( navId ).toggle();
        }
    };

    self.openDropdownMenu = function( $mdOpenMenu , ev ) {
        originatorEv = ev;
        $mdOpenMenu( ev );
    };

    self.showToastMessage = function ( message , level ) {
        var toast = $mdToast.simple()
                            .textContent( message )
                            .position( 'top right' );

        if ( level === 'warning' || level === 'error' ) {
            toast = toast.action( 'OK' )
                        .highlightAction( true )
                        .hideDelay( false );
        }

        $mdToast.show( toast );
    };

    self.setSidenavCookie = function( event ) {
        var currentSidenavCookie = $cookies.get('sidenavCookie');

        if ( typeof(currentSidenavCookie) != 'undefined' && typeof(event) == 'undefined' ) {
            self.lockSidenav = currentSidenavCookie === 'true' ? true : false;
        } else {
            $cookies.put( 'sidenavCookie' , self.lockSidenav );
        }
    };

} ] );
