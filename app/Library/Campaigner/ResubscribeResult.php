<?php
namespace App\Library\Campaigner;
class ResubscribeResult
{
    const __default = 'Success';
    const Success = 'Success';
    const InternalError = 'InternalError';
    const InvalidContact = 'InvalidContact';
    const ContactIsDeleted = 'ContactIsDeleted';
    const ContactIsNotUnsubscribed = 'ContactIsNotUnsubscribed';
    const AccessDenied = 'AccessDenied';
    const Error = 'Error';
    const StatusRequired = 'StatusRequired';


}
