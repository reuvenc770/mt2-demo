<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;
use App\Services\MT1SuppressionService;

class LazyMT1SuppressionCheckIterator implements \Iterator {
    private $suppService;

    private $emailList;
    private $emailListCursor;
    private $cursorPositionValid = true;

    protected $validEmail = '';
    protected $lastValidEmail = '';

    public function __construct ( MT1SuppressionService $suppService , Model $emailList ) {
        $this->suppService = $suppService;
        $this->emailList = $emailList;
        $this->emailListCursor = $this->emailList->cursor();
    }

    public function current () {
        return $this->validEmail;
    }

    public function next () {
        $this->lastValidEmail = $this->validEmail;
        $this->validEmail = '';
        $this->cursorPositionValid = false;
        
        while ( $this->validEmail == '' && $this->emailListCursor->valid() ) {
            $currentEmail = $this->emailListCursor->current();

            if ( !$this->suppService->isSuppressed( $currentEmail ) ) {
                $this->validEmail = $currentEmail;
                $this->cursorPositionValid = true;

                break;
            }

            $this->emailListCursor->next();
        }
    }

    public function valid () {
        return $this->cursorPositionValid;
    }

    /**
     * Unused
     */
    public function key () { return $this->emailListCursor->key(); }
    public function rewind () { $this->emailListCursor->rewind(); }
}
