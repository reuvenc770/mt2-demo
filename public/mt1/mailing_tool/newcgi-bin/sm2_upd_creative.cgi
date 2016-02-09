#!/usr/bin/perl

# *****************************************************************************************
# sm2_upd_creative.cgi
#
# this page updates select creatives, subjects,and froms based on advertiser_id
#
# History
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
my $aname;
my $copywriter;
my $crid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $aid = $query->param('aid');
my $usa_id = $query->param('usa_id');
my $all = $query->param('all');
if ($all eq "")
{
	$all=0;
}
my $sord= $query->param('sord');
if ($sord eq "")
{
	$sord="Alphabetical";
}
my $cord="creative_name";
my $subord="advertiser_subject";
my $ford="advertiser_from";
if ($sord eq "ID")
{
	$cord="creative_id desc";
	$subord="subject_id desc";
	$ford="from_id desc";
}
my $t1= $query->param('t1');
my $t2= $query->param('t2');
my $t3= $query->param('t3');
if ($t2 eq "")
{
	$t2=0;
}
if ($t3 eq "")
{
	$t3=0;
}
my $uid = $query->param('uid');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
function doit(value,text)
{
	parent.main.addCreative(value,text);
}
function doit1(value,text)
{
	parent.main.addSubject(value,text);
}
function doit2(value,text)
{
	parent.main.addFrom(value,text);
}
function doit3(value,text)
{
	parent.main.addUSA(value,text);
}
function set_fields(crid,csubject,cfrom)
{
	parent.main.set_creative_fields(crid,csubject,cfrom);
}
function set_cr_fields(crid)
{
	parent.main.set_creative_field(crid);
}
function set_subject_fields(crid)
{
	parent.main.set_subject_field(crid);
}
function set_from_fields(crid)
{
	parent.main.set_from_field(crid);
}
function set_usa_fields(crid)
{
	parent.main.set_usa_field(crid);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql = "select creative_id, creative_name,original_flag, trigger_flag, approved_flag,mediactivate_flag,copywriter from creative where status='A' and advertiser_id=$aid and trigger_flag='N' ";
if ($t2 > 0)
{
	$sql=$sql . " and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y'))";
}
if ($t1 == 3)
{
	$sql=$sql." and internal_approved_flag='Y' ";
}
$sql=$sql." order by $cord";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
my $oflag;
my $tflag;
my $aflag;
my $mflag;
my $temp_str;
if ($all == 0)
{
	print "doit(0,\"ROTATE ALL\");\n";
}
while (($cid,$cname,$oflag,$tflag,$aflag,$mflag,$copywriter) = $sth->fetchrow_array())
{
	$cname=~s/"/\\"/g;
	$temp_str = $cid . " - " . $cname . " (";
	if ($tflag eq "Y")
	{
		$temp_str = $temp_str . "TRIGGER - ";
	}
	if ($oflag eq "Y")
	{
		$temp_str = $temp_str . "O ";
	}
	else
	{
		$temp_str = $temp_str . "A ";
	}
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
	if ($mflag eq "Y")
	{
		$temp_str = $temp_str . " - M ";
	}
	if ($aflag eq "Y")
	{
		$temp_str = $temp_str . ")";
	}
	else
	{
		$temp_str = $temp_str . "- NA!)";
	}
	print "doit($cid,\"$temp_str\");\n";
}
$sth->finish();

$sql = "select subject_id,advertiser_subject,approved_flag,original_flag,copywriter from advertiser_subject where advertiser_id=$aid and status='A' ";
if ($t2 > 0)
{
	$sql=$sql . " and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y'))";
}
if ($t1 == 3)
{
	$sql=$sql." and internal_approved_flag='Y' ";
}
$sql=$sql." order by $subord";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $csubject;
my $sid;
my $aflag;
my $oflag;
if ($all == 0)
{
	print "doit1(999999999,\"ROTATE ALL\");\n";
}
while (($sid,$csubject,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
	$csubject=~s/"/\\"/g;
    $temp_str = $sid . " - " . $csubject. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	print "doit1($sid,\"$temp_str\");\n";
}
$sth->finish();
$sql = "select from_id,advertiser_from,approved_flag,original_flag,copywriter from advertiser_from where advertiser_id=$aid and status='A' ";
if ($t2 > 0)
{
	$sql=$sql . " and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag = 'Y'))";
}
if ($t1 == 3)
{
	$sql=$sql." and internal_approved_flag='Y' ";
}
$sql=$sql." order by $ford";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cfrom;
my $sid;
my $aflag;
my $oflag;
if ($all == 0)
{
	print "doit2(999999999,\"ROTATE ALL\");\n";
}
while (($sid,$cfrom,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
	$cfrom=~s/"/\\"/g;
    $temp_str = $sid . " - " . $cfrom. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	print "doit2($sid,\"$temp_str\");\n";
}
$sth->finish();
if ($t1 == 1)
{
	$sql="select creative_id from UniqueCreative where unq_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($uid);
	while (($crid)=$sth1->fetchrow_array())
	{
		print "set_cr_fields(\"$crid\");\n";
	}
	$sth1->finish();
	$sql="select subject_id from UniqueSubject where unq_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($uid);
	while (($crid)=$sth1->fetchrow_array())
	{
		print "set_subject_fields(\"$crid\");\n";
	}
	$sth1->finish();
	$sql="select from_id from UniqueFrom where unq_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($uid);
	while (($crid)=$sth1->fetchrow_array())
	{
		print "set_from_fields(\"$crid\");\n";
	}
	$sth1->finish();
}
elsif ($t1 == 2)
{
	$sql="select creative_id from UniqueAdvertiserCreative where usa_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($usa_id);
	while (($crid)=$sth1->fetchrow_array())
	{
		print "set_cr_fields(\"$crid\");\n";
	}
	$sth1->finish();
	$sql="select subject_id from UniqueAdvertiserSubject where usa_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($usa_id);
	while (($crid)=$sth1->fetchrow_array())
	{
		print "set_subject_fields(\"$crid\");\n";
	}
	$sth1->finish();
	$sql="select from_id from UniqueAdvertiserFrom where usa_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($usa_id);
	while (($crid)=$sth1->fetchrow_array())
	{
		print "set_from_fields(\"$crid\");\n";
	}
	$sth1->finish();
}
if ($t3 == 1)
{
	my $usaid;
	my $uname;
	print "doit3(0,\"Select One\");\n";
	$sql="select usa.usa_id,usa.name from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' and ai.advertiser_id=$aid order by usa.name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($usaid,$uname)=$sth1->fetchrow_array())
	{
		print "doit3($usaid,\"$uname\");\n";
	}
	$sth1->finish();
}
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
