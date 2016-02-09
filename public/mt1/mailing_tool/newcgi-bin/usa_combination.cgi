#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $uname;
my $aname;
my $rowCnt;
my $aid;
my $oldcid;
my $olds;
my $oldf;
my $cid;
my $cname;
my $sid;
my $sname;
my $fid;
my $fname;
my ($oflag,$tflag,$aflag,$mflag,$copywriter);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
my $usaid=$query->param('usaid');

$sql="select usa.name,usa.rowCnt,ai.advertiser_name,ai.advertiser_id from UniqueScheduleAdvertiser usa, advertiser_info ai where usa.advertiser_id=ai.advertiser_id and usa.usa_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($usaid);
($uname,$rowCnt,$aname,$aid)=$sth->fetchrow_array();
$sth->finish();

print "Content-type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>USA Combination Edit</title>
<style>
body{
	background-color:#99d1f4;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#000;}
	
a{
	color:#000;}
	
#topbuttons{
	padding-left: 450px;
	padding-top:25px;
	padding-bottom:10px;}

#topinfo{
	padding-left:375px;}
</style>
</head>

<body>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="517" style="color:#397c00;" align="right"><a href="#" style="color:#397c00; text-decoration:none;"><b>Logout</a>&nbsp;&nbsp;&nbsp;<a href="#" style="color:#397c00; text-decoration:none;">Customer Assistance</a></b></td>
  </tr>
</table>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <div id="topbuttons">
    <a href="mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34"></a>
    </div>
    <div id="topinfo">
    <table width="300" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="62" style="padding-bottom:5px;">Name:</td>
        <td width="238" style="padding-bottom:5px;"><b>$uname</b></td>
      </tr>
      <tr>
        <td style="padding-bottom:20px;">Advertiser:</td>
        <td style="padding-bottom:20px;"><b>$aname</b></td>
      </tr>
    </table>
    </div>
	<form method=post action=usa_combination_upd.cgi>
	<input type=hidden name=usaid value=$usaid>
    <table width="1300" border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr align=center style="font-size:16px; font-weight:bold; color:#FFF;">
    <td bgcolor="#509c10" style="padding-bottom:5px;">Creative</td>
    <td bgcolor="#509c10" style="padding-bottom:5px;">Subject</td>
    <td bgcolor="#509c10" style="padding-bottom:5px;">From</td>
    <td width="237" bgcolor="#509c10" style="padding-bottom:5px;">&nbsp;</td>
	</tr>
end_of_html
my $i=1;
while ($i <= $rowCnt)
{
  	print "<tr align=center>";
	$sql="select creative_id from UniqueAdvertiserCreative where usa_id=? and rowID=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($usaid,$i);
	($oldcid)=$sth->fetchrow_array();
	$sth->finish();
	$sql="select creative_id, creative_name,original_flag, trigger_flag, approved_flag,mediactivate_flag,copywriter from creative where status='A' and advertiser_id=? and trigger_flag='N' and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y')) order by creative_name";
	$sth=$dbhu->prepare($sql);
	$sth->execute($aid);
    print "<td bgcolor=\"#ebfad1\" style=\"padding-bottom:5px; padding-top:5px;\"><select name=cid$i>";
	while (($cid,$cname,$oflag,$tflag,$aflag,$mflag,$copywriter)=$sth->fetchrow_array())
	{
	    my $temp_str = $cid . " - " . $cname . " (";
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
		if ($cid == $oldcid)
		{
			print "<option value=$cid selected>$temp_str</option>";
		}
		else
		{
			print "<option value=$cid >$temp_str</option>";
		}
	}
	print "</select></td>";
#
	$sql="select subject_id from UniqueAdvertiserSubject where usa_id=? and rowID=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($usaid,$i);
	($olds)=$sth->fetchrow_array();
	$sth->finish();
	$sql="select subject_id,advertiser_subject,approved_flag,original_flag,copywriter from advertiser_subject where advertiser_id=? and status='A' and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y')) order by advertiser_subject"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($aid);
    print "<td bgcolor=\"#ebfad1\" style=\"padding-bottom:5px; padding-top:5px;\"><select name=sid$i>";
	while (($sid,$sname,$aflag,$oflag,$copywriter)=$sth->fetchrow_array())
	{

	    my $temp_str = $sid . " - " . $sname. " (";
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
		if ($sid == $olds)
		{
			print "<option value=$sid selected>$temp_str</option>";
		}
		else
		{
			print "<option value=$sid >$temp_str</option>";
		}
	}
	print "</select></td>";
#
	$sql="select from_id from UniqueAdvertiserFrom where usa_id=? and rowID=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($usaid,$i);
	($oldf)=$sth->fetchrow_array();
	$sth->finish();
	$sql="select from_id,advertiser_from,approved_flag,original_flag,copywriter from advertiser_from where advertiser_id=? and status='A' and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y')) order by advertiser_from"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($aid);
    print "<td bgcolor=\"#ebfad1\" style=\"padding-bottom:5px; padding-top:5px;\"><select name=fid$i>";
	while (($fid,$fname,$aflag,$oflag,$copywriter)=$sth->fetchrow_array())
	{

	    my $temp_str = $fid . " - " . $fname. " (";
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
		if ($fid == $oldf)
		{
			print "<option value=$fid selected>$temp_str</option>";
		}
		else
		{
			print "<option value=$fid >$temp_str</option>";
		}
	}
	print "</select></td>";
	if ($i == $rowCnt)
	{
    	print "<td bgcolor=\"#ebfad1\" style=\"padding-bottom:5px; padding-top:5px;\"><a href=\"usa_combination_addrow.cgi?usaid=$usaid\"><img src=\"/images/but_newrow.gif\" border=0 width=121 height=25></a>";
		if ($i > 1)
		{
			print "&nbsp;&nbsp;<a href=usa_combination_delrow.cgi?usaid=$usaid>Delete Row</a>";
		}
		print "</td>";
	}
	print "</tr>";
	$i++;
}
print<<"end_of_html";
	<tr><td colspan=4 align=middle><input type=submit value="Update"></td></tr>
    </table>
	</form>
<div id="topbuttons">
    <a href="mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    </div>
    </td>
  </tr>
</table>
</body>
</html>
end_of_html
