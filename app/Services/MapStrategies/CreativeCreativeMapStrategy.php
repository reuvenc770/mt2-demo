<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CreativeCreativeMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['creative_id'],
            'file_name' => $record['creative_name'],
            'is_approved' => ($record['approved_flag'] === 'Y' ? 1 : 0),
            'status' => $record['status'],
            'creative_html' => $this->removeWindowsChars($record['html_code']),
            'is_original' => ($record['original_flag'] == 'N' ? 0 : 1), //
            'trigger_flag' => ($record['trigger_flag'] == 'N' ? 0 : 1), //
            'creative_date' => $this->fixBadDate($record['creative_date']), 
            'inactive_date' => $this->fixBadDate($record['inactive_date']), 
            'unsub_image' => $record['unsub_image'], 
            'default_subject' => $record['default_subject'], 
            'default_from' => $record['default_from'], 
            'image_directory' => $record['image_directory'], 
            'thumbnail' => $record['thumbnail'], 
            'date_approved' => $this->fixBadDatetime($record['date_approved']), 
            'approved_by' => $record['approved_by'], 
            'content_id' => $record['content_id'], 
            'header_id' => $record['header_id'], 
            'body_content_id' => $record['body_content_id'], 
            'style_id' => $record['style_id'], 
            'replace_flag' => ($record['replace_flag'] == 'N' ? 0 : 1), //
            'mediactivate_flag' => ($record['mediactivate_flag'] == 'N' ? 0 : 1), // 
            'hitpath_flag' => ($record['hitpath_flag'] == 'N' ? 0 : 1), //
            'comm_wizard_c3' => $record['comm_wizard_c3'], 
            'comm_wizard_cid' => $record['comm_wizard_cid'], 
            'comm_wizard_progid' => $record['comm_wizard_progid'], 
            'cr' => $record['cr'], 
            'landing_page' => $record['landing_page'], 
            'is_internally_approved' => ($record['internal_approved_flag'] == 'N' ? 0 : 1), //
            'internal_date_approved' => $this->fixBadDatetime($record['internal_date_approved']), 
            'internal_approved_by' => $record['internal_approved_by'], 
            'copywriter' => ($record['copywriter'] == 'N' ? 0 : 1), //
            'copywriter_name' => $record['copywriter_name'], 
            'original_html' => $record['original_html'], 
            'deleted_by' => $record['deleted_by'], 
            'host_images' => ($record['host_images'] == 'N' ? 0 : 1), //
            'needs_processing' => $record['needsProcessing']
        ];
    }

    private function fixBadDate($date) {
        if ('0000-00-00' === $date) {
            return null;
        }
        else {
            return Carbon::parse($date)->toDateString();
        }
    }

    private function fixBadDatetime($datetime) {
        if ('0000-00-00 00:00:00' === $datetime) {
            return null;
        }
        else {
            return Carbon::parse($datetime)->toDatetimeString();
        }
    }

    private function removeWindowsChars($html) {
        $html = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $html);
        return $html;
    }
}