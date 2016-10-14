mt2App.controller( 'AppController' , [ '$rootScope' , '$location' , '$window' ,  '$mdToast' , '$mdMedia' , '$cookies' , '$timeout' , '$log' , function ( $rootScope , $location , $window , $mdToast , $mdMedia , $cookies , $timeout , $log ) {
    var self = this;

    self.activeSection = {};

    self.setCurrentActiveSection = function ( sectionName , path ) {
        if ( path == self.currentPath ) {
            self.activeSection = {};
            self.activeSection[ sectionName ] = true;
        }
    };

    /**
     * Main Side Nav
     */
    self.sidenavSectionOpenStatus = {};
    self.sidenavMouseOverOpenStatus = {};
    self.sidenavMouseOverCss = {};
    self.sideNavMinimized = false;

    self.currentPath = '';

    self.initSideNavMenu = function ( sectionName ) {
        self.sidenavSectionOpenStatus[ sectionName ] = false;
        self.sidenavMouseOverOpenStatus[ sectionName ] = false;
    };

    self.openActiveSideNavMenu = function ( sectionName , path ) {
        if ( path == self.currentPath ) {
            self.openSideNavMenu( sectionName );

            self.activeSection = {};
            self.activeSection[ sectionName ] = true;
        }
    };

    self.openSideNavMenu = function ( sectionName ) {
        self.closeSideNavAccordians();

        self.sidenavSectionOpenStatus[ sectionName ] = true;
    };

    self.closeSideNavAccordians = function () {
        angular.forEach( self.sidenavSectionOpenStatus , function ( section , key ) {
            self.sidenavSectionOpenStatus[ key ] = false;
        } );
    };

    self.openHoverMenu = function ( sectionName ) {
        self.closeHoverMenu();

        self.sidenavMouseOverOpenStatus[ sectionName ] = true;

        self.sidenavMouseOverCss[ sectionName ] = { "left" : self.getParentWidth( sectionName ) , "top" : self.getParentTopOffset( sectionName ) };
    };

    self.getParentWidth = function ( sectionName ) {
        return ( angular.element( document.getElementById( sectionName + 'Parent' ) ).prop( 'offsetWidth' ) - 3 ) + 'px'; //Taking border into account
    };

    self.getParentTopOffset = function ( sectionName ) {
        return angular.element( document.getElementById( sectionName + 'Parent' ) ).prop( 'offsetTop' ) + 'px';
    };

    self.closeHoverMenu = function () {
        angular.forEach( self.sidenavMouseOverOpenStatus , function ( section , key ) {
            self.sidenavMouseOverOpenStatus[ key ] = false;
            self.sidenavMouseOverCss[ key ] = {};
        } );
    };

    self.toggleNavSize = function ( $event ) {
        self.removeToggleFocus( $event );

        self.closeSideNavAccordians();
        self.closeHoverMenu();

        self.sideNavMinimized = !self.sideNavMinimized; 

        self.setSidenavCookie( $event );
    };

    self.removeToggleFocus = function ( $event ) {
        $event.target.blur();
        angular.element( $event.target ).parent().blur();
    }

    self.setSidenavCookie = function( event ) {
        var currentSidenavCookie = $cookies.get('sidenavCookie');

        if ( typeof(currentSidenavCookie) != 'undefined' && typeof(event) == 'undefined' ) {
            self.sideNavMinimized = currentSidenavCookie === 'true' ? true : false;
        } else {
            $cookies.put( 'sidenavCookie' , self.sideNavMinimized );
        }
    };

    /**
     * Helpers
     */
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
} ] );
