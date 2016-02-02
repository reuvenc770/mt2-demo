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
