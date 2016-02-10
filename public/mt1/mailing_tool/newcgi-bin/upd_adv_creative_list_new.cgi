#!/usr/bin/perl

# *****************************************************************************************
# upd_adv_creative_list.cgi
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
my $open_cnt;
my $click_cnt;
my $incoming_cid;
my ($c1,$c2,$c3,$c4,$c5,$s1,$s2,$s3,$s4,$s5,$f1,$f2,$f3,$f4,$f5);
my ($c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15);
my ($s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$s15);
my ($s16,$s17,$s18,$s19,$s20,$s21,$s22,$s23,$s24,$s25,$s26,$s27,$s28,$s29,$s30);
my ($f6,$f7,$f8,$f9,$f10);
my ($f11,$f12,$f13,$f14,$f15,$f16,$f17,$f18,$f19,$f20);
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $catid1;
my $catid2;
my $advertiser_id1;
my $advertiser_id2;
my $sid;
my $aflag;
my $internal_aflag;
my $oflag;
my $copywriter;
my $fid;

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

my $aid = $query->param('aid');
my $did = $query->param('did');
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
$sql="select creative_id,creative_name,approved_flag,original_flag,internal_approved_flag,copywriter from creative where advertiser_id=$aid and ((inactive_date = '0000-00-00') or (inactive_date > curdate()) or (inactive_date is null)) and status='A' and trigger_flag='N' order by creative_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($cid,$aname,$aflag,$oflag,$internal_aflag,$copywriter) = $sth->fetchrow_array())
{
#	$sql = "select sum(el.open_cnt),(sum(el.click_cnt)/sum(el.open_cnt)*100),sum(el.click_cnt) from email_log el,campaign c where el.creative_id=$cid and el.open_cnt > 0 and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$aid";
	my $sent_cnt;
	$sql="select open_cnt,((click_cnt/open_cnt)*100),click_cnt,sent_cnt from creative_stat where creative_id=?";
	$sth1 = $dbhu->prepare($sql) ;
	$sth1->execute($cid);
	if (($open_cnt,$cclick,$click_cnt,$sent_cnt) = $sth1->fetchrow_array())
	{
	}
	else
	{
		$open_cnt=0;
		$cclick="";
		$click_cnt=0;
		$sent_cnt=0;
	}
	$sth1->finish();

	my $bounce_cnt;
#	$sql="select sum(block)+sum(hard)+sum(soft)+sum(tech)+sum(unk) from strmail_failure_summary where crID=? and date>=date_sub(curdate(),interval 60 day)";
#	$sth1 = $dbhq->prepare($sql) ;
#	$sth1->execute($cid);
#	($bounce_cnt) = $sth1->fetchrow_array();
#	$sth1->finish();
	$bounce_cnt=0;
	$sent_cnt=$sent_cnt-$bounce_cnt;

	if ($sent_cnt > 0)
	{
		my $temp=($open_cnt/$sent_cnt*100);
		$copen=sprintf("%5.2f",$temp);
		$temp=($click_cnt/$sent_cnt)*100000;
		$cindex=sprintf("%5.2f",$temp);
	}
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
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . "- AA";
    }
    else
    {
        $temp_str = $temp_str . "- NA!";
    }
    if ($internal_aflag eq "Y")
    {
        $temp_str = $temp_str . "- IA)";
    }
    else
    {
        $temp_str = $temp_str . ")";
    }
	$temp_str = $temp_str . " " . $aname."(".$cid.")"; 
	$temp_str=~s/"/\\"/g;
	print "doit2($cid,\"$temp_str\");\n";
}
$sql="select subject_id,advertiser_subject,approved_flag,original_flag,internal_approved_flag,copywriter from advertiser_subject where advertiser_id=$aid and status='A' order by advertiser_subject";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($sid,$aname,$aflag,$oflag,$internal_aflag,$copywriter) = $sth->fetchrow_array())
{
##	$sql = "select (sum(el.open_cnt)/sum(el.sent_cnt)*100) from email_log el, campaign c where el.subject_id=$sid and el.open_cnt > 0 and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$aid";
	$sql="select ((open_cnt/sent_cnt)*100) from subject_stat where subject_id=?";
	$sth1 = $dbhu->prepare($sql) ;
	$sth1->execute($sid);
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
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . "- AA";
    }
    else
    {
        $temp_str = $temp_str . "- NA!";
    }
    if ($internal_aflag eq "Y")
    {
        $temp_str = $temp_str . "- IA)";
    }
    else
    {
        $temp_str = $temp_str . ")";
    }
	$temp_str = $temp_str . " " . $aname."(".$sid.")"; 
	$temp_str =~ s/"/\\"/g;
	print "doit($sid,\"$temp_str\");\n";
}
$sth->finish();
$sql="select from_id,advertiser_from,approved_flag,original_flag,internal_approved_flag,copywriter from advertiser_from where advertiser_id=$aid order by advertiser_from";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($fid,$aname,$aflag,$oflag,$internal_aflag,$copywriter) = $sth->fetchrow_array())
{
##	$sql = "select (sum(el.open_cnt)/sum(el.sent_cnt)*100) from email_log el,campaign c where el.from_id=$fid and el.open_cnt > 0 and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$aid";
	$sql="select ((open_cnt/sent_cnt)*100) from from_stat where from_id=?";
	$sth1 = $dbhu->prepare($sql) ;
	$sth1->execute($fid);
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
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . "- AA";
    }
    else
    {
        $temp_str = $temp_str . "- NA!";
    }
    if ($internal_aflag eq "Y")
    {
        $temp_str = $temp_str . "- IA)";
    }
    else
    {
        $temp_str = $temp_str . ")";
    }
	$temp_str = $temp_str . " " . $aname."(".$fid.")"; 
	$temp_str =~ s/"/\\"/g;
	print "doit1($fid,\"$temp_str\");\n";
}
$sth->finish();
#
#	If (tid=1) which means editing campaign then update creative, subject, and
#	from fields
#
	$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,subject16,subject17,subject18,subject19,subject20,subject21,subject22,subject23,subject24,subject25,subject26,subject27,subject28,subject29,subject30,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,from11,from12,from13,from14,from15,from16,from17,from18,from19,from20 from advertiser_setup where advertiser_id=$aid and class_id=$did";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$s15,$s16,$s17,$s18,$s19,$s20,$s21,$s22,$s23,$s24,$s25,$s26,$s27,$s28,$s29,$s30,$f1,$f2,$f3,$f4,$f5,$f6,$f7,$f8,$f9,$f10,$f11,$f12,$f13,$f14,$f15,$f16,$f17,$f18,$f19,$f20) = $sth->fetchrow_array();
	$sth->finish();
#
#	Get advertiser for trigger creative
#
	$catid1 = -1;
	$catid2 = -1;
	$advertiser_id1 = -1;
	$advertiser_id2 = -1;
	print "parent.main.set_fields('$c1','$c2','$c3','$c4','$c5','$c6','$c7','$c8','$c9','$c10','$c11','$c12','$c13','$c14','$c15','$s1','$s2','$s3','$s4','$s5','$s6','$s7','$s8','$s9','$s10','$s11','$s12','$s13','$s14','$s15','$s16','$s17','$s18','$s19','$s20','$s21','$s22','$s23','$s24','$s25','$s26','$s27','$s28','$s29','$s30','$f1','$f2','$f3','$f4','$f5','$f6','$f7','$f8','$f9','$f10','$f11','$f12','$f13','$f14','$f15','$f16','$f17','$f18','$f19','$f20');\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
