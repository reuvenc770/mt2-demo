#!/usr/bin/perl
#===============================================================================
# Purpose: Suppression List Manager
# File   : supplist_mgr.cgi
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 05/01/07  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $aid;
my $aname;
my $list_id;
my $list_name;
my $last_updated;
my $file_date;
my $daycnt;
my $reccnt;
my $cpasscard;
my $passcard;
my $sortby= $query->param('sortby');
if ($sortby eq "")
{
	$sortby="last_updated";
}
my $f= $query->param('f');
if ($f eq "")
{
	$f="ALL";
}

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
    print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<title>Suppression List Manager</title>
<style type="text/css">

* { margin: 0; }

p { margin-top: 1em; }

a:active {  text-decoration: none; color: #3333FF}
a:link {  text-decoration: none; color: #3333FF}
a:visited {  text-decoration: none; color: #3333FF}
a:hover {  text-decoration: underline; color: #666699}

.txtS { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 9px }
.txt { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 11px }
.txtL { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 13px }

.txtSB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 9px; font-weight: bold }
.txtB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 11px; font-weight: bold }
.txtLB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 13px; font-weight: bold }

.errSB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 9px; font-weight: bold; color: #FF0000 }
.errB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 11px; font-weight: bold; color: #FF0000 }
.errLB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 13px; font-weight: bold; color: #FF0000 }

.title { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 14px }
.titleB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 14px; font-weight: bold }

.headline { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 16px }

.txtWhite { font-family: tahoma, trebuchet ms; font-size: 11px; color: #FFFFFF; font-weight: bold }
.img { border: none }

.dotted { border-color: #acacac; border-style: dotted; border-width: 1px; }

.white { background-color: #efefef; }
.grey { background-color: #dfdfdf; }

.list { list-style: none; margin: 0; padding: 0; }
.list ul, li { list-style: none; margin: 0; padding: 0; }

</style>

</head>

<body>
<table width="100%" cellspacing="0" cellpadding="10" id="titlebar">
  <tr>
    <td width="100%" bgcolor="#DFDFDF">
<font class="title">
<b>Suppression List Manager</b></font><br>
<font class="txtB"><a href="/newcgi-bin/mainmenu.cgi">go home &raquo;</a></font> 
    </td>
  </tr>
</table>
<br>
&nbsp;<div align="center">
			<td width="220" valign="top">
			<table border="0" width="210" align="center" cellspacing="0" cellpadding="8" id="menu">
			<tr><td class="txt" style="border-color: #acacac; border-style: dotted; border-width: 1px; ">
			<b>MANAGE SUPPRESSION LISTS</b><br><br>

			<ul class="list">
			<li><a href="/newcgi-bin/supplist_add.cgi">add a new suppression list</a></li>
			<li><a href="/newcgi-bin/find_info_id.cgi">get info for email ID</a></li>
			<li><a href="/newcgi-bin/find_info.cgi">get info for email address</a></li>
			<li><a href="/newcgi-bin/sub_disp_uns.cgi">add emails to global suppression</a></li>
			<li><a href="/newcgi-bin/domain_supplist_list.cgi">add domains to 
			global suppression</a></li>
			</ul>

			</td></tr>

			<tr><td class="txtS"><br></td></tr>
			<tr><td class="txt" style="border-color: #acacac; border-style: dotted; border-width: 1px; ">
			<b>ADVERTISERS/CLIENTS</b><br><br>
			<ul class="list">
			<li><a href="/adv_search.html">update select advertisers</a></li>
			<li><a href="/newcgi-bin/client_list.cgi">update clients</a></li>
			<li><a href="/client_schedule.html">clients schedule</a></li>
			<li><a href="/weekly.html">internal weekly schedule</a></li>
			<li><a href="/weekly_3rd.html">strongmail weekly schedule</a></li>
			</ul>
			
			</td></tr>
			
			<tr><td class="txtS"><br></td></tr>
			<tr><td class="txt" style="border-color: #acacac; border-style: dotted; border-width: 1px; ">
			<b><a href="http://209.120.227.3:83/reports/index.cgi">REPORTS</a></b><br><br>
			<ul class="list">
			<li></li><a href="http://209.120.227.3:83/reports/strongmail/new_strongmail_report.cgi">strongmail campaign report</a></li>
			<li><a href="http://209.120.227.3:83/reports/strongmail/report_by_server.cgi">strongmail server report</a></li>
			<li><a href="http://209.120.227.3:83/reports/rep_reporting.cgi?rt=campaign">internal campaign report</a></li>
			<li><a href="http://209.120.227.3:83/reports/strongmail/newsletter_report.cgi">newsletter report</a></li>
			<li><a href="http://209.120.227.3:83/reports/tot_rev_report.cgi">overall revenue report</a></li>
			</ul>
			
			</td></tr>
			
			<tr><td class="txtS"><br></td></tr>
			<tr><td class="txt" style="border-color: #acacac; border-style: dotted; border-width: 1px; ">
			<b>OTHER</b><br><br>
			<ul class="list">
			<li><a href="http://mediactivate.com/intranet/">Log into Mediactivate</a></li>
			</ul>
			
			</td></tr>
			</table>
			</td>
			<td align="center" valign="top" class="txt"><font class="title"><a href="/newcgi-bin/supplist_add.cgi">Add a new suppression list</a> | 
			<a href="/newcgi-bin/sub_disp_uns.cgi">Add emails to global suppression</a> | 
			<a href="/newcgi-bin/domain_supplist_list.cgi">Add domains to global suppression</a></font>
			
			<p class="title" style="color: red; "><b>Advertisers missing a suppression file:</b></p>
			<ul class="list">
end_of_html
my $sql="select distinct ai.advertiser_id,advertiser_name from advertiser_info ai, campaign c, category_info c1 where ai.status='A' and ai.vendor_supp_list_id in (0,1) and c.advertiser_id=ai.advertiser_id and c.scheduled_date >= curdate() and c.deleted_date is null and ai.category_id = c1.category_id and c1.category_name != 'UK' order by advertiser_name";
my $sth=$dbhq->prepare($sql);
$sth->execute();
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<li><a href=\"/newcgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$aid\">$aname</a></li>\n";
}
$sth->finish();
print<<"end_of_html";
			</ul>
			<br><br>
			
			<p>
			<form method="post" action="/newcgi-bin/supplist_mgr.cgi">
			<input type=hidden name=sortby value="$sortby">
									<b>View</b><font class="txt"><b>: </b><select name="f">
end_of_html
if ($f eq "ALL")
{
	print "<option selected value=\"ALL\">ALL</option>\n";
	print "<option value=\"OUTOFDATE\">out of date</option>\n";
	print "<option value=\"CURRENT\">current/up to date</option>\n";
}
elsif ($f eq "OUTOFDATE")
{
	print "<option value=\"ALL\">ALL</option>\n";
	print "<option selected value=\"OUTOFDATE\">out of date</option>\n";
	print "<option value=\"CURRENT\">current/up to date</option>\n";
}
elsif ($f eq "CURRENT")
{
	print "<option value=\"ALL\">ALL</option>\n";
	print "<option value=\"OUTOFDATE\">out of date</option>\n";
	print "<option selected value=\"CURRENT\">current/up to date</option>\n";
}
print<<"end_of_html";
									</select> <input type="submit" value="filter" class="txt"></font>
									<br>
&nbsp;</form></p>
	<table class="txt" cellSpacing="0" cellPadding="0" width="100%" border="0">
	
	<tr bgColor="#509c10" class="txtB">
		<td vAlign="top" width="2%" align="left"><font face="Arial">
		<img height="7" src="/mail-images/blue_tl.gif" width="7" border="0"></font></td>
		<td align="left" width="20%">
		<a href="/newcgi-bin/supplist_mgr.cgi?f=$f&sortby=list_name"><font color="white">Suppression List Name</font></a></td>
		<td width="30%"><font color="white">Advertisers</font></td>
		<td align="center"><font color="white">Passcard</font></td>
		<td align="center"><font color="white">Direct<br>Suppression URL</font></td>
		<td align="left" width="15%">
		<font color="white">Next Date Scheduled</font></td>
		<td align="left" width="15%">
		<a href="/newcgi-bin/supplist_mgr.cgi?f=$f&sortby=last_updated"><font color="white">Last Updated</font></a><a href="sort_updated">
		</a> 
		</td>
		<td align="center">
		<font color="white">Filedate</font>
		</td>
		<td align="center"><font color="white">Recs<br>Added</font></td>
		<td width="7%">
		<font color="white" width="10%">Functions </font></td>
		<td width="2%" vAlign="top" align="right"><font face="Arial">
		<img height="7" src="/mail-images/blue_tr.gif" width="7" border="0"></font></td>
	</tr>
end_of_html
$sql="select list_id,list_name,date(last_updated),datediff(curdate(),last_updated),filedate,records_added from vendor_supp_list_info where status='A' and list_id != 1752 and list_id > 1";
if ($f eq "OUTOFDATE") 
{
	$sql=$sql." and ((filedate is not null and filedate < date_sub(curdate(),interval 7 day)) or (filedate is null and last_updated < date_sub(curdate(),interval 7 day)))";
}
elsif ($f eq "CURRENT")
{
	$sql=$sql." and ((filedate is not null and filedate >= date_sub(curdate(),interval 10 day)) or (filedate is null and last_updated >= date_sub(curdate(),interval 10 day)))";
}
$sql=$sql . " order by $sortby";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $i=0;
my $lstr;
my $recs_added;
while (($list_id,$list_name,$last_updated,$daycnt,$file_date,$recs_added) = $sth->fetchrow_array())
{
	if ($f eq "OUTOFDATE")
	{
		$sql="select count(*) from campaign c,advertiser_info ai where ai.vendor_supp_list_id=? and c.advertiser_id=ai.advertiser_id and c.scheduled_date >= curdate() and c.deleted_date is null";
	}
	else
	{
		$sql="select count(*) from advertiser_info where status='A' and vendor_supp_list_id=?";
	}
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($list_id);
	($reccnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if (($reccnt == 0) and ($f eq "OUTOFDATE"))
	{
		$sql="select had.advertiser_id, sum(h.amount) as Revenue from advertiser_info had JOIN HitpathApiData h on had.sid=h.sid JOIN Affiliate a ON h.hitpath_id = a.affiliateCode JOIN AffiliateGroupList agl ON a.affiliateCode = agl.affiliateCode and h.ip not in ('66.9.60.138', '66.9.60.130') and effectiveDate >= date_sub(curdate(),interval 30 day) and agl.affiliateGroupID = 4 and had.vendor_supp_list_id=?  group by had.advertiser_id having Revenue>100 limit 1";
		my $sth1=$dbhq->prepare($sql);
		$sth1->execute($list_id);
		my $tid;
		my $revenue;
		if (($tid,$revenue)=$sth1->fetchrow_array())
		{
			$reccnt=1;
		}
		$sth1->finish();
	}
	if ($reccnt > 0)
	{
		if (($i % 2) == 0)
    	{
 			print "<tr class=\"grey\">";
		}
		else
		{
 			print "<tr>";
		}
		if ($file_date ne "")
		{
            $sql = "select datediff(curdate(),'$file_date')";
            $sth1 = $dbhu->prepare($sql) ;
            $sth1->execute();
            ($daycnt) = $sth1->fetchrow_array();
            $sth1->finish();
		}
		if ($file_date eq "")
		{
			$file_date=$last_updated;
		}
		if ($daycnt > 9)
		{
			$lstr="<font color=red>$file_date (out of date)</font>";
		}
		elsif (($daycnt >= 7) && ($daycnt <= 9))
		{
			$lstr="<font color=darkgoldenrod>$file_date (update soon)</font>";
		}
		else
		{
			$lstr="$file_date";
		}		
		$i++;
print<<"end_of_html";
		<td><b>$i</b></td>
		<td align="left">
		<a href="/cgi-bin/supplist_addnames.cgi?tid=$list_id">$list_name</font></a></a></td>
		<td class="txt">
		<ul class="list">
end_of_html
#		$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and vendor_supp_list_id=? order by advertiser_name";
		$sql="select advertiser_id,advertiser_name,ci.passcard,advertiser_info.passcard,advertiser_info.direct_suppression_url,date_format(date_sub(curdate(),interval 1 day),'%d') from advertiser_info, company_info ci where vendor_supp_list_id=? and advertiser_info.status='A' and advertiser_info.company_id=ci.company_id order by advertiser_name";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($list_id);
		my $aid_str="";
		my $passcard_str="";
		my $direct_url="";
		my $durl;
		my $cday;
    	my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    	$month+=1; $year+=1900;
    	$day=(length($day) < 2) ? "0$day" : $day;
    	$month=(length($month) < 2) ? "0$month" : $month;
		while (($aid,$aname,$cpasscard,$passcard,$durl,$cday)=$sth1->fetchrow_array())
		{
			if ($passcard ne "")
			{
				$cpasscard=$passcard;
			}
			$aid_str=$aid_str.$aid.",";
			print "<li><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$aid\">$aname</a></li>\n";
			if ($cpasscard ne "")
			{
				$passcard_str=$passcard_str."<li><a href=\"file://10.128.1.69/intern/#passcards/$cpasscard\">$cpasscard</a></li>";
			}
			else
			{
				$passcard_str=$passcard_str."<li></li>";
			}
			if ($durl ne "")
			{
				$durl=~s/{{MM}}/$month/g;
				$durl=~s/{{YYYY}}/$year/g;
				$durl=~s/{{YY}}/substr($year,2)/g;
				$durl=~s/{{DD-1}}/$cday/g;
				$durl=~s/{{DD}}/$day/g;
				$direct_url=$direct_url."<li><a href=\"$durl\">$durl</a></li>";
			}
			else
			{
				$direct_url=$direct_url."<li></li>";
			}
		}
		$sth1->finish();
		$_=$aid_str;
		chop;
		$aid_str=$_;
print<<"end_of_html";
		</ul>
		</td>
		<td class="txt">
		<ul class="list">
		$passcard_str</ul></td>
		<td class="txt">
		<ul class="list">
		$direct_url</ul></td>
end_of_html
#
#		Get the next scheduled date for all the advertisers
#
		my $next_date;
		$sql="select min(scheduled_date) from campaign force index(scheduled_date) where deleted_date is null and scheduled_date >= curdate() and advertiser_id in ($aid_str)"; 
		$sth1=$dbhq->prepare($sql);
		$sth1->execute();
		($next_date)=$sth1->fetchrow_array();
		$sth1->finish();
#		if ($next_date eq "")
#		{
#			$sql="select max(scheduled_date) from campaign where deleted_date is null and scheduled_date between date_sub(curdate(),interval 30 day) and  curdate() and advertiser_id in ($aid_str) "; 
#			$sth1=$dbhq->prepare($sql);
#			$sth1->execute();
#			($next_date)=$sth1->fetchrow_array();
#			$sth1->finish();
#			print "<td align=left width=\"16%\"><font color=\"green\"><i>$next_date</i></font>\n";
#		}
#		else
#		{
#			print "<td align=\"left\" width=\"16%\">$next_date</td>\n";
#		}
		print "<td align=\"left\" width=\"16%\">$next_date</td>\n";
print<<"end_of_html";
		<td width="13%">
		$last_updated</td>
		<td>$lstr</td>
		<td>$recs_added</td>
		<td align="left" width="7%">
		<ul class="list">
		<li><a href="/cgi-bin/supplist_addnames.cgi?tid=$list_id">update</a></li>
		<li><a href="/newcgi-bin/supplist_rename.cgi?vid=$list_id&f=$f&sortby=$sortby">rename</a></li>
		<li><a href="/newcgi-bin/supplist_delete.cgi?vid=$list_id&f=$f&sortby=$sortby">delete</a></li>
		</ul>
		</td>
		<td>&nbsp;</td>
	</tr>
end_of_html
	}
}
$sth->finish();
print<<"end_of_html";
	<tr>
	<td colspan="7">
	</td></tr>
	</font>
</table>

								</td>
		</tr>
	</table>
</div>
<p>
<div align="right" class="txt">copyright &copy; 2007 by Spire Vision, LLC</div>

</body>

</html>
end_of_html
