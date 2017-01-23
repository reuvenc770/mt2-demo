<?php

namespace App\Library\NetAtlantic;


/**
 * gSOAP 2.7.16 generated service definition
 */
class NetAtlanticMananger extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'Array' => 'App\\Library\\NetAtlantic\\ArrayCustom',
      'Struct' => 'App\\Library\\NetAtlantic\\Struct',
      'duration' => 'App\\Library\\NetAtlantic\\duration',
      'dateTime' => 'App\\Library\\NetAtlantic\\dateTime',
      'NOTATION' => 'App\\Library\\NetAtlantic\\NOTATION',
      'time' => 'App\\Library\\NetAtlantic\\time',
      'date' => 'App\\Library\\NetAtlantic\\date',
      'gYearMonth' => 'App\\Library\\NetAtlantic\\gYearMonth',
      'gYear' => 'App\\Library\\NetAtlantic\\gYear',
      'gMonthDay' => 'App\\Library\\NetAtlantic\\gMonthDay',
      'gDay' => 'App\\Library\\NetAtlantic\\gDay',
      'gMonth' => 'App\\Library\\NetAtlantic\\gMonth',
      'boolean' => 'App\\Library\\NetAtlantic\\boolean',
      'base64Binary' => 'App\\Library\\NetAtlantic\\base64Binary',
      'hexBinary' => 'App\\Library\\NetAtlantic\\hexBinary',
      'float' => 'App\\Library\\NetAtlantic\\floatCustom',
      'double' => 'App\\Library\\NetAtlantic\\double',
      'anyURI' => 'App\\Library\\NetAtlantic\\anyURI',
      'QName' => 'App\\Library\\NetAtlantic\\QName',
      'string' => 'App\\Library\\NetAtlantic\\stringCustom',
      'normalizedString' => 'App\\Library\\NetAtlantic\\normalizedString',
      'token' => 'App\\Library\\NetAtlantic\\token',
      'language' => 'App\\Library\\NetAtlantic\\language',
      'Name' => 'App\\Library\\NetAtlantic\\Name',
      'NMTOKEN' => 'App\\Library\\NetAtlantic\\NMTOKEN',
      'NCName' => 'App\\Library\\NetAtlantic\\NCName',
      'NMTOKENS' => 'App\\Library\\NetAtlantic\\NMTOKENS',
      'ID' => 'App\\Library\\NetAtlantic\\ID',
      'IDREF' => 'App\\Library\\NetAtlantic\\IDREF',
      'ENTITY' => 'App\\Library\\NetAtlantic\\ENTITY',
      'IDREFS' => 'App\\Library\\NetAtlantic\\IDREFS',
      'ENTITIES' => 'App\\Library\\NetAtlantic\\ENTITIES',
      'decimal' => 'App\\Library\\NetAtlantic\\decimal',
      'integer' => 'App\\Library\\NetAtlantic\\integer',
      'nonPositiveInteger' => 'App\\Library\\NetAtlantic\\nonPositiveInteger',
      'negativeInteger' => 'App\\Library\\NetAtlantic\\negativeInteger',
      'long' => 'App\\Library\\NetAtlantic\\long',
      'int' => 'App\\Library\\NetAtlantic\\intCustom',
      'short' => 'App\\Library\\NetAtlantic\\short',
      'byte' => 'App\\Library\\NetAtlantic\\byte',
      'nonNegativeInteger' => 'App\\Library\\NetAtlantic\\nonNegativeInteger',
      'unsignedLong' => 'App\\Library\\NetAtlantic\\unsignedLong',
      'unsignedInt' => 'App\\Library\\NetAtlantic\\unsignedInt',
      'unsignedShort' => 'App\\Library\\NetAtlantic\\unsignedShort',
      'unsignedByte' => 'App\\Library\\NetAtlantic\\unsignedByte',
      'positiveInteger' => 'App\\Library\\NetAtlantic\\positiveInteger',
      'SimpleMailingStruct' => 'App\\Library\\NetAtlantic\\SimpleMailingStruct',
      'MessageStruct' => 'App\\Library\\NetAtlantic\\MessageStruct',
      'TMMessageStruct' => 'App\\Library\\NetAtlantic\\TMMessageStruct',
      'DocPart' => 'App\\Library\\NetAtlantic\\DocPart',
      'ContentStruct' => 'App\\Library\\NetAtlantic\\ContentStruct',
      'UrlTrackingStruct' => 'App\\Library\\NetAtlantic\\UrlTrackingStruct',
      'MemberStruct' => 'App\\Library\\NetAtlantic\\MemberStruct',
      'CharSetStruct' => 'App\\Library\\NetAtlantic\\CharSetStruct',
      'TinyMemberStruct' => 'App\\Library\\NetAtlantic\\TinyMemberStruct',
      'MailingStruct' => 'App\\Library\\NetAtlantic\\MailingStruct',
      'SegmentStruct' => 'App\\Library\\NetAtlantic\\SegmentStruct',
      'TrackingSummaryStruct' => 'App\\Library\\NetAtlantic\\TrackingSummaryStruct',
      'SimpleMemberStruct' => 'App\\Library\\NetAtlantic\\SimpleMemberStruct',
      'KeyValueType' => 'App\\Library\\NetAtlantic\\KeyValueType',
      'ListStruct' => 'App\\Library\\NetAtlantic\\ListStruct',
      'MemberBanStruct' => 'App\\Library\\NetAtlantic\\MemberBanStruct',
      'TopicStruct' => 'App\\Library\\NetAtlantic\\TopicStruct',
      'SiteStruct' => 'App\\Library\\NetAtlantic\\SiteStruct',
      'PreviewStruct' => 'App\\Library\\NetAtlantic\\PreviewStruct',
      'ServerAdminStruct' => 'App\\Library\\NetAtlantic\\ServerAdminStruct',
      'SiteAdminStruct' => 'App\\Library\\NetAtlantic\\SiteAdminStruct',
      'ArrayOfDocPart' => 'App\\Library\\NetAtlantic\\ArrayOfDocPart',
      'ArrayOfSimpleMemberStruct' => 'App\\Library\\NetAtlantic\\ArrayOfSimpleMemberStruct',
      'ArrayOfCharSetStruct' => 'App\\Library\\NetAtlantic\\ArrayOfCharSetStruct',
      'ArrayOfKeyValueType' => 'App\\Library\\NetAtlantic\\ArrayOfKeyValueType',
      'ArrayOfListStruct' => 'App\\Library\\NetAtlantic\\ArrayOfListStruct',
      'ArrayOfMailingStruct' => 'App\\Library\\NetAtlantic\\ArrayOfMailingStruct',
      'ArrayOfSegmentStruct' => 'App\\Library\\NetAtlantic\\ArrayOfSegmentStruct',
      'ArrayOfint' => 'App\\Library\\NetAtlantic\\ArrayOfint',
      'ArrayOfTrackingSummaryStruct' => 'App\\Library\\NetAtlantic\\ArrayOfTrackingSummaryStruct',
      'ArrayOfArrayOfstring' => 'App\\Library\\NetAtlantic\\ArrayOfArrayOfstring',
      'ArrayOfMemberStruct' => 'App\\Library\\NetAtlantic\\ArrayOfMemberStruct',
      'ArrayOfTinyMemberStruct' => 'App\\Library\\NetAtlantic\\ArrayOfTinyMemberStruct',
      'ArrayOfMemberBanStruct' => 'App\\Library\\NetAtlantic\\ArrayOfMemberBanStruct',
      'ArrayOfstring' => 'App\\Library\\NetAtlantic\\ArrayOfstring',
      'ArrayOfSimpleMailingStruct' => 'App\\Library\\NetAtlantic\\ArrayOfSimpleMailingStruct',
      'ArrayOfContentStruct' => 'App\\Library\\NetAtlantic\\ArrayOfContentStruct',
      'ArrayOfUrlTrackingStruct' => 'App\\Library\\NetAtlantic\\ArrayOfUrlTrackingStruct',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = null)
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'authentication' => 0,
      'login' => 'username',
      'password' => 'secret',
      'connection_timeout' => 60,
      'features' => 1,
    ), $options);
      if (!$wsdl) {
        $wsdl = 'http://zip.netatlantic.com:82/?wsdl';
      }
      parent::__construct($wsdl, $options);
    }

    /**
     * Service definition of function ns__ApiVersion
     *
     * @return string
     */
    public function ApiVersion()
    {
      return $this->__soapCall('ApiVersion', array());
    }

    /**
     * Service definition of function ns__CurrentUserEmailAddress
     *
     * @return string
     */
    public function CurrentUserEmailAddress()
    {
      return $this->__soapCall('CurrentUserEmailAddress', array());
    }

    /**
     * Service definition of function ns__DeleteMembers
     *
     * @param ArrayOfstring $FilterCriteriaArray
     * @return int
     */
    public function DeleteMembers(ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('DeleteMembers', array($FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__GetMemberID
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @return int
     */
    public function GetMemberID(SimpleMemberStruct $SimpleMemberStructIn)
    {
      return $this->__soapCall('GetMemberID', array($SimpleMemberStructIn));
    }

    /**
     * Service definition of function ns__CreateSingleMember
     *
     * @param stringCustom $EmailAddress
     * @param stringCustom $FullName
     * @param stringCustom $ListName
     * @return int
     */
    public function CreateSingleMember($EmailAddress, $FullName, $ListName)
    {
      return $this->__soapCall('CreateSingleMember', array($EmailAddress, $FullName, $ListName));
    }

    /**
     * Service definition of function ns__CreateManyMembers
     *
     * @param ArrayOfTinyMemberStruct $ArrayOfTinyMemberStructIn
     * @param stringCustom $ListName
     * @param boolean $SkipBadRecords
     * @return int
     */
    public function CreateManyMembers(ArrayOfTinyMemberStruct $ArrayOfTinyMemberStructIn, $ListName, boolean $SkipBadRecords)
    {
      return $this->__soapCall('CreateManyMembers', array($ArrayOfTinyMemberStructIn, $ListName, $SkipBadRecords));
    }

    /**
     * Service definition of function ns__SqlSelect
     *
     * @param stringCustom $SqlStatement
     * @return ArrayOfArrayOfstring
     */
    public function SqlSelect($SqlStatement)
    {
      return $this->__soapCall('SqlSelect', array($SqlStatement));
    }

    /**
     * Service definition of function ns__SqlInsert
     *
     * @param stringCustom $SqlStatement
     * @param ArrayOfKeyValueType $DataArray
     * @param boolean $ReturnID
     * @return int
     */
    public function SqlInsert($SqlStatement, ArrayOfKeyValueType $DataArray, boolean $ReturnID)
    {
      return $this->__soapCall('SqlInsert', array($SqlStatement, $DataArray, $ReturnID));
    }

    /**
     * Service definition of function ns__SqlUpdate
     *
     * @param stringCustom $SqlStatement
     * @param ArrayOfKeyValueType $DataArray
     * @param stringCustom $SqlWhere
     * @return boolean
     */
    public function SqlUpdate($SqlStatement, ArrayOfKeyValueType $DataArray, $SqlWhere)
    {
      return $this->__soapCall('SqlUpdate', array($SqlStatement, $DataArray, $SqlWhere));
    }

    /**
     * Service definition of function ns__SqlDelete
     *
     * @param stringCustom $TableName
     * @param stringCustom $SqlWhere
     * @return boolean
     */
    public function SqlDelete($TableName, $SqlWhere)
    {
      return $this->__soapCall('SqlDelete', array($TableName, $SqlWhere));
    }

    /**
     * Service definition of function ns__UpdateMemberPassword
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param stringCustom $Password
     * @return boolean
     */
    public function UpdateMemberPassword(SimpleMemberStruct $SimpleMemberStructIn, $Password)
    {
      return $this->__soapCall('UpdateMemberPassword', array($SimpleMemberStructIn, $Password));
    }

    /**
     * Service definition of function ns__CheckMemberPassword
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param stringCustom $Password
     * @return boolean
     */
    public function CheckMemberPassword(SimpleMemberStruct $SimpleMemberStructIn, $Password)
    {
      return $this->__soapCall('CheckMemberPassword', array($SimpleMemberStructIn, $Password));
    }

    /**
     * Service definition of function ns__CopyMember
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param stringCustom $EmailAddress
     * @param stringCustom $FullName
     * @param stringCustom $ListName
     * @return int
     */
    public function CopyMember(SimpleMemberStruct $SimpleMemberStructIn, $EmailAddress, $FullName, $ListName)
    {
      return $this->__soapCall('CopyMember', array($SimpleMemberStructIn, $EmailAddress, $FullName, $ListName));
    }

    /**
     * Service definition of function ns__CreateList
     *
     * @param ListTypeEnum $ListTypeEnumIn Constant: string - Valid values: marketing, announcement-moderated, discussion-moderated, discussion-unmoderated
     * @param stringCustom $ListName
     * @param stringCustom $ShortDescription
     * @param stringCustom $AdminName
     * @param stringCustom $AdminEmail
     * @param stringCustom $AdminPassword
     * @param stringCustom $Topic
     * @return int
     */
    public function CreateList($ListTypeEnumIn, $ListName, $ShortDescription, $AdminName, $AdminEmail, $AdminPassword, $Topic)
    {
      return $this->__soapCall('CreateList', array($ListTypeEnumIn, $ListName, $ShortDescription, $AdminName, $AdminEmail, $AdminPassword, $Topic));
    }

    /**
     * Service definition of function ns__DeleteList
     *
     * @param stringCustom $ListName
     * @return boolean
     */
    public function DeleteList($ListName)
    {
      return $this->__soapCall('DeleteList', array($ListName));
    }

    /**
     * Service definition of function ns__EmailOnWhatLists
     *
     * @param stringCustom $EmailAddress
     * @return ArrayOfstring
     */
    public function EmailOnWhatLists($EmailAddress)
    {
      return $this->__soapCall('EmailOnWhatLists', array($EmailAddress));
    }

    /**
     * Service definition of function ns__EmailPasswordOnWhatLists
     *
     * @param stringCustom $EmailAddress
     * @param stringCustom $Password
     * @return ArrayOfstring
     */
    public function EmailPasswordOnWhatLists($EmailAddress, $Password)
    {
      return $this->__soapCall('EmailPasswordOnWhatLists', array($EmailAddress, $Password));
    }

    /**
     * Service definition of function ns__CreateListAdmin
     *
     * @param stringCustom $AdminEmail
     * @param stringCustom $AdminPassword
     * @param stringCustom $AdminListName
     * @param stringCustom $AdminFullName
     * @param boolean $ReceiveListAdminMail
     * @param boolean $ReceiveModerationNotification
     * @param boolean $BypassListModeration
     * @return int
     */
    public function CreateListAdmin($AdminEmail, $AdminPassword, $AdminListName, $AdminFullName, boolean $ReceiveListAdminMail, boolean $ReceiveModerationNotification, boolean $BypassListModeration)
    {
      return $this->__soapCall('CreateListAdmin', array($AdminEmail, $AdminPassword, $AdminListName, $AdminFullName, $ReceiveListAdminMail, $ReceiveModerationNotification, $BypassListModeration));
    }

    /**
     * Service definition of function ns__CreateMemberBan
     *
     * @param MemberBanStruct $MemberBanStructIn
     * @return int
     */
    public function CreateMemberBan(MemberBanStruct $MemberBanStructIn)
    {
      return $this->__soapCall('CreateMemberBan', array($MemberBanStructIn));
    }

    /**
     * Service definition of function ns__GetEmailFromMemberID
     *
     * @param intCustom $MemberID
     * @return string
     */
    public function GetEmailFromMemberID($MemberID)
    {
      return $this->__soapCall('GetEmailFromMemberID', array($MemberID));
    }

    /**
     * Service definition of function ns__GetListID
     *
     * @param stringCustom $ListName
     * @return int
     */
    public function GetListID($ListName)
    {
      return $this->__soapCall('GetListID', array($ListName));
    }

    /**
     * Service definition of function ns__GetListnameFromMemberID
     *
     * @param intCustom $MemberID
     * @return string
     */
    public function GetListnameFromMemberID($MemberID)
    {
      return $this->__soapCall('GetListnameFromMemberID', array($MemberID));
    }

    /**
     * Service definition of function ns__ImportContent
     *
     * @param intCustom $ContentID
     * @return SimpleMailingStruct
     */
    public function ImportContent($ContentID)
    {
      return $this->__soapCall('ImportContent', array($ContentID));
    }

    /**
     * Service definition of function ns__SelectMembers
     *
     * @param ArrayOfstring $FilterCriteriaArray
     * @return ArrayOfMemberStruct
     */
    public function SelectMembers(ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('SelectMembers', array($FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__SelectSimpleMembers
     *
     * @param ArrayOfstring $FilterCriteriaArray
     * @return ArrayOfSimpleMemberStruct
     */
    public function SelectSimpleMembers(ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('SelectSimpleMembers', array($FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__SendMailing
     *
     * @param intCustom $SegmentID
     * @param MailingStruct $MailingStructIn
     * @return int
     */
    public function SendMailing($SegmentID, MailingStruct $MailingStructIn)
    {
      return $this->__soapCall('SendMailing', array($SegmentID, $MailingStructIn));
    }

    /**
     * Service definition of function ns__MailingStatus
     *
     * @param intCustom $MailingID
     * @return string
     */
    public function MailingStatus($MailingID)
    {
      return $this->__soapCall('MailingStatus', array($MailingID));
    }

    /**
     * Service definition of function ns__ScheduleMailing
     *
     * @param intCustom $SegmentID
     * @param dateTime $SendDate
     * @param MailingStruct $MailingStructIn
     * @return int
     */
    public function ScheduleMailing($SegmentID, dateTime $SendDate, MailingStruct $MailingStructIn)
    {
      return $this->__soapCall('ScheduleMailing', array($SegmentID, $SendDate, $MailingStructIn));
    }

    /**
     * Service definition of function ns__ModerateMailing
     *
     * @param intCustom $ModerateID
     * @param boolean $Accept
     * @param boolean $SendRejectMessage
     * @return boolean
     */
    public function ModerateMailing($ModerateID, boolean $Accept, boolean $SendRejectMessage)
    {
      return $this->__soapCall('ModerateMailing', array($ModerateID, $Accept, $SendRejectMessage));
    }

    /**
     * Service definition of function ns__SelectContent
     *
     * @param ArrayOfstring $FilterCriteriaArray
     * @return ArrayOfContentStruct
     */
    public function SelectContent(ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('SelectContent', array($FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__SelectLists
     *
     * @param stringCustom $ListName
     * @param stringCustom $SiteName
     * @return ArrayOfListStruct
     */
    public function SelectLists($ListName, $SiteName)
    {
      return $this->__soapCall('SelectLists', array($ListName, $SiteName));
    }

    /**
     * Service definition of function ns__SelectSegments
     *
     * @param ArrayOfstring $FilterCriteriaArray
     * @return ArrayOfSegmentStruct
     */
    public function SelectSegments(ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('SelectSegments', array($FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__SendMailingDirect
     *
     * @param ArrayOfstring $EmailAddressArray
     * @param ArrayOfint $MemberIDArray
     * @param MailingStruct $MailingStructIn
     * @return int
     */
    public function SendMailingDirect(ArrayOfstring $EmailAddressArray, ArrayOfint $MemberIDArray, MailingStruct $MailingStructIn)
    {
      return $this->__soapCall('SendMailingDirect', array($EmailAddressArray, $MemberIDArray, $MailingStructIn));
    }

    /**
     * Service definition of function ns__SendMemberDoc
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param MessageTypeEnum $DocTypeIn Constant: string - Valid values: confirm, hello, goodbye, held, private, delivery
     * @return int
     */
    public function SendMemberDoc(SimpleMemberStruct $SimpleMemberStructIn, $DocTypeIn)
    {
      return $this->__soapCall('SendMemberDoc', array($SimpleMemberStructIn, $DocTypeIn));
    }

    /**
     * Service definition of function ns__TrackingSummary
     *
     * @param intCustom $OutMailID
     * @return TrackingSummaryStruct
     */
    public function TrackingSummary($OutMailID)
    {
      return $this->__soapCall('TrackingSummary', array($OutMailID));
    }

    /**
     * Service definition of function ns__Unsubscribe
     *
     * @param ArrayOfSimpleMemberStruct $SimpleMemberStructArrayIn
     * @return int
     */
    public function Unsubscribe(ArrayOfSimpleMemberStruct $SimpleMemberStructArrayIn)
    {
      return $this->__soapCall('Unsubscribe', array($SimpleMemberStructArrayIn));
    }

    /**
     * Service definition of function ns__UpdateMemberEmail
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param stringCustom $EmailAddress
     * @return boolean
     */
    public function UpdateMemberEmail(SimpleMemberStruct $SimpleMemberStructIn, $EmailAddress)
    {
      return $this->__soapCall('UpdateMemberEmail', array($SimpleMemberStructIn, $EmailAddress));
    }

    /**
     * Service definition of function ns__UpdateMemberKind
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param MemberKindEnum $MemberKind Constant: string - Valid values: digest, daymimedigest, mimedigest, index, nomail, mail, expired
     * @return boolean
     */
    public function UpdateMemberKind(SimpleMemberStruct $SimpleMemberStructIn, $MemberKind)
    {
      return $this->__soapCall('UpdateMemberKind', array($SimpleMemberStructIn, $MemberKind));
    }

    /**
     * Service definition of function ns__UpdateMemberStatus
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param MemberStatusEnum $MemberStatus Constant: string - Valid values: normal, member, confirm, confirm-failed, private, expired, held, unsub, referred, needs-confirm, needs-hello, needs-goodbye, complaint
     * @return boolean
     */
    public function UpdateMemberStatus(SimpleMemberStruct $SimpleMemberStructIn, $MemberStatus)
    {
      return $this->__soapCall('UpdateMemberStatus', array($SimpleMemberStructIn, $MemberStatus));
    }

    /**
     * Service definition of function ns__UpdateList
     *
     * @param ListStruct $ListStructIn
     * @return boolean
     */
    public function UpdateList(ListStruct $ListStructIn)
    {
      return $this->__soapCall('UpdateList', array($ListStructIn));
    }

    /**
     * Service definition of function ns__UpdateListAdmin
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param boolean $IsListAdmin
     * @param boolean $ReceiveListAdminMail
     * @param boolean $ReceiveModerationNotification
     * @param boolean $BypassListModeration
     * @return boolean
     */
    public function UpdateListAdmin(SimpleMemberStruct $SimpleMemberStructIn, boolean $IsListAdmin, boolean $ReceiveListAdminMail, boolean $ReceiveModerationNotification, boolean $BypassListModeration)
    {
      return $this->__soapCall('UpdateListAdmin', array($SimpleMemberStructIn, $IsListAdmin, $ReceiveListAdminMail, $ReceiveModerationNotification, $BypassListModeration));
    }

    /**
     * Service definition of function ns__UpdateMemberDemographics
     *
     * @param SimpleMemberStruct $SimpleMemberStructIn
     * @param ArrayOfKeyValueType $DemographicsArray
     * @return boolean
     */
    public function UpdateMemberDemographics(SimpleMemberStruct $SimpleMemberStructIn, ArrayOfKeyValueType $DemographicsArray)
    {
      return $this->__soapCall('UpdateMemberDemographics', array($SimpleMemberStructIn, $DemographicsArray));
    }

    /**
     * Service definition of function ns__CreateMemberColumn
     *
     * @param stringCustom $FieldName
     * @param FieldTypeEnum $FieldType Constant: string - Valid values: integer, date, char1, char2, char3, varchar5, varchar10, varchar20, varchar30, varchar40, varchar50, varchar60, varchar70, varchar80, varchar90, varchar100, varchar150, varchar200, varchar250
     * @return boolean
     */
    public function CreateMemberColumn($FieldName, $FieldType)
    {
      return $this->__soapCall('CreateMemberColumn', array($FieldName, $FieldType));
    }

    /**
     * Service definition of function ns__DeleteMemberColumn
     *
     * @param stringCustom $FieldName
     * @return boolean
     */
    public function DeleteMemberColumn($FieldName)
    {
      return $this->__soapCall('DeleteMemberColumn', array($FieldName));
    }

    /**
     * Service definition of function ns__CreateSegment
     *
     * @param SegmentStruct $SegmentStructIn
     * @return int
     */
    public function CreateSegment(SegmentStruct $SegmentStructIn)
    {
      return $this->__soapCall('CreateSegment', array($SegmentStructIn));
    }

    /**
     * Service definition of function ns__UpdateSegment
     *
     * @param SegmentStruct $SegmentStructIn
     * @return boolean
     */
    public function UpdateSegment(SegmentStruct $SegmentStructIn)
    {
      return $this->__soapCall('UpdateSegment', array($SegmentStructIn));
    }

    /**
     * Service definition of function ns__DeleteSegment
     *
     * @param intCustom $SegmentID
     * @return boolean
     */
    public function DeleteSegment($SegmentID)
    {
      return $this->__soapCall('DeleteSegment', array($SegmentID));
    }

    /**
     * Service definition of function ns__SendMessage
     *
     * @param MessageStruct $MessageStructIn
     * @return int
     */
    public function SendMessage(MessageStruct $MessageStructIn)
    {
      return $this->__soapCall('SendMessage', array($MessageStructIn));
    }

    /**
     * Service definition of function ns__CreateSite
     *
     * @param SiteStruct $SiteStructIn
     * @return int
     */
    public function CreateSite(SiteStruct $SiteStructIn)
    {
      return $this->__soapCall('CreateSite', array($SiteStructIn));
    }

    /**
     * Service definition of function ns__UpdateSite
     *
     * @param SiteStruct $SiteStructIn
     * @return boolean
     */
    public function UpdateSite(SiteStruct $SiteStructIn)
    {
      return $this->__soapCall('UpdateSite', array($SiteStructIn));
    }

    /**
     * Service definition of function ns__DeleteSite
     *
     * @param intCustom $SiteID
     * @return boolean
     */
    public function DeleteSite($SiteID)
    {
      return $this->__soapCall('DeleteSite', array($SiteID));
    }

    /**
     * Service definition of function ns__CreateTopic
     *
     * @param TopicStruct $TopicStructIn
     * @return boolean
     */
    public function CreateTopic(TopicStruct $TopicStructIn)
    {
      return $this->__soapCall('CreateTopic', array($TopicStructIn));
    }

    /**
     * Service definition of function ns__UpdateTopic
     *
     * @param TopicStruct $TopicStructIn
     * @return boolean
     */
    public function UpdateTopic(TopicStruct $TopicStructIn)
    {
      return $this->__soapCall('UpdateTopic', array($TopicStructIn));
    }

    /**
     * Service definition of function ns__DeleteTopic
     *
     * @param stringCustom $TopicTitle
     * @return boolean
     */
    public function DeleteTopic($TopicTitle)
    {
      return $this->__soapCall('DeleteTopic', array($TopicTitle));
    }

    /**
     * Service definition of function ns__GetPreviewMailing
     *
     * @param PreviewStruct $PreviewStructIn
     * @return string
     */
    public function GetPreviewMailing(PreviewStruct $PreviewStructIn)
    {
      return $this->__soapCall('GetPreviewMailing', array($PreviewStructIn));
    }

    /**
     * Service definition of function ns__CreateServerAdmin
     *
     * @param ServerAdminStruct $ServerAdminStructIn
     * @return int
     */
    public function CreateServerAdmin(ServerAdminStruct $ServerAdminStructIn)
    {
      return $this->__soapCall('CreateServerAdmin', array($ServerAdminStructIn));
    }

    /**
     * Service definition of function ns__UpdateServerAdmin
     *
     * @param ServerAdminStruct $ServerAdminStructIn
     * @return boolean
     */
    public function UpdateServerAdmin(ServerAdminStruct $ServerAdminStructIn)
    {
      return $this->__soapCall('UpdateServerAdmin', array($ServerAdminStructIn));
    }

    /**
     * Service definition of function ns__DeleteServerAdmin
     *
     * @param ServerAdminStruct $ServerAdminStructIn
     * @return boolean
     */
    public function DeleteServerAdmin(ServerAdminStruct $ServerAdminStructIn)
    {
      return $this->__soapCall('DeleteServerAdmin', array($ServerAdminStructIn));
    }

    /**
     * Service definition of function ns__CreateSiteAdmin
     *
     * @param SiteAdminStruct $SiteAdminStructIn
     * @return int
     */
    public function CreateSiteAdmin(SiteAdminStruct $SiteAdminStructIn)
    {
      return $this->__soapCall('CreateSiteAdmin', array($SiteAdminStructIn));
    }

    /**
     * Service definition of function ns__UpdateSiteAdmin
     *
     * @param SiteAdminStruct $SiteAdminStructIn
     * @return boolean
     */
    public function UpdateSiteAdmin(SiteAdminStruct $SiteAdminStructIn)
    {
      return $this->__soapCall('UpdateSiteAdmin', array($SiteAdminStructIn));
    }

    /**
     * Service definition of function ns__DeleteSiteAdmin
     *
     * @param SiteAdminStruct $SiteAdminStructIn
     * @return boolean
     */
    public function DeleteSiteAdmin(SiteAdminStruct $SiteAdminStructIn)
    {
      return $this->__soapCall('DeleteSiteAdmin', array($SiteAdminStructIn));
    }

    /**
     * Service definition of function ns__CreateContent
     *
     * @param ContentStruct $ContentStructIn
     * @return int
     */
    public function CreateContent(ContentStruct $ContentStructIn)
    {
      return $this->__soapCall('CreateContent', array($ContentStructIn));
    }

    /**
     * Service definition of function ns__UpdateContent
     *
     * @param ContentStruct $ContentStructIn
     * @return boolean
     */
    public function UpdateContent(ContentStruct $ContentStructIn)
    {
      return $this->__soapCall('UpdateContent', array($ContentStructIn));
    }

    /**
     * Service definition of function ns__DeleteContent
     *
     * @param ContentStruct $ContentStructIn
     * @return boolean
     */
    public function DeleteContent(ContentStruct $ContentStructIn)
    {
      return $this->__soapCall('DeleteContent', array($ContentStructIn));
    }

    /**
     * Service definition of function ns__SelectListsEx
     *
     * @param stringCustom $ListName
     * @param stringCustom $SiteName
     * @param ArrayOfstring $FieldsToFetch
     * @param ArrayOfstring $WhereClause
     * @return ArrayOfArrayOfstring
     */
    public function SelectListsEx($ListName, $SiteName, ArrayOfstring $FieldsToFetch, ArrayOfstring $WhereClause)
    {
      return $this->__soapCall('SelectListsEx', array($ListName, $SiteName, $FieldsToFetch, $WhereClause));
    }

    /**
     * Service definition of function ns__SelectMembersEx
     *
     * @param ArrayOfstring $FieldsToFetch
     * @param ArrayOfstring $FilterCriteriaArray
     * @return ArrayOfArrayOfstring
     */
    public function SelectMembersEx(ArrayOfstring $FieldsToFetch, ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('SelectMembersEx', array($FieldsToFetch, $FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__SelectOutmailsEx
     *
     * @param ArrayOfstring $FieldsToFetch
     * @param ArrayOfstring $FilterCriteriaArray
     * @return ArrayOfArrayOfstring
     */
    public function SelectOutmailsEx(ArrayOfstring $FieldsToFetch, ArrayOfstring $FilterCriteriaArray)
    {
      return $this->__soapCall('SelectOutmailsEx', array($FieldsToFetch, $FilterCriteriaArray));
    }

    /**
     * Service definition of function ns__TMSendMessage
     *
     * @param TMMessageStruct $TMMessageStructIn
     * @return unsignedLong
     */
    public function TMSendMessage(TMMessageStruct $TMMessageStructIn)
    {
      return $this->__soapCall('TMSendMessage', array($TMMessageStructIn));
    }

}
