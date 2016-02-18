mt2App.directive( 'pagination' , [ '$rootScope' , '$timeout' , function ( $rootScope , $timeout ) {
    return {
        "scope" : {} ,
        "controller" : function  () {
            var self = this;

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

            self.updatePage = function ( pageNumber ) {
                self.currentpage = pageNumber;

                $timeout( function () { $rootScope.$emit( 'updatePage' ); } , 200 );
            };

            self.prevPage = function () {
                self.button1--;
                self.button2--;
                self.button3--;
                self.button4--;
                self.button5--;
                self.currentpage--;

                $timeout( function () { $rootScope.$emit( 'updatePage' ); } , 200 );
            };

            self.nextPage = function () {
                self.button1++;
                self.button2++;
                self.button3++;
                self.button4++;
                self.button5++;
                self.currentpage++;

                $timeout( function () { $rootScope.$emit( 'updatePage' ); } , 200 );
            };
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'currentpage' : '='
        } ,
        "templateUrl" : "js/templates/pagination.html"
    };
} ] );
