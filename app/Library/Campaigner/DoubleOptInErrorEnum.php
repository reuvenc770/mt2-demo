<?php
namespace App\Library\Campaigner;
class DoubleOptInErrorEnum
{
    const __default = 'Success';
    const Success = 'Success';
    const InvalidFormId = 'InvalidFormId';
    const FormNotComplete = 'FormNotComplete';
    const ContactFilterRequired = 'ContactFilterRequired';
    const InvalidContactKey = 'InvalidContactKey';
    const ContactSearchXMLNoResults = 'ContactSearchXMLNoResults';
    const ContactSearchXMLInvalid = 'ContactSearchXMLInvalid';
    const InvalidContactId = 'InvalidContactId';
    const InvalidContactStatus = 'InvalidContactStatus';


}
