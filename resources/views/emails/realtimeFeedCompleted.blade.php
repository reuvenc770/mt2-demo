@extends( 'emails.cmpTemplate' )

@section( 'primaryHeader' )
Notifications
@endsection

@section( 'secondaryHeader' )
Realtime Feed Processing - Completed 
@endsection

@section( 'content' )
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                  <tbody>
                    <tr>
                      <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left">
                        <div class="" style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;">The following list contains realtime feed record consumption runs and their preliminary breakdown.</div>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left">
                        <table cellpadding="0" cellspacing="0" style="cellspacing:0px;color:#000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;" width="100%" border="0">
                          <tr style="border-bottom:1px solid #ecedee;text-align:left;padding:15px 0;">
                            <th>Processed</th>
                            <th>Feed</th>
                            <th>Party</th>
                            <th>Total</th>
                            <th>Valid</th>
                            <th>Failed</th>
                          </tr>
                        
                            @foreach ( $content as $runs )
                                @foreach ( $runs[ 'counts' ] as $feedId => $details )
                                    <tr>
                                        <td>{{ $details[ 'timestamp' ] }}</td>
                                        <td>
                                            {{ $details[ 'feedName' ] . ' [' . $feedId . ']' }}
                                        </td>
                                        <td>{{ $details[ 'party' ] }}</td>
                                        <td>{{ $details[ 'success' ] + $details[ 'fail' ] }}</td>
                                        <td>{{ $details[ 'success' ] }}</td>
                                        <td>{{ $details[ 'fail' ] }}</td>
                                    </tr>
                                @endforeach 
                            @endforeach
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
@endsection
