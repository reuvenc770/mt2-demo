#!/usr/bin/perl

# *****************************************************************************************
# upd_subject_list.cgi
#
# this page updates select list based on advertiser_id
#
# History
# Jim Sobeck, 01/19/05, Creation
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
my $aid;
my $cid;
my $incoming_cid;
my ($c1,$c2,$c3,$c4,$c5,$s1,$s2,$s3,$s4,$s5,$f1,$f2,$f3,$f4,$f5);
my ($c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15);
my ($s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$s15);
my ($f6,$f7,$f8,$f9,$f10);
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $trigger_creative;
my $sid;
my $aflag;
my $oflag;
my $fid;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

my $aid = $query->param('aid');
my $tid = $query->param('tid');
my $temp_str;
my $copen;
my $cclick;
my $cindex;
my $sth1;
my $incoming_cid = $query->param('cid');
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
	parent.main.addSubjectOption(value,text);
}
function doit1(value,text)
{
	parent.main.addFromOption(value,text);
}
function doit2(value,text)
{
	parent.main.addCreativeOption(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
doit(0,'Select One');
doit1(0,'Select One');
doit2(0,'Select One');
end_of_html
$sql="select creative_id,creative_name,approved_flag,original_flag from creative where advertiser_id=$aid and ((inactive_date = '0000-00-00') or (inactive_date > curdate())) and status='A' order by creative_name";
$sth = $dbh->prepare($sql) ;
$sth->execute();
while (($cid,$aname,$aflag,$oflag) = $sth->fetchrow_array())
{
	$sql = "select (sum(open_cnt)/sum(sent_cnt)*100),(sum(click_cnt)/sum(open_cnt)*100),((sum(click_cnt)/sum(sent_cnt))*100000) from creative_log where creative_id=$cid";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($copen,$cclick,$cindex) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($copen eq "")
	{
		$copen = "0.00";
	}
	if ($cclick eq "")
	{
		$cclick = "0.00";
	}
	if ($cindex eq "")
	{
		$cindex = "0";
	}
	$temp_str = "(" . $copen . "% Open - " . $cclick . "% Click - " . $cindex . " Index - ";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	$temp_str = $temp_str . " " . $aname; 
	print "doit2($cid,\"$temp_str\");\n";
}
$sql="select subject_id,advertiser_subject,approved_flag,original_flag from advertiser_subject where advertiser_id=$aid and status='A' order by advertiser_subject";
$sth = $dbh->prepare($sql) ;
$sth->execute();
while (($sid,$aname,$aflag,$oflag) = $sth->fetchrow_array())
{
	$sql = "select (sum(open_cnt)/sum(sent_cnt)*100) from subject_log where subject_id=$sid";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($copen) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($copen eq "")
	{
		$copen = "0.00";
	}
	$temp_str = "(" . $copen . "% Open - ";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	$temp_str = $temp_str . " " . $aname; 
	$temp_str =~ s/"/\\"/g;
	print "doit($sid,\"$temp_str\");\n";
}
$sth->finish();
$sql="select from_id,advertiser_from,approved_flag,original_flag from advertiser_from where advertiser_id=$aid order by advertiser_from";
$sth = $dbh->prepare($sql) ;
$sth->execute();
while (($fid,$aname,$aflag,$oflag) = $sth->fetchrow_array())
{
	$sql = "select (sum(open_cnt)/sum(sent_cnt)*100) from from_log where from_id=$fid";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($copen) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($copen eq "")
	{
		$copen = "0.00";
	}
	$temp_str = "(" . $copen . "% Open - ";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	$temp_str = $temp_str . " " . $aname; 
	print "doit1($fid,\"$temp_str\");\n";
}
$sth->finish();
#
#	If (tid=1) which means editing campaign then update creative, subject, and
#	from fields
#
if ($tid == 1)
{
	$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10 trigger_creative from campaign where campaign_id=$incoming_cid";
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$s15,$f1,$f2,$f3,$f4,$f5,$f6,$f7,$f8,$f9,$f10,$trigger_creative) = $sth->fetchrow_array();
	$sth->finish();
#
#	Get advertiser for trigger creative
#
	if ($trigger_creative > 0)
	{
		print "parent.main.addTriggerOption(0,'Select One');\n";
	$sql="select creative_id,creative_name from creative where advertiser_id=(select advertiser_id from creative where creative_id=$trigger_creative) order by creative_name";
$sth = $dbh->prepare($sql) ;
$sth->execute();
while (($cid,$aname) = $sth->fetchrow_array())
{
    print "parent.main.addTriggerOption($cid,'$aname');\n";
}
$sth->finish();
	}
	print "parent.main.set_fields('$c1','$c2','$c3','$c4','$c5','$c6','$c7','$c8','$c9','$c10','$c11','$c12','$c13','$c14','$c15','$s1','$s2','$s3','$s4','$s5','$s6','$s7','$s8','$s9','$s10','$s11','$s12','$s13','$s14','$s15','$f1','$f2','$f3','$f4','$f5','$f6','$f7','$f8','$f9','$f10','$trigger_creative');\n";
}
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
