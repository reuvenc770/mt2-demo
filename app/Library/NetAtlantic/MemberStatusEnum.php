<?php

namespace App\Library\NetAtlantic;

class MemberStatusEnum
{
    const __default = 'normal';
    const normal = 'normal';
    const member = 'member';
    const confirm = 'confirm';
    const confirmfailed = 'confirm-failed';
    const aPrivate = 'private';
    const expired = 'expired';
    const held = 'held';
    const unsub = 'unsub';
    const referred = 'referred';
    const needsconfirm = 'needs-confirm';
    const needshello = 'needs-hello';
    const needsgoodbye = 'needs-goodbye';
    const complaint = 'complaint';


}
