;(function($) {
    $.fn.setStickyHeader = function() {
        var table = $( '#attrReportTable' );
        var stickyHeader;

        function init(){
            table.wrap( '<div class="table-container" />' );
            stickyHeader = table.find( 'thead.table-header' )
                                .clone()
                                .insertBefore( table )
                                .wrap( '<table class="sticky-header" />' );
            resizeStickyHeader();
        }
        function resizeStickyHeader() {
            var thWidthList = table.find( 'th' );

            stickyHeader.find( 'th' ).each( function( index ) {
                // $(this).css("width",$this.find("th").eq(index).outerWidth()+"px");
                console.log( $( thWidthList[ index ] ).css( 'width' ) );
                // thWidth = table.find( 'th' ).eq( index ).outerWidth();
                // console.log(thWidth);
                // $( this ). css( "width" , thWidth+"px" );
            } );
        }

        init();
    };
})(jQuery);

$(document).ready( function(){
    // $( '#attrReportTable' ).setStickyHeader();
});

// #reporttab needs to not be hidden/have class 'active'
// add click handler to report tab