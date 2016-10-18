mt2App.directive( 'pagination' , [ '$rootScope' , '$timeout' , function ( $rootScope , $timeout ) {
    return {
        "scope" : {} ,
        "controller" : function  () {
            var self = this;
            self.lastUpdate = null;

            self.button1 = 1;
            self.button2 = 2;
            self.button3 = 3;
            self.button4 = 4;
            self.button5 = 5;

            $rootScope.$on( 'resetPaginationPager' , function () {
                self.button1 = 1;
                self.button2 = 2;
                self.button3 = 3;
                self.button4 = 4;
                self.button5 = 5;
            } );

            self.updatePage = function ( pageNumber , event ) {
                event.preventDefault();

                if ( pageNumber > self.maxpage ) return null;

                $timeout.cancel( self.lastUpdate );

                self.currentpage = pageNumber;

                self.lastUpdate = $timeout( function () { $rootScope.$emit( 'updatePage' ); } , 500 );
            };

            self.prevPage = function ( event ) {
                event.preventDefault();

                if ( self.currentpage == 1 ) return null;

                if ( self.currentpage > 1 ) {
                    self.button1--;
                    self.button2--;
                    self.button3--;
                    self.button4--;
                    self.button5--;

                    $timeout.cancel( self.lastUpdate );

                    self.currentpage--;

                    self.lastUpdate = $timeout( function () { $rootScope.$emit( 'updatePage' ); } , 500 );
                }
            };

            self.nextPage = function ( event ) {
                event.preventDefault();

                if ( ( self.currentpage + 1 ) > self.maxpage ) return null;

                if ( self.currentpage < parseInt( self.maxpage ) ) {
                    self.button1++;
                    self.button2++;
                    self.button3++;
                    self.button4++;
                    self.button5++;

                    $timeout.cancel( self.lastUpdate );

                    self.currentpage++;

                    self.lastUpdate = $timeout( function () { $rootScope.$emit( 'updatePage' ); } , 500 );
                }
            };
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'currentpage' : '=' ,
            'maxpage' : '='
        } ,
        "templateUrl" : "js/templates/pagination.html"
    };
} ] );
