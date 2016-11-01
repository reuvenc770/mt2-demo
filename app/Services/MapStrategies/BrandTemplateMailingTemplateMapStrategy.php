<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class BrandTemplateMailingTemplateMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['template_id'],
            'template_name' => $record['template_name'],
            'template_type' => 1, #$record['approved_flag'], // hard-coded to Normal HTML, at least for now
            'template_html' => $record['html_code'],
            'template_text' => $this->returnText($record['html_code'])
        ];
    }

    public function returnText($html) {
        return '';
    }
}