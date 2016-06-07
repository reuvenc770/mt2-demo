<?php

namespace App\Listeners;

use App\Events\BulkSuppressionFileWasUploaded;
use App\Facades\Suppression;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use League\Csv\Reader;
class ParseBulkSuppressionFiles implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BulkSuppressionFileWasUploaded  $event
     * @return void
     */
    public function handle(BulkSuppressionFileWasUploaded $event)
    {
        $path = storage_path() . "/app/files/uploads/bulksuppression/{$event->getDate()}/{$event->getFileName()}";
        $reader = Reader::createFromPath($path);
        foreach ($reader as $index => $row) {
            Suppression::recordSuppressionByReason($row[0], $event->getDate(), $event->getReasonId());
        }
    }

}
