;(function($) {
    $.fn.setStickyHeader = function() {
        var table = $( this );
        var stickyHeader;

        function init(){
            table.wrap( '<div class="table-container" />' );
            stickyHeader = table.find( 'thead.table-header' )
                                .clone()
                                .insertBefore( table )
                                .wrap( '<table md-table class="sticky-header" />' );
            stickyHeader = $('.sticky-header');

            resizeStickyHeader();
        }

        function resizeStickyHeader() {

            stickyHeader.find( 'th' ).each( function( index ) {

                thWidth = table.find( 'th' ).eq( index ).outerWidth();

                $( this ). css( "width" , thWidth+"px" );
            } );
        }

        function scrollStickyHeader() {
            var offset = $( this ).scrollTop();

            tableOffsetTop = table.offset().top;

            tableOffsetBottom = tableOffsetTop + table.height() - table.find( 'thead.table-header' ).height();

            console.log( 'offset: ' + offset + ' ; tableOffsetTop: ' + tableOffsetTop + ' ; tableOffsetBottom: ' + tableOffsetBottom )

            if ( offset < tableOffsetTop || offset > tableOffsetBottom ) {
                stickyHeader.hide();
            } else if ( offset >= tableOffsetTop && offset <= tableOffsetBottom && stickyHeader.is( ':hidden' ) ) {
                stickyHeader.show();
            }
        }

        $(window).resize( resizeStickyHeader );
        $(window).scroll( resizeStickyHeader );
        $(window).scroll( scrollStickyHeader );
        init();
    };
})(jQuery);

$(document).ready( function(){
    $( '.stickyTable' ).setStickyHeader();
});