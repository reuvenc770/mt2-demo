mt2App.directive( 'mailingtemplateTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/mailingtemplate-table.html"
    };
} );
