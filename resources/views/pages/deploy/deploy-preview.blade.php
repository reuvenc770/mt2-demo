@extends( 'layout.default' )

@section( 'title' , 'View Deploy HTML' )


@section( 'content' )

            {!! htmlentities($html, ENT_SUBSTITUTE) !!}
@stop

@section( 'pageIncludes' )

@stop
