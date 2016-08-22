mt2App.directive( 'registrarTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggle"  : "&"
        } ,
        "templateUrl" : "js/templates/registrar-table.html"
    };
} );
