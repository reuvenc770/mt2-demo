#!/usr/bin/perl

# ******************************************************************************
# unique_resume_save.cgi 
#
# this page updates information in the unique_campaign table
#
# History
# ******************************************************************************

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
my $sid;
my $rowID;
my $usaType;
my $i;
my $class;
my $errmsg;
my $rows;
my $camp_id;
my $images = $util->get_images_url;
my $pmesg="";
my @url_array;
my $cnt;
my $curl;
my $temp_cnt;
my $btype;
my $nl_id;

$cnt=0;
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
# Remove old url information
#
my $uid= $query->param('uid');
my @domainid = $query->param('domainid');
my @cdomainid = $query->param('cdomainid');
my $group_id = $query->param('group_id');
my $template_id = $query->param('template_id');
my $adv_id = $query->param('adv_id');
my $cusa= $query->param('cusa');
if ($cusa eq "")
{
    $cusa=0;
}
my @creative;
my @csubject;
my @cfrom;
my $mta_id = $query->param('mta_id');
my $exclude_domains = $query->param('exclude_domains');
my $pastedomain= $query->param('pastedomain');
my $cpastedomain= $query->param('cpastedomain');
my $mail_from = $query->param('mail_from');
my $jlogProfileID=$query->param('jlogProfileID');
if ($jlogProfileID eq "")
{
	$jlogProfileID=0;
}
my $ConvertSubject = $query->param('ConvertSubject');
if ($ConvertSubject eq "")
{
    $ConvertSubject="None";
}
my $ConvertFrom = $query->param('ConvertFrom');
if ($ConvertFrom eq "")
{
    $ConvertFrom="None";
}
my $savepaste=$pastedomain;
if ($pastedomain ne '')
{
	$pastedomain =~ s/[ \n\r\f\t]/\|/g ;    
	$pastedomain =~ s/\|{2,999}/\|/g ;           
	@domainid= split '\|', $pastedomain;
}
if ($cpastedomain ne '')
{
	$cpastedomain =~ s/[ \n\r\f\t]/\|/g ;    
	$cpastedomain =~ s/\|{2,999}/\|/g ;           
	@cdomainid= split '\|', $cpastedomain;
}
#
if ($cusa > 0)
{
	$sql="select usaType from UniqueScheduleAdvertiser where usa_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	($usaType)=$sth->fetchrow_array();
	$sth->finish();

	my $i;
	my $t1;
	$i=0;
	$sql="select subject_id from UniqueAdvertiserSubject where usa_id=? order by rowID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	while (($t1)=$sth->fetchrow_array())
	{
		$csubject[$i]=$t1;
		$i++;
	}
	$sth->finish();
	$i=0;
	$sql="select from_id from UniqueAdvertiserFrom where usa_id=? order by rowID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	while (($t1)=$sth->fetchrow_array())
	{
		$cfrom[$i]=$t1;
		$i++;
	}
	$sth->finish();
	$i=0;
	$sql="select creative_id from UniqueAdvertiserCreative where usa_id=? order by rowID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	while (($t1)=$sth->fetchrow_array())
	{
		$creative[$i]=$t1;
		$i++;
	}
	$sth->finish();
	if (($#creative < 0) or ($#cfrom < 0) or ($#csubject < 0))
	{
			print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<center>
<h3>Deploy not saved because Creative, Subject, or From Missing for USA<h3>
</center>
</body>
</html>
end_of_html
		exit();
	}
}
else
{
	@creative = $query->param('creative');
	@csubject= $query->param('csubject');
	@cfrom = $query->param('cfrom');
}
#
$sql="delete from UniqueCreative where unq_id=$uid";
$rows=$dbhu->do($sql);
$sql="delete from UniqueSubject where unq_id=$uid";
$rows=$dbhu->do($sql);
$sql="delete from UniqueFrom where unq_id=$uid";
$rows=$dbhu->do($sql);
$sql="delete from UniqueDomain where unq_id=$uid";
$rows=$dbhu->do($sql);
$sql="delete from UniqueContentDomain where unq_id=$uid";
$rows=$dbhu->do($sql);
$domainid[0]=~s/,//g;

my $cstatus;
my $dbid;
$sql="select dbID from campaign where id=? limit 1";
$sth=$dbhu->prepare($sql);
$sth->execute($uid);
($dbid)=$sth->fetchrow_array();
$sth->finish();

if ($dbid eq "")
{
	$cstatus="START";
}
else
{
	$cstatus="PENDING";
}
my $aname;
$sql="select advertiser_name from advertiser_info where advertiser_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($adv_id);
($aname)=$sth->fetchrow_array();
$sth->finish();
my $cname;
my $oldaid;
$sql="select campaign_name,advertiser_id from unique_campaign where unq_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($uid);
($cname,$oldaid)=$sth->fetchrow_array();
$sth->finish();
if ($oldaid == $adv_id)
{
	$aname=$cname;
}

$sql="update unique_campaign set status='$cstatus',mailing_domain='$domainid[0]',group_id=$group_id,mailing_template=$template_id,exclude_domain='$exclude_domains',pasted_domains='$savepaste',advertiser_id=$adv_id,creative_id=$creative[0],subject_id=$csubject[0],from_id=$cfrom[0],mta_id=$mta_id,ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',mail_from='$mail_from',campaign_name='$aname',jlogProfileID=$jlogProfileID  where unq_id=$uid";
$rows=$dbhu->do($sql);
logAction($user_id,$uid,"Resumed");

$sql="delete from UniqueDomain where unq_id=$uid";
$rows=$dbhu->do($sql);
my $i=0;
while ($i <= $#domainid)
{
	$domainid[$i]=~s/,//g;
	$sql="insert UniqueDomain(unq_id,mailing_domain) values($uid,'$domainid[$i]')";
	$rows=$dbhu->do($sql);
	$i++;
}
$sql="delete from UniqueContentDomain where unq_id=$uid";
$rows=$dbhu->do($sql);
my $i=0;
while ($i <= $#cdomainid)
{
	$cdomainid[$i]=~s/,//g;
	$sql="insert UniqueContentDomain(unq_id,domain_name) values($uid,'$cdomainid[$i]')";
	$rows=$dbhu->do($sql);
	$i++;
}

$sql="delete from UniqueExcludeClass where unq_id=$uid";
$rows=$dbhu->do($sql);

my @isps= $query->param('isps');
foreach my $isp (@isps)
{
	$sql="insert into UniqueExcludeClass(unq_id,class_id) values($uid,$isp)";
	$rows=$dbhu->do($sql);
}
$i=0;
while ($i <= $#creative)
{
    if ($usaType eq "Combination")
    {
        $rowID=$i+1;
    }
    else
    {
        $rowID=0;
    }
	$sql="insert into UniqueCreative(unq_id,creative_id,rowID) values($uid,$creative[$i],$rowID)";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#csubject)
{
    if ($usaType eq "Combination")
    {   
        $rowID=$i+1;
    }       
    else
    {   
        $rowID=0;
    }  
	$sql="insert into UniqueSubject(unq_id,subject_id,rowID) values($uid,$csubject[$i],$rowID)";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#cfrom)
{
    if ($usaType eq "Combination")
    {   
        $rowID=$i+1;
    }       
    else
    {   
        $rowID=0;
    }  
	$sql="insert into UniqueFrom(unq_id,from_id,rowID) values($uid,$cfrom[$i],$rowID)";
	$rows=$dbhu->do($sql);
	$i++;
}
$aname=~s/'/''/g;
$sql="select campaign_id from campaign where id=? and deleted_date is null";
$sth=$dbhu->prepare($sql);
$sth->execute($uid);
while (($camp_id)=$sth->fetchrow_array())
{
	$sql="update campaign,campaign_name='$aname' set advertiser_id=$adv_id where campaign_id=$camp_id";
	$rows=$dbhu->do($sql);
}
$sth->finish();
print "Location: /cgi-bin/unique_deploy_list.cgi\n\n";
#
sub logAction
{
    my ($user_id,$unq_id,$action)=@_;
    my $sql="insert into UniqueCampaignLog(unq_id,user_id,logDate,action) values($unq_id,$user_id,now(),'$action')";
    my $rows=$dbhu->do($sql);
}
