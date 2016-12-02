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

    protected $suppressionStatusWanted = false;

    public function __construct ( MT1SuppressionService $suppService , Model $emailList , $returnedSuppressed = false ) {
        $this->suppService = $suppService;
        $this->emailList = $emailList;
        $this->emailListCursor = $this->emailList->cursor();

        $this->suppressionStatusWanted = $returnSuppressed;

        $this->findNextRecord();
    }

    public function current () {
        return $this->emailListCursor->current();
    }

    public function next () {
        $this->emailListCursor->next();
        $this->findNextRecord();
    }

    public function valid () {
        return $this->cursorPositionValid;
    }

    protected function findNextRecord () {
        $this->lastValidEmail = $this->validEmail;
        $this->validEmail = '';
        $this->cursorPositionValid = false;
        
        while ( $this->validEmail == '' && $this->emailListCursor->valid() ) {
            $currentEmail = $this->emailListCursor->current();

            if ( $this->suppService->isSuppressed( $currentEmail ) === $this->suppressionStatusWanted ) {
                $this->validEmail = $currentEmail;
                $this->cursorPositionValid = true;

                break;
            }

            $this->emailListCursor->next();
        }
    }

    /**
     * Unused
     */
    public function key () { return $this->emailListCursor->key(); }
    public function rewind () { $this->emailListCursor->rewind(); }
}
