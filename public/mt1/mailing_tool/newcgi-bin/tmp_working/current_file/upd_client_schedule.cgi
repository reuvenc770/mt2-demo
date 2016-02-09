#!/usr/bin/perl

# *****************************************************************************************
# upd_client_schedule.cgi
#
# History
# Jim Sobeck, 06/29/05, creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sth2;
my $sql;
my $dbh;
my $cid;
my $company;
my $shour;
my $images = $util->get_images_url;
my ($slot_id,$stime,$profile_id,$brand_id,$cstatus); 
my $temp_profile_id;
my $third_id;
my $temp_tid;
my $mailer_name;
my $profile_name;
my $max_id;
my $max_emails;
my $aol_flag;
my $hotmail_flag;
my $yahoo_flag;
my $other_flag;
my $rows;
my @type_arr= (
    ["Campaigns","C"],
    ["3rd Party","3"],
    ["AOL","A"],
    ["Daily","D"] );
my $pid;
my $pname;
my $host_cnt;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

my $cid = $query->param('client_id');
if ($cid eq "0")
{
	print "Location: /blank.html\n\n";
	exit(0);
}
my $daily_cnt= $query->param('daily_cnt');
my $camp_cnt= $query->param('camp_cnt');
my $aol_cnt= $query->param('aol_cnt');
my $rotate_cnt= $query->param('rotating_cnt');
my $third_cnt= $query->param('third_cnt');
my $reccnt;
$sql = "select count(*) from network_schedule where client_id=$cid";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($reccnt) = $sth->fetchrow_array();
$sth->finish();
if ($reccnt > 0)
{
	$sql = "update network_schedule set campaign_cnt=$camp_cnt,aol_cnt=$aol_cnt,daily_cnt=$daily_cnt,rotating_cnt=$rotate_cnt,3rdparty_cnt=$third_cnt where client_id=$cid";
}
else
{
	$sql="insert into network_schedule(client_id,campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt) values($cid,$camp_cnt,$aol_cnt,$daily_cnt,$rotate_cnt,$third_cnt)";
}
$rows=$dbhu->do($sql);
#
$sql = "select company from user where user_id=$cid";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($company) = $sth->fetchrow_array();
$sth->finish();
#
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='C'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "delete from schedule_info where client_id=$cid and slot_type='C' and slot_id > $camp_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $camp_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'C')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
#
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='3'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "delete from schedule_info where client_id=$cid and slot_type='3' and slot_id > $third_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $third_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'3')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='A'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "delete from schedule_info where client_id=$cid and slot_type='A' and slot_id > $aol_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $aol_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'A')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='D'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "delete from schedule_info where client_id=$cid and slot_type='D' and slot_id > $daily_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $daily_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'D')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='R'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "delete from schedule_info where client_id=$cid and slot_type='R' and slot_id > $rotate_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $rotate_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'R')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Schedule Details</title>
</head>
<body>
<form method=post action="/cgi-bin/sav_client_schedule.cgi">
<input type=hidden name=cid value=$cid>
<!-- <table border="0" width="35%" id="table7">
	<tr>
		<td><b><font face="Verdana">$company: </font></b></td>
		<td>
		&nbsp;</td>
	</tr>
</table> -->
<table border="0" width="35%" id="table25">
	<tr>
		<td><b><font face="Verdana"><a href="/cgi-bin/listprofile_list.cgi?client_id=$cid" target="_blank">List Profiles</a></font></b></td>
		<td>
		&nbsp;</td>
	</tr>
</table>
<table border="0" width="35%" id="table26">
	<tr>
		<td>&nbsp;</td>
		<td>
		&nbsp;</td>
	</tr>
</table>
end_of_html
my $i = 0;
while ($i <= $#type_arr)
{
print<<"end_of_html";
<table border="0" width="35%" id="table4">
	<tr>
		<td><b><font face="Verdana">&nbsp;$type_arr[$i][0]:</font></b></td>
		<td width="205">
		<p align="right"><b><font face="Verdana">&nbsp;</font></b></td>
	</tr>
</table>
<table border="1" width="54%" id="table1">
end_of_html
if ($type_arr[$i][1] eq "3")
{
print<<"end_of_html";
	<tr><td width="9"><b><font face="Verdana" size="1">#</font></b></td>
		<td width="88"><b><font face="Verdana" size="1">Time:</font></b></td>
		<td width="239"><b><font face="Verdana" size="1">3rd Party/Profile:</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">Brand</font></b></td>
		<td width="180"><b><font face="Verdana" size="1"></font></b></td>
	</tr>
end_of_html
}
else
{
print<<"end_of_html";
	<tr><td width="9"><b><font face="Verdana" size="1">#</font></b></td>
		<td width="88"><b><font face="Verdana" size="1">Time:</font></b></td>
		<td width="239"><b><font face="Verdana" size="1">List Profile Name & Quantity:</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">Brand & # of Unique Managed Hosts:</font></b></td>
		<td width="180"><b><font face="Verdana" size="1"></font></b></td>
	</tr>
end_of_html
}
$sql = "select slot_id,hour(schedule_time),profile_id,brand_id,status,third_party_id from schedule_info where client_id=$cid and slot_type='$type_arr[$i][1]' order by slot_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($slot_id,$stime,$profile_id,$brand_id,$cstatus,$third_id) = $sth->fetchrow_array())
{
print<<"end_of_html";
	<tr>
		<td width="9" align="center"><b><font face="Verdana" size="1">$slot_id</font></b></td>
		<td width="88">
									<select name="tid_${type_arr[$i][1]}_${slot_id}">
end_of_html
my $j=1;
if ($stime > 12)
{
	$shour = $stime - 12;
}
else
{
	$shour = $stime;
}
while ($j <= 12)
{
	if ($j == $shour)
	{
		print "<option selected value=$j>$j</option>\n";
	}
	else
	{
		print "<option value=$j>$j</option>\n";
	}
	$j++;
}
print "</select><select name=am_pm_${type_arr[$i][1]}_${slot_id}>\n";
if ($stime >= 13)
{
	print "<option value=\"AM\">AM</option>\n";
	print "<option value=\"PM\" selected>PM</option>\n";
}
else
{
	print "<option value=\"AM\" selected>AM</option>\n";
	print "<option value=\"PM\">PM</option>\n";
}
print<<"end_of_html";
									</select></td>
		<td>
									<select name="pid_${type_arr[$i][1]}_${slot_id}" size="1">
end_of_html
if ($type_arr[$i][1] ne "3")
{
$sql="select profile_id,profile_name,max_emails,aol_flag,yahoo_flag,other_flag,hotmail_flag from list_profile where client_id=$cid and status='A' order by profile_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
my $reccnt_str="";
while (($pid,$pname,$max_emails,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag) = $sth1->fetchrow_array())
{
	$reccnt_str="";
	my $total_cnt = 0;
	if ($max_emails != -1)
	{
		$reccnt_str = "T - $max_emails";
	}
	else
	{
		if ($other_flag eq "Y")
		{
			$sql = "select sum(member_cnt)-sum(aol_cnt)-sum(hotmail_cnt)-sum(msn_cnt)-sum(foreign_cnt)-sum(yahoo_cnt) from list where list_id in (select list_id from list_profile_list where profile_id=$pid)";
			$sth2 = $dbhq->prepare($sql) ;
			$sth2->execute();
			($reccnt) = $sth2->fetchrow_array();
			$sth2->finish();
			if ($reccnt eq "")
			{
				$reccnt = 0;
			}
			$total_cnt = $total_cnt + $reccnt;
			$reccnt=commify($reccnt);
			$reccnt_str = $reccnt_str . "O - $reccnt&nbsp;&nbsp;&nbsp;";
		}
		if ($yahoo_flag eq "Y")
		{
			$sql = "select sum(yahoo_cnt) from list where list_id in (select list_id from list_profile_list where profile_id=$pid)";
			$sth2 = $dbhq->prepare($sql) ;
			$sth2->execute();
			($reccnt) = $sth2->fetchrow_array();
			$sth2->finish();
			if ($reccnt eq "")
			{
				$reccnt = 0;
			}
			$total_cnt = $total_cnt + $reccnt;
			$reccnt=commify($reccnt);
			$reccnt_str = $reccnt_str . "Y - $reccnt&nbsp;&nbsp;&nbsp;";
		}
		if ($aol_flag eq "Y")
		{
			$sql = "select sum(aol_cnt) from list where list_id in (select list_id from list_profile_list where profile_id=$pid)";
			$sth2 = $dbhq->prepare($sql) ;
			$sth2->execute();
			($reccnt) = $sth2->fetchrow_array();
			$sth2->finish();
			if ($reccnt eq "")
			{
				$reccnt = 0;
			}
			$total_cnt = $total_cnt + $reccnt;
			$reccnt=commify($reccnt);
			$reccnt_str = $reccnt_str . "A - $reccnt&nbsp;&nbsp;&nbsp;";
		}
		if ($hotmail_flag eq "Y")
		{
			$sql = "select sum(hotmail_cnt)+sum(msn_cnt) from list where list_id in (select list_id from list_profile_list where profile_id=$pid)";
			$sth2 = $dbhq->prepare($sql) ;
			$sth2->execute();
			($reccnt) = $sth2->fetchrow_array();
			$sth2->finish();
			if ($reccnt eq "")
			{
				$reccnt = 0;
			}
			$total_cnt = $total_cnt + $reccnt;
			$reccnt=commify($reccnt);
			$reccnt_str = $reccnt_str . "H - $reccnt&nbsp;&nbsp;&nbsp;";
		}
		$total_cnt=commify($total_cnt);
		$reccnt_str = "T - $total_cnt&nbsp;&nbsp;&nbsp;" . $reccnt_str;		
	}
	if ($pid == $profile_id)
	{
		print "<option value=$pid selected>$pname: $reccnt_str</option>\n";
	}
	else
	{
		print "<option value=$pid>$pname: $reccnt_str</option>\n";
	}
}
$sth1->finish();
}
else
{
	$sql="select distinct third_party_defaults.third_party_id,mailer_name,profile_name,profile_id from third_party_defaults,list_profile where third_party_defaults.third_party_id=list_profile.third_party_id and client_id=$cid and list_profile.status='A' order by mailer_name,profile_name";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	while (($temp_tid,$mailer_name,$profile_name,$temp_profile_id) = $sth2->fetchrow_array())
	{
		if (($temp_tid == $third_id) && ($temp_profile_id == $profile_id))
		{
			print "<option selected value=$temp_tid|$temp_profile_id>$mailer_name - $profile_name</option>\n";
		}
		else
		{
			print "<option value=$temp_tid|$temp_profile_id>$mailer_name - $profile_name</option>\n";
		}		
	}
	$sth2->finish();
}
print<<"end_of_html";
									</select></td>
		<td>
									<select name="bid_${type_arr[$i][1]}_${slot_id}" size="1">
end_of_html
my $other_cnt;
my $yahoo_cnt;
$sql="select brand_id,brand_name from client_brand_info where client_id=$cid and status='A' order by brand_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($pid,$pname) = $sth1->fetchrow_array())
{
	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type in ('O','Y')";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	($host_cnt) = $sth2->fetchrow_array();
	$sth2->finish();
	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type='O' and server_name not in (select server_name from brand_host where brand_id=$pid and server_type ='Y')";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	($other_cnt) = $sth2->fetchrow_array();
	$sth2->finish();
	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type='Y' and server_name not in (select server_name from brand_host where brand_id=$pid and server_type ='O')";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	($yahoo_cnt) = $sth2->fetchrow_array();
	$sth2->finish();
	my $tcnt = $other_cnt + $yahoo_cnt;
	if ($tcnt < $host_cnt)
	{
		$other_cnt = $other_cnt + ($host_cnt-$tcnt)/2.0;
		$yahoo_cnt = $yahoo_cnt + ($host_cnt-$tcnt)/2.0;
	}
	if ($pid == $brand_id)
	{
		if ($type_arr[$i][1] ne "3")
		{
			print "<option value=$pid selected>$pname:&nbsp;&nbsp;O - $other_cnt&nbsp;&nbsp;&nbsp;&nbsp;Y - $yahoo_cnt</option>\n";
		}
		else
		{		
			print "<option value=$pid selected>$pname</option>\n";
		}
	}
	else
	{
		if ($type_arr[$i][1] ne "3")
		{
			print "<option value=$pid>$pname:&nbsp;&nbsp;O - $other_cnt&nbsp;&nbsp;&nbsp;&nbsp;Y - $yahoo_cnt</option>\n";
		}
		else
		{		
			print "<option value=$pid >$pname</option>\n";
		}
	}
}
$sth1->finish();
print<<"end_of_html";
									</select></td>
end_of_html
if ($cstatus eq "A")
{
	print "<input type=hidden name=stat_${type_arr[$i][1]}_${slot_id} value=\"A\">\n";
	print "<td align=center><a href=\"/cgi-bin/delete_slot.cgi?cid=$cid&sid=$slot_id&flag=D&stype=${type_arr[$i][1]}\">Delete</a></td>\n";
}
else
{
	print "<input type=hidden name=stat_${type_arr[$i][1]}_${slot_id} value=\"D\">\n";
	print "<td align=center><a href=\"/cgi-bin/delete_slot.cgi?cid=$cid&sid=$slot_id&flag=A&stype=${type_arr[$i][1]}\">UnDelete</a></td>\n";
}
print<<"end_of_html";
	</tr>
end_of_html
}
	print "</table>";
$sth->finish();
$i++;
}
print<<"end_of_html";
<p align="left">
						<a href="/blank.html">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0">
						<input type="image" src="/images/save.gif" border="0" name="I1"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p>
</form>
</body>
</html>
end_of_html
#
$util->clean_up();
exit(0);

sub commify
{
    my $text = reverse $_[0];
    $text =~ s/(\d\d\d)(?=\d)(?!\d*\.)/$1,/g;
    return scalar reverse $text;
}
