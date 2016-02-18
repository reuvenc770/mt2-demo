/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [ 'ngMaterial' ] );

mt2App.config( function ( $locationProvider ) {
    $locationProvider.html5Mode( true );
} );

mt2App.directive( 'genericTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" , 
        "bindToController" : { 
            "headers" : "=" ,
            "records" : "=" ,
            "editurl" : "="
        } ,
        "templateUrl" : "js/templates/generic-table.html"
    };
} );

mt2App.directive( 'editButton' , [ '$window' , '$location' , function ( $window , $location ) {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "editurl" : "=" ,
            "recordid" : "="
        } ,
        "templateUrl" : "js/templates/edit-button.html" ,
        "link" : function ( scope , element , attrs )  {
            if ( typeof( scope.ctrl ) != 'undefined' ) {
                element.on( 'click' , function () {
                    var fullEditUrl = scope.ctrl.editurl + scope.ctrl.recordid;
                    $location.url( fullEditUrl );
                    $window.location.href = fullEditUrl;
                } );
            }
        }
    };
} ] );

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

mt2App.directive( 'paginationControl' ,  function ( $log ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {
            var self = this;
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'mode' : '@'
        } ,
        "templateUrl" : "js/templates/pagination-control.html"
    };
} );

mt2App.directive( 'paginationButton' , function ( $log , $rootScope ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'pagenumber' : '='
        } ,
        "templateUrl" : "js/templates/pagination-button.html"
    };
} );

mt2App.directive( 'paginationCount' , [ '$rootScope' , '$timeout' , function ( $rootScope , $timeout ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {
            var self = this;

            self.updateRecordCount = function () {
                self.currentpage = 1;

                $timeout( function () {
                    $rootScope.$emit( 'updatePage' );
                    $rootScope.$emit( 'resetPaginationPager' );
                } , 200 );
            }
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'recordcount' : '=' ,
            'currentpage' : '='
        } ,
        "templateUrl" : "js/templates/pagination-count.html"
    };
} ] );

//# sourceMappingURL=angular_base.js.map
