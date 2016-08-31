mt2App.directive( 'feedTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/feed-table.html"
    };
} );
