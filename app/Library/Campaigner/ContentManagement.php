<?php
namespace App\Library\Campaigner;
class ContentManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'ListEmailTemplates' => '\\ListEmailTemplates',
      'Authentication' => '\\Authentication',
      'ListEmailTemplatesResponse' => '\\ListEmailTemplatesResponse',
      'ArrayOfEmailTemplateDescription' => '\\ArrayOfEmailTemplateDescription',
      'EmailTemplateDescription' => '\\EmailTemplateDescription',
      'ResponseHeader' => '\\ResponseHeader',
      'GetEmailTemplate' => '\\GetEmailTemplate',
      'GetEmailTemplateResponse' => '\\GetEmailTemplateResponse',
      'GetEmailTemplateResult' => '\\GetEmailTemplateResult',
      'EmailTemplateData' => '\\EmailTemplateData',
      'ListMediaFiles' => '\\ListMediaFiles',
      'ListMediaFilesResponse' => '\\ListMediaFilesResponse',
      'ArrayOfMediaFileDescription' => '\\ArrayOfMediaFileDescription',
      'MediaFileDescription' => '\\MediaFileDescription',
      'DeleteMediaFiles' => '\\DeleteMediaFiles',
      'ArrayOfInt' => '\\ArrayOfInt',
      'DeleteMediaFilesResponse' => '\\DeleteMediaFilesResponse',
      'UploadMediaFile' => '\\UploadMediaFile',
      'UploadMediaFileResponse' => '\\UploadMediaFileResponse',
      'UploadMediaFileResult' => '\\UploadMediaFileResult',
      'UploadMediaFileData' => '\\UploadMediaFileData',
      'ListProjects' => '\\ListProjects',
      'ListProjectsResponse' => '\\ListProjectsResponse',
      'ArrayOfProjectDescription' => '\\ArrayOfProjectDescription',
      'ProjectDescription' => '\\ProjectDescription',
      'CreateUpdateMyTemplates' => '\\CreateUpdateMyTemplates',
      'TemplateContent' => '\\TemplateContent',
      'CreateUpdateMyTemplatesResponse' => '\\CreateUpdateMyTemplatesResponse',
      'CreateUpdateMyTemplatesResult' => '\\CreateUpdateMyTemplatesResult',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @throws \Exception
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/contentmanagement.asmx?WSDL')
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
        try {
            parent::__construct($wsdl, $options);
        } catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * List Email Templates
     *
     * @param ListEmailTemplates $parameters
     * @return ListEmailTemplatesResponse
     */
    public function ListEmailTemplates(ListEmailTemplates $parameters)
    {
      return $this->__soapCall('ListEmailTemplates', array($parameters));
    }

    /**
     * Get Email Template
     *
     * @param GetEmailTemplate $parameters
     * @return GetEmailTemplateResponse
     */
    public function GetEmailTemplate(GetEmailTemplate $parameters)
    {
      return $this->__soapCall('GetEmailTemplate', array($parameters));
    }

    /**
     * List Media Files
     *
     * @param ListMediaFiles $parameters
     * @return ListMediaFilesResponse
     */
    public function ListMediaFiles(ListMediaFiles $parameters)
    {
      return $this->__soapCall('ListMediaFiles', array($parameters));
    }

    /**
     * Delete Media Files
     *
     * @param DeleteMediaFiles $parameters
     * @return DeleteMediaFilesResponse
     */
    public function DeleteMediaFiles(DeleteMediaFiles $parameters)
    {
      return $this->__soapCall('DeleteMediaFiles', array($parameters));
    }

    /**
     * Upload Media File
     *
     * @param UploadMediaFile $parameters
     * @return UploadMediaFileResponse
     */
    public function UploadMediaFile(UploadMediaFile $parameters)
    {
      return $this->__soapCall('UploadMediaFile', array($parameters));
    }

    /**
     * List Projects
     *
     * @param ListProjects $parameters
     * @return ListProjectsResponse
     */
    public function ListProjects(ListProjects $parameters)
    {
      return $this->__soapCall('ListProjects', array($parameters));
    }

    /**
     * Create or Update My Templates
     *
     * @param CreateUpdateMyTemplates $parameters
     * @return CreateUpdateMyTemplatesResponse
     */
    public function CreateUpdateMyTemplates(CreateUpdateMyTemplates $parameters)
    {
      return $this->__soapCall('CreateUpdateMyTemplates', array($parameters));
    }

}
