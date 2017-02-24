mt2App.directive( 'listprofileCombineCreate' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "combineName" : "=",
            "ftpFolder"   : "=",
            "ftpFolderError": "=",
            "createCombine" : "&",
            "combineError" : "="
        } ,
        "templateUrl" : "js/templates/listprofile-combine-create-modal.html"
    };
} );
