#!/usr/bin/perl

# *****************************************************************************************
# upd_newsletter.cgi
#
# this page updates information in the newsletter table
#
# History
# Jim Sobeck, 11/15/06, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $images = $util->get_images_url;
my $camp_id;
my $pmesg;
my $rid;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Get the information about the user from the form 
#
my $nl_id = $query->param('nl_id');
my $send_confirm = $query->param('send_confirm');
my $backto = $query->param('backto');
my $nl_name = $query->param('nl_name');
$nl_name=~ s/'/''/g; 
my $nl_subject= $query->param('nl_subject');
my $nl_append_subject= $query->param('nl_append_subject');
$nl_subject=~ s/'/''/g; 
$nl_append_subject=~ s/'/''/g; 
my $nl_reminder_subject= $query->param('nl_reminder_subject');
$nl_reminder_subject=~ s/'/''/g; 
my $nl_from= $query->param('nl_from');
$nl_from=~ s/'/''/g; 
my $nl_from_address= $query->param('nl_from_address');
$nl_from_address=~ s/'/''/g; 
my $nl_reply_address= $query->param('nl_reply_address');
$nl_reply_address=~ s/'/''/g; 
my $nl_confirm_cnt= $query->param('nl_confirm_cnt');
my $nl_slots= $query->param('nl_slots');
if ($nl_confirm_cnt eq "")
{
	$nl_confirm_cnt=0;
}
my $nl_template= $query->param('nl_template');
if ($nl_template ne "")
{
}
else
{
	$nl_template='';
}
$nl_template=~ s/'/''/g; 
my $nl_confirmation = $query->param('nl_confirmation');
if ($nl_confirmation ne "")
{
}
else
{
	$nl_confirmation='';
}
$nl_confirmation=~ s/'/''/g; 
my $nl_reminder = $query->param('nl_reminder');
if ($nl_reminder ne "")
{
}
else
{
	$nl_reminder ='';
}
$nl_reminder=~ s/'/''/g; 
my $aid=$query->param('aid');
if ($aid eq "")
{
	$aid=0;
}
my $bid=$query->param('bid');
if ($bid eq "")
{
	$bid=0;
}
my $pid=$query->param('pid');
if ($pid eq "")
{
	$pid=0;
}
#
#
if ($nl_id > 0)
{
	$sql="select campaign_id from newsletter where nl_id=$nl_id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($camp_id)=$sth->fetchrow_array();
	$sth->finish();
	$sql="update campaign set advertiser_id=$aid,brand_id=$bid,profile_id=$pid where campaign_id=$camp_id";
	$sth = $dbhu->do($sql);
#
#
	$sql = "update newsletter set nl_name='$nl_name',nl_template='$nl_template',nl_confirmation='$nl_confirmation',nl_confirm_cnt=$nl_confirm_cnt,nl_from='$nl_from',nl_subject='$nl_subject',advertiser_id=$aid,brand_id=$bid,profile_id=$pid,nl_from_address='$nl_from_address',nl_reply_address='$nl_reply_address',nl_reminder_subject='$nl_reminder_subject',nl_reminder='$nl_reminder',nl_append_subject='$nl_append_subject',nl_slots=$nl_slots,send_confirm='$send_confirm' where nl_id=$nl_id";
}
else
{
	my $camp_str=$nl_name." Confirmation"; 
	$sql="insert into campaign(campaign_name,status,created_datetime,advertiser_id,brand_id,profile_id,campaign_type) values('$camp_str','C',now(),$aid,$bid,$pid,'NEWSLETTER')";
	$sth = $dbhu->do($sql);
	$sql="select campaign_id from campaign where campaign_name='$camp_str' and status='C' and advertiser_id=$aid and brand_id=$bid and profile_id=$pid"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($camp_id)=$sth->fetchrow_array();
	$sth->finish();


	$sql="insert into newsletter(nl_name,nl_template,nl_confirmation,nl_confirm_cnt,nl_status,nl_from,nl_subject,advertiser_id,brand_id,profile_id,campaign_id,nl_from_address,nl_reply_address,nl_reminder_subject,nl_reminder,nl_append_subject,nl_slots,send_confirm) values('$nl_name','$nl_template','$nl_confirmation',$nl_confirm_cnt,'A','$nl_from','$nl_subject',$aid,$bid,$pid,$camp_id,'$nl_from_address','$nl_reply_address','$nl_reminder_subject','$nl_reminder','$nl_append_subject',$nl_slots,'$send_confirm')";
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating newsletter record for $sql $user_id: $errmsg");
}
else
{
	if ($backto eq "")
	{
		print "Location: /cgi-bin/newsletter_list.cgi\n\n";
	}
	else
	{
		$_ = $backto;
		if (/preview.cgi/)
		{
    print qq {
    <script language="Javascript">
    var newwin = window.open("$backto", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
    $pmesg="";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/newsletter_disp.cgi?pmode=U&nl_id=$nl_id&pmesg=$pmesg&rid=$rid";
</script>
</body></html>
end_of_html
		}
		else
		{
			print "Location: $backto\n\n";
		}
	}
}
$util->clean_up();
exit(0);
