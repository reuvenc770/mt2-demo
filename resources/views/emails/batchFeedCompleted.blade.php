@extends( 'emails.cmpTemplate' )

@section( 'primaryHeader' )
Notifications
@endsection

@section( 'secondaryHeader' )
Feed Processing - Completed 
@endsection

@section( 'content' )
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                  <tbody>
                    <tr>
                      <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left">
                        <div class="" style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;">The following files were processed:</div>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left">
                        <table cellpadding="0" cellspacing="0" style="cellspacing:0px;color:#000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;" width="100%" border="0">
                          <tr style="border-bottom:1px solid #ecedee;text-align:left;padding:15px 0;">
                            <th>Feed</th>
                            <th>Files</th>
                            <th>Record Count</th>
                            <th>Consumed At</th>
                          </tr>
                        
                            @foreach ( $content as $fileList )
                                @foreach ( $fileList[ 'files' ] as $file )
                                    <tr>
                                        <td>
                                            {{ $file[ 'feedName' ] }}
                                        </td>
                                        <td>{{ $file[ 'file' ] }}</td>
                                        <td>{{ $file[ 'recordCount' ] }}</td>
                                        <td>{{ $file[ 'timeFinished' ] }}</td>
                                    </tr>
                                @endforeach 
                            @endforeach
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
@endsection
