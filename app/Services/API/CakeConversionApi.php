<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\API;

use App\Services\API\CakeApi;
use App\Facades\Guzzle;

class CakeConversionApi extends CakeApi {
    const ENDPOINT = "http://caridan.ampxl.net/app/websvc/cake/mt2/conv.php?";

    const API_KEY = 'F9437Yjf*udfk39';

    const FIELDNAME_API_KEY = 'ak';
    const FIELDNAME_RECORD_TYPE = 'rt';
    const FIELDNAME_START_DATE = 's';
    const FIELDNAME_END_DATE = 'e';
    //TODO PARENT HAS 2 PARAMS
    public function __construct () {}

    protected function constructApiUrl( $data = null ) {
        $fields = [
            self::FIELDNAME_API_KEY => self::API_KEY ,
            self::FIELDNAME_RECORD_TYPE => $data[ 'recordType' ] ,
            self::FIELDNAME_START_DATE => $data[ 'start' ] ,
            self::FIELDNAME_END_DATE => $data[ 'end' ]
        ];

        return self::ENDPOINT . http_build_query( $fields );
    }
}
