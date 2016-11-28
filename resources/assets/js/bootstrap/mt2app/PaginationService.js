mt2App.service( 'paginationService' , [ function () {
    var self = this;

    self.getDefaultPaginationCount = function () {
        return '25';
    };

    self.getDefaultPaginationOptions = function () {
        return [10, 25, 50, 100];
    };

    self.sortPage = function ( sortField ) {
        var sort = { 'field' : sortField , 'desc' : false };

        if (/^\-/.test( sortField ) ) {
            sort.field = sort.field.substring( 1 );
            sort.desc = true;
        }

        return sort;
    };

} ] );