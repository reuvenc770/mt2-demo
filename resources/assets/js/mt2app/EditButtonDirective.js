mt2App.directive( 'editButton' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "clickhandler" : "&" ,
            "recordid" : "="
        } ,
        "templateUrl" : "js/templates/edit-button.html"
    };
} );
