<?php
namespace App\Services;

use Symfony\Component\Yaml\Yaml;
use Storage;

class YamlProcessingService
{

  public function exportToYaml ( $modelObject , $filePath )
  {
    $rawRecords = $modelObject->all()->toArray();
    $recordsInYamlFormat = Yaml::dump( $rawRecords );

    Storage::disk( 'config' )->put( $filePath , $recordsInYamlFormat );
  }

  public function importYaml ( $modelObject , $filePath )
  {
    $configExists = Storage::disk( 'config' )->exists( $filePath );

    if ( !$configExists ){
      throw new \Exception('Config file ' . $filePath . ' does not exist.');
    }

    $recordsInYamlFormat = Storage::disk( 'config' )->get( $filePath );
    $recordsFromConfig = Yaml::parse( $recordsInYamlFormat );

    $modelObject->truncate();
    $modelObject->insert( $recordsFromConfig );

  }

}