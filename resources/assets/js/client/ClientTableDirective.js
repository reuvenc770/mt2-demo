mt2App.directive( 'clientTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/client-table.html"
    };
} );
