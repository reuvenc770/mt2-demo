mt2App.directive( 'deployTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "showrow" : "=",
            "currentdeploy" : "=",
            "espaccounts" : "=",
            "formerrors" : "="
        } ,
        "templateUrl" : "js/templates/deploy-table.html"
    };
} );
