mt2App.controller( 'AttributionController' , [ 'AttributionApiService' , '$log' , function ( AttributionApiService , $log ) {
    var self = this;

    self.models = [];

    self.viewAdd = function () {}
    self.loadModels = function () {
        self.currentlyLoading = 1;

        AttributionApiService.getModels(
            self.currentPage ,
            self.paginationCount ,
            function ( response ) {
                $log.log( response.data.data );

                self.models = response.data.data;

                self.pageCount = response.data.last_page;

                self.currentlyLoading = 0;
            } ,
            function ( response ) { $log.log( response ); }
        );
    }

    self.currentlyLoading = false;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.pageCount = 0;
    self.reachedFirstPage = true;
    self.reachedMaxPage = false;
} ] );
