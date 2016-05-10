mt2App.directive( 'deleteexportButton' , function () {
  return {
      "scope" : {} ,
      "controller" : function () {} ,
      "controllerAs" : "ctrl" ,
      "bindToController" : {
        "recordid": "=",
        "deleteexport": "&"
    } ,
    "templateUrl" : "js/templates/deleteexport-button.html"
  };
});