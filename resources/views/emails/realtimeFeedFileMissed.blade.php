@extends( 'emails.cmpTemplate' )

@section( 'primaryHeader' )
Alerts
@endsection

@section( 'secondaryHeader' )
Realtime Feed Files Missed 
@endsection

@section( 'content' )
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                  <tbody>
                    <tr>
                      <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left">
                        <div class="" style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;">The following list contains realtime feed files that were not processed.</div>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left">
                        <table cellpadding="0" cellspacing="0" style="cellspacing:0px;color:#000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;" width="100%" border="0">
                          <tr style="border-bottom:1px solid #ecedee;text-align:left;padding:15px 0;">
                            <th>File Path</th>
                            <th>Reason</th>
                          </tr>
                        
                            @foreach ( $content as $fileList )
                                @foreach ( $fileList[ 'files' ] as $file )
                                    <tr>
                                        <td>{{ $file[ 'path' ] }}</td>
                                        <td>{{ $file[ 'reason' ] }}</td>
                                    </tr>
                                @endforeach 
                            @endforeach
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
@endsection
