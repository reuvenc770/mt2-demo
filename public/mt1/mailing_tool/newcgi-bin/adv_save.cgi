#!/usr/bin/perl
# *****************************************************************************************
# list_save.cgi
#
# this page saves the list changes
#
# History
# Grady Nash, 8/30/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util_mail;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my @textads= $query->param('textads');
my $oldaid = $query->param('oldaid');
my $uid= $query->param('uid');
my $aname = $query->param('advertiser_name');
my $cstatus= $query->param('cstatus');
my $priority= $query->param('priority');
$aname=~ s/'/''/g;
my $include_creative = $query->param('include_creative');
my $newaid;
my $test_flag;

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

my $aurl;
$sql="select advertiser_url from advertiser_info where advertiser_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($oldaid);
($aurl)=$sth->fetchrow_array();
$sth->finish();
$aurl=~s/xxagentidxx_jjhitjj/jjhitjj/g;
$aurl=~s/xxagentidxx/jjhitjj/g;

$test_flag="N";
if ($cstatus eq "T")
{
	$cstatus="A";
	$test_flag="Y";
}
elsif ($cstatus eq "P")
{
	$cstatus="I";
	$test_flag="P";
}
elsif ($cstatus eq "U")
{
	$cstatus="A";
	$test_flag="U";
}

if (($cstatus eq "I") or ($cstatus eq "A" and $test_flag eq "N") or ($cstatus eq "A" and $test_flag eq "P"))
{
	$priority=1;
}
else
{
	$sql="update advertiser_info set priority=priority+1 where priority >= $priority and ((status not in ('A','I')) or (status='A' and test_flag in ('Y','U')))";
	$rows=$dbhu->do($sql);
}
if ($include_creative eq "Y")
{
	$sql = "insert into advertiser_info(advertiser_name,email_addr,internal_email_addr,physical_addr,status,offer_type,payout,payout_pounds,vendor_supp_list_id,suppression_url,auto_download,suppression_username,suppression_password,category_id,unsub_image,unsub_link,unsub_use,unsub_text,advertiser_url,company_id,friendly_advertiser_name,track_internally,priority,test_flag,pass_tracking,sourceInternal) select '$aname',email_addr,internal_email_addr,physical_addr,'$cstatus','CPA',payout,payout_pounds,vendor_supp_list_id,suppression_url,auto_download,suppression_username,suppression_password,category_id,unsub_image,unsub_link,unsub_use,unsub_text,'$aurl',company_id,friendly_advertiser_name,track_internally,$priority,'$test_flag','Y','Y' from advertiser_info where advertiser_id=$oldaid";
}
else
{
	$sql = "insert into advertiser_info(advertiser_name,email_addr,internal_email_addr,physical_addr,status,offer_type,vendor_supp_list_id,unsub_use,unsub_text,company_id,advertiser_url,friendly_advertiser_name,track_internally,priority,test_flag,pass_tracking,sourceInternal) select '$aname',email_addr,internal_email_addr,physical_addr,'$cstatus','CPA',1,unsub_use,unsub_text,company_id,'$aurl',friendly_advertiser_name,track_internally,$priority,'$test_flag','Y','Y' from advertiser_info where advertiser_id=$oldaid";
}
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating advertiser_info $sql: $errmsg");
	exit(0);
}
$sql = "select max(advertiser_id) from advertiser_info where advertiser_name='$aname'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($newaid) = $sth->fetchrow_array();
$sth->finish();

if ($util->getConfigVal("AUTO_APPROVE"))
{
	$sql="update advertiser_info set date_approved=curdate(),approved_by='AUTO',advertiser_url_date_approved=curdate(),advertiser_url_approved_by='AUTO' where advertiser_id=$newaid";
	$rows = $dbhu->do($sql);
}
$sql = "insert into advertiser_contact_info(advertiser_id,contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes) select $newaid,contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes from advertiser_contact_info where advertiser_id=$oldaid";
$rows = $dbhu->do($sql);

$sql = "insert into advertiser_approval(advertiser_id,email_addr) select $newaid,email_addr from advertiser_approval where advertiser_id=$oldaid";
$rows = $dbhu->do($sql);
$sql = "insert into advertiser_email(advertiser_id,email_addr) select $newaid,email_addr from advertiser_email where advertiser_id=$oldaid";
$rows = $dbhu->do($sql);
$sql = "insert into advertiser_seedlist(advertiser_id,email_addr) select $newaid,email_addr from advertiser_seedlist where advertiser_id=$oldaid";
$rows = $dbhu->do($sql);
#
if ($include_creative eq "Y")
{
	$sql = "insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter) select $newaid,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter from advertiser_from where advertiser_id=$oldaid";
	$rows = $dbhu->do($sql);
	$sql = "insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter) select $newaid,advertiser_subject,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter from advertiser_subject where advertiser_id=$oldaid and advertiser_subject.status='A'";
	$rows = $dbhu->do($sql);
	$sql = "insert into creative(advertiser_id,status,creative_name,original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,date_approved,approved_by,internal_approved_flag,internal_date_approved,internal_approved_by,hitpath_flag,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page,copywriter) select $newaid,status,creative_name,original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,date_approved,approved_by,internal_approved_flag,internal_date_approved,internal_approved_by,hitpath_flag,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page,copywriter from creative where advertiser_id=$oldaid";
	$rows = $dbhu->do($sql);
	#
	# Get the default subject and from old advertiser
	#
	my ($sname,$cstatus,$date_approved,$approved_by,$cid);
	my $sid;
	my $sth1;
$|=1 ;
	$sql = "select advertiser_subject,advertiser_subject.status,advertiser_subject.date_approved,advertiser_subject.approved_by,creative_id from advertiser_subject,creative where default_subject=subject_id and creative.advertiser_id=$oldaid and advertiser_subject.status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($sname,$cstatus,$date_approved,$approved_by,$cid) = $sth->fetchrow_array())
	{
		$sql = "select subject_id from advertiser_subject where advertiser_subject='$sname' and status='$cstatus' and advertiser_id=$newaid";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($sid) = $sth1->fetchrow_array();
		$sth1->finish();
		$sql = "update creative set default_subject=$sid where creative_id=$cid";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
	$sql = "select advertiser_from,advertiser_from.status,advertiser_from.date_approved,advertiser_from.approved_by,creative_id from advertiser_from,creative where default_from=from_id and creative.advertiser_id=$newaid";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($sname,$cstatus,$date_approved,$approved_by,$cid) = $sth->fetchrow_array())
	{
		$sql = "select from_id from advertiser_from where advertiser_from='$sname' and status='$cstatus' and advertiser_id=$newaid";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($sid) = $sth1->fetchrow_array();
		$sth1->finish();
		$sql = "update creative set default_from=$sid where creative_id=$cid";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
#
#	$sql = "insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal) select $newaid,url,code,date_added,client_id,link_id,daily_deal from advertiser_tracking where advertiser_id=$oldaid";
#	$rows = $dbhu->do($sql);
   $sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id and advertiser_tracking.advertiser_id=$newaid) where advertiser_id=$newaid";
   $rows = $dbhu->do($sql);
	#
	# Copy the advertiser rotation
	#
	$sql="insert into advertiser_setup(advertiser_id,date_modified,class_id,";
	my $tstr;
	my $i=1;
	while ($i <= 15)
	{
		$tstr=$tstr."creative".$i."_id,";
		$i++;
	}
	$i=1;
	while ($i <= 30)
	{
		$tstr=$tstr."subject".$i.",";
		$i++;
	}
	$i=1;
	while ($i <= 20)
	{
		$tstr=$tstr."from".$i.",";
		$i++;
	}
	chop($tstr);
	$sql=$sql.$tstr.") select ".$newaid.",now(),class_id,".$tstr." from advertiser_setup where advertiser_id=$oldaid";
   $rows = $dbhu->do($sql);

	$sql="select class_id,".$tstr." from advertiser_setup where advertiser_id=$oldaid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $newcid;
	while (my $hrInfo=$sth->fetchrow_hashref)
	{
		my $class_id=$hrInfo->{"class_id"};
		for (my $i=1; $i <= 15; $i++)
        {
			my $cid=$hrInfo->{"creative${i}_id"};
        	if ($cid > 0)
            {
				$sql = "select creative_id from creative where creative_name in (select creative_name from creative where creative_id=? and advertiser_id=?) and advertiser_id=? order by creative_id limit 1";
				my $stha=$dbhu->prepare($sql);
				$stha->execute($cid,$oldaid,$newaid);
				if (($newcid)=$stha->fetchrow_array())
				{
					$sql="update advertiser_setup set creative${i}_id=$newcid where advertiser_id=$newaid and class_id=$class_id";
   					$rows = $dbhu->do($sql);
				}
				$stha->finish();
            }
        }
		for (my $i=1; $i <= 30; $i++)
        {
			my $cid=$hrInfo->{"subject${i}"};
        	if ($cid > 0)
            {
				$sql = "select subject_id from advertiser_subject where advertiser_subject in (select advertiser_subject from advertiser_subject where subject_id=? and advertiser_id=?) and advertiser_id=? order by subject_id limit 1";
				my $stha=$dbhu->prepare($sql);
				$stha->execute($cid,$oldaid,$newaid);
				if (($newcid)=$stha->fetchrow_array())
				{
					$sql="update advertiser_setup set subject${i}=$newcid where advertiser_id=$newaid and class_id=$class_id";
   					$rows = $dbhu->do($sql);
				}
				$stha->finish();
            }
        }
		for (my $i=1; $i <= 20; $i++)
        {
			my $cid=$hrInfo->{"from${i}"};
        	if ($cid > 0)
            {
				$sql = "select from_id from advertiser_from where advertiser_from in (select advertiser_from from advertiser_from where from_id=? and advertiser_id=?) and advertiser_id=? order by from_id limit 1";
				my $stha=$dbhu->prepare($sql);
				$stha->execute($cid,$oldaid,$newaid);
				if (($newcid)=$stha->fetchrow_array())
				{
					$sql="update advertiser_setup set from${i}=$newcid where advertiser_id=$newaid and class_id=$class_id";
   					$rows = $dbhu->do($sql);
				}
				$stha->finish();
            }
        }
	}
	$sth->finish();
}
else
{
#	$sql = "insert into advertiser_from(advertiser_id,advertiser_from) values($newaid,'{{FOOTER_SUBDOMAIN}}')";
#	$rows = $dbhu->do($sql);
}
my $format = "H"; 
my $email_str;
my $cid;
my $cemail;

$sql = "select advertiser_name,company_id from advertiser_info where advertiser_id=$newaid"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
($aname,$cid) = $sth->fetchrow_array();
$sth->finish();
$email_str = "";
$email_str="group.approvals\@zetainteractive.com,";
$sql = "select cm.email_addr from CampaignManager cm,company_info ci where cm.manager_id=ci.manager_id and ci.manager_id != 0 and ci.company_id=$cid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cemail) = $sth->fetchrow_array())
{
	$email_str = $email_str . $cemail . ",";
}
$sth->finish();
$_ = $email_str;
chop;
$email_str = $_;
my $internal=1;
if ($uid eq "")
{
	srand(rand time());
	my @c=split(/ */, "bcdfghjklmnprstvwxyz");
	my @v=split(/ */, "aeiou");
	$uid= $c[int(rand(20))];
	$uid = $uid . $v[int(rand(5))];
	$uid = $uid . $c[int(rand(20))];
	$uid = $uid . $v[int(rand(5))];
	$uid = $uid . $c[int(rand(20))];
	$uid = $uid . int(rand(999999));
	$sql = "delete from approval_list where advertiser_id=$newaid and date_added < date_sub(curdate(),interval 7 day)";
	my $rows=$dbhu->do($sql);
	$sql = "insert into approval_list(advertiser_id,uid,date_added) values($newaid,'$uid',now())";
	my $rows=$dbhu->do($sql);
}
&util_mail::mail_approvaltest($dbhu,$email_str,$user_id,$newaid,$aname,$uid,$internal,$cstatus,@textads);

print "Location: advertiser_disp2.cgi?pmode=U&puserid=$newaid\n\n";

# exit function

$util->clean_up();
exit(0);
