#!/usr/bin/perl

# *****************************************************************************
# upd_newsletter_schedule.cgi
#
# History
# Jim Sobeck, 01/04/07, creation
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sth1b;
my $sth2;
my $sql;
my $dbh;
my $reccnt;
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
my $pid;
my $pname;
my $host_cnt;
my $nl_id;
my $nl_name;
my $nl_slots;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Network Schedule Details</title>
</head>
<body>
<form form=campform method=post action="/cgi-bin/sav_newsletter_slots.cgi">
<table border="0" width="35%" id="table26">
	<tr>
		<td>&nbsp;</td>
		<td>
		&nbsp;</td>
	</tr>
</table>
end_of_html
$sql="select nl_id,nl_name,nl_slots from newsletter where nl_status='A' order by nl_name";
$sth1b=$dbhq->prepare($sql);
$sth1b->execute();
while (($nl_id,$nl_name,$nl_slots)=$sth1b->fetchrow_array())
{
	$sql = "delete from nl_slot_info where nl_id=$nl_id and slot_id > $nl_slots";
	$rows=$dbhu->do($sql);

	$sql="select max(slot_id) from nl_slot_info where nl_id=?";
	$sth2=$dbhq->prepare($sql);
	$sth2->execute($nl_id);
	($max_id)=$sth2->fetchrow_array();
	$sth2->finish();
	$max_id++;

	while ($max_id <= $nl_slots)
	{
		$sql = "insert into nl_slot_info(nl_id,slot_id) values($nl_id,$max_id)";
		$rows=$dbhu->do($sql);
		$max_id++;
	}
#
print<<"end_of_html";
<table border="0" width="35%" id="table4">
	<tr>
		<td><b><font face="Verdana">&nbsp;<a href="/newcgi-bin/newsletter_disp.cgi?pmode=U&nl_id=$nl_id">$nl_name</a>:</font></b></td>
		<td width="205">
		<p align="right"><b><font face="Verdana">&nbsp;</font></b></td>
	</tr>
</table>
<table border="1" width="54%" id="table1">
	<tr><td width="9"><b><font face="Verdana" size="1">#</font></b></td>
		<td width="88"><b><font face="Verdana" size="1">Time:</font></b></td>
		<td width="239"><b><font face="Verdana" size="1">List Profile Name & Quantity:</font></b></td>
		<td width="180"><b><font face="Verdana" size="1"></font></b></td>
	</tr>
end_of_html
	$sql = "select slot_id,hour(schedule_time),profile_id,status from nl_slot_info where nl_id=$nl_id order by slot_id";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	while (($slot_id,$stime,$profile_id,$cstatus) = $sth->fetchrow_array())
	{
print<<"end_of_html";
	<tr>
		<td width="9" align="center"><b><font face="Verdana" size="1">$slot_id</font></b></td>
		<td width="88">
									<select name="tid_${nl_id}_${slot_id}">
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
		print "</select><select name=am_pm_${nl_id}_${slot_id}>\n";
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
									<select name="pid_${nl_id}_${slot_id}" size="1">
end_of_html
		$sql="select profile_id,profile_name,max_emails,aol_flag,yahoo_flag,other_flag,hotmail_flag from list_profile where client_id=2 and profile_type='NEWSLETTER' and nl_id=$nl_id order by profile_name"; 
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		my $reccnt_str="";
		while (($pid,$pname,$max_emails,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag) = $sth1->fetchrow_array())
		{
			$sql="select count(*) from list_profile where nl_id=$nl_id and profile_type='NEWSLETTER' and status='A' and profile_name='$pname'";
			my $sth1a=$dbhq->prepare($sql);
			$sth1a->execute();
			($reccnt)=$sth1a->fetchrow_array();
			$sth1a->finish();
			#
			# If any clients still have this profile then display it
			# 
			if ($reccnt > 0)
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

		}
		$sth1->finish();
print<<"end_of_html";
									</select></td>
end_of_html
			if ($cstatus eq "A")
			{
				print "<input type=hidden name=stat_${nl_id}_${slot_id} value=\"A\">\n";
				print "<td align=center><a href=\"/cgi-bin/delete_nl_slot.cgi?nl_id=$nl_id&sid=$slot_id&flag=D\">Delete</a></td></tr>\n";
			}
			else
			{
				print "<input type=hidden name=stat_${nl_id}_${slot_id} value=\"D\">\n";
				print "<td align=center><a href=\"/cgi-bin/delete_nl_slot.cgi?nl_id=$nl_id&sid=$slot_id&flag=A\">UnDelete</a></td></tr>\n";
			}
	}
	print "</table>";
	$sth->finish();
}
$sth1b->finish();
print<<"end_of_html";
<p align="left">
						<a href="/newcgi-bin/mainmenu.cgi">
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
