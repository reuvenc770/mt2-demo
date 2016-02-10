#!/usr/bin/perl
# *****************************************************************************************
# rep_adv_subject_creative.cgi
#
# this page displays the Advertiser Subject/Creative By Network 
#
# History
# Jim Sobeck, 6/02/05, Creation
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
my $sql;
my $dbh;
my $errmsg;
my $aid=$query->param('aid');
my $client_id=$query->param('clientid');
if ($client_id eq "")
{
	$client_id=0;
}
my $sdate=$query->param('sdate');
my $edate=$query->param('edate');
my $cid;
my $pid;
my $cid_str;
my $company;
my $subject;
my $creative_name;
my $from;
my $open_percent;
my $percent;
my $click_percent;
my $open_cnt;
my $sent_cnt;
my $bounce_cnt;
my $click_cnt;
my $tcid;
my @CIDARR;
my @CDATEARR;
my @PID;
my $i;
my $cidcnt;
my $advertiser_name;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
$user_id = 1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($advertiser_name) = $sth->fetchrow_array();
$sth->finish();
#
$cidcnt = 0;
if ($client_id == 0)
{
	$sql = "select count(*) from campaign where advertiser_id=$aid and scheduled_date >= '$sdate' and scheduled_date <= '$edate' and deleted_date is null and status != 'W' and status != 'T' and campaign_id in (select campaign_id from email_log where sent_cnt > 0 and open_cnt > 0) order by sent_datetime desc";
}
else
{
	$sql = "select count(*) from campaign where advertiser_id=$aid and scheduled_date >= '$sdate' and scheduled_date <= '$edate' and deleted_date is null and campaign.status != 'W' and campaign.status != 'T' and campaign_id in (select campaign_id from email_log where sent_cnt > 0 and open_cnt > 0) and profile_id in (select profile_id from list_profile where client_id=$client_id) order by sent_datetime desc";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
($cidcnt) = $sth->fetchrow_array();
$sth->finish();
if ($cidcnt == 0)
{
$cidcnt = 0;
if ($client_id == 0)
{
	$sql = "select campaign_id,date_format(sent_datetime,'%m/%d/%Y'),profile_id from campaign where advertiser_id=$aid and deleted_date is null and status != 'W' and status!='T' and campaign_id in (select campaign_id from email_log where sent_cnt > 0 and open_cnt > 0) order by sent_datetime desc limit 5";
}
else
{
	$sql = "select campaign_id,date_format(sent_datetime,'%m/%d/%Y'),campaign.profile_id from campaign,list_profile where advertiser_id=$aid and deleted_date is null and campaign.status != 'W' and campaign.status!='T' and campaign_id in (select campaign_id from email_log where sent_cnt > 0 and open_cnt > 0) and campaign.profile_id=list_profile.profile_id and list_profile.client_id=$client_id order by sent_datetime desc limit 5";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
$cid_str="";
while (($cid,$sdate,$pid) = $sth->fetchrow_array())
{
	$cid_str = $cid_str . $cid . ",";
	$CIDARR[$cidcnt] = $cid;
	$CDATEARR[$cidcnt] = $sdate;	
	$PID[$cidcnt] = $pid;	
	$cidcnt++;
}
$sth->finish();
}
else
{
$cidcnt = 0;
if ($client_id == 0)
{
	$sql = "select campaign_id,date_format(sent_datetime,'%m/%d/%Y'),campaign.profile_id from campaign where advertiser_id=$aid and scheduled_date >= '$sdate' and scheduled_date <= '$edate'  and deleted_date is null and status != 'W' and status !='T' and campaign_id in (select campaign_id from email_log where sent_cnt > 0 and open_cnt > 0) order by sent_datetime desc";
}
else
{
	$sql = "select campaign_id,date_format(sent_datetime,'%m/%d/%Y'),campaign.profile_id from campaign,list_profile where advertiser_id=$aid and scheduled_date >= '$sdate'  and scheduled_date <= '$edate' and deleted_date is null and campaign.status != 'W' and campaign.status !='T' and campaign_id in (select campaign_id from email_log where sent_cnt > 0 and open_cnt > 0) and campaign.profile_id=list_profile.profile_id and list_profile.client_id=$client_id order by sent_datetime desc";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
$cid_str="";
while (($cid,$sdate,$pid) = $sth->fetchrow_array())
{
	$cid_str = $cid_str . $cid . ",";
	$CIDARR[$cidcnt] = $cid;
	$CDATEARR[$cidcnt] = $sdate;	
	$PID[$cidcnt] = $pid;	
	$cidcnt++;
}
$sth->finish();
}
$_ = $cid_str;
chop;
$cid_str = $_;
# print out html page
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Advertiser Subject/Creative Stats by Network</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<h3>Subject/Creative Stats by Network Report </h3>
<p>
<font face="verdana,arial,helvetica,sans serif" color="#509C10" size="3"><b>Advertiser : $advertiser_name</b></font><p>
</center>
<TABLE cellSpacing=0 cellPadding=0 width="900" border=1>
<TR bgColor="$table_header_bg">
<TD align=left width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Network</B> </FONT></TD>
<TD align=left width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Total</B> </FONT></TD>
end_of_html
#
#	 Get the network for the campaigns
#
$i = 0;
while ($i < $cidcnt)
{
	if ($PID[$i] > 0)
	{
		$sql = "select company from user where user_id in (select client_id from list_profile where profile_id=$PID[$i])"; 
	}
	else
	{
		$sql = "select company from user where user_id in (select distinct user_id from campaign_list,list where campaign_id=$CIDARR[$i] and campaign_list.list_id=list.list_id)";
	}
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($company) = $sth->fetchrow_array())
{
	print "<TD align=left width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>$company</font></td>\n";
}
else
{
	print "<TD align=left width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1></font></td>\n";
}
$sth->finish();
$i++;
}
print "</tr><tr><td><b>Date</b></td><td></td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD>$CDATEARR[$i]</td>";
	$i++;
}
print<<"end_of_html";
</tr>
<tr><td><b>CID</b></td><td></td>
end_of_html
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=center>$CIDARR[$i]</td>";
	$i++;
}
print<<"end_of_html";
<tr><td><b>Creative Open(s)</b></td></tr>
end_of_html
$i=0;
$sql="select creative_name,creative.creative_id,sum(open_cnt),sum(sent_cnt) from creative,email_log where creative.creative_id=email_log.creative_id and campaign_id in ($cid_str) and open_cnt > 0 and sent_cnt > 0 group by creative_name,creative.creative_id order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($creative_name,$tcid,$open_cnt,$sent_cnt) = $sth->fetchrow_array())
{
	$sql="select sum(block)+sum(hard)+sum(soft)+sum(tech)+sum(unk) from strmail_failure_summary where crID=? and campID in ($cid_str)";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute($tcid);
	($bounce_cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$sent_cnt=$sent_cnt-$bounce_cnt;
	if ($sent_cnt > 0)
	{
		my $temp=($open_cnt/$sent_cnt*100);
		$percent=sprintf("%5.2f",$temp);
	}
	else
	{
		$percent="0.0";
	}
	printf "<tr><td>%s</td><td><b>%d (%d) %5.2f%</b></td>",$creative_name,$open_cnt,$sent_cnt,$percent;
	$i=0;
	while ($i < $cidcnt)
	{
		$sql = "select sum(open_cnt),sum(sent_cnt) from email_log where creative_id=? and campaign_id = ? and open_cnt > 0 and sent_cnt > 0";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute($tcid, $CIDARR[$i]);
		($open_cnt,$sent_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
	$sql="select sum(block)+sum(hard)+sum(soft)+sum(tech)+sum(unk) from strmail_failure_summary where crID=? and campID=?";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute($tcid, $CIDARR[$i]);
	($bounce_cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$sent_cnt=$sent_cnt-$bounce_cnt;
	if ($sent_cnt > 0)
	{
		my $temp=($open_cnt/$sent_cnt)*100;
		$percent=sprintf("%5.2f",$temp);
	}
	else
	{
		$percent="0.0";
	}
		if ($open_cnt > 0)
		{
			printf "<td><b>%d (%d) %5.2f%</b></td>",$open_cnt,$sent_cnt,$percent;
		}
		else
		{
			print "<td>$open_cnt ($sent_cnt)</td>";
		}
		$i++;
	}
	print "</tr>";
}
$sth->finish();
print<<"end_of_html";
<tr><td><b>Creative Clicks(s)</b></td></tr>
end_of_html
$i=0;
$sql="select creative_name,creative.creative_id,sum(click_cnt),sum(open_cnt) from creative,email_log where creative.creative_id=email_log.creative_id and campaign_id in ($cid_str) and open_cnt > 0 and sent_cnt > 0 group by creative_name,creative.creative_id order by creative_name";
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($creative_name,$tcid,$click_cnt,$open_cnt) = $sth->fetchrow_array())
{
		$percent=($click_cnt/$open_cnt)*100;
	printf "<tr><td>%s</td><td><b>%d (%d) %5.2f%</b></td>",$creative_name,$click_cnt,$open_cnt,$percent;
	$i=0;
	while ($i < $cidcnt)
	{
		$sql = "select sum(click_cnt),sum(open_cnt) from email_log where creative_id=? and campaign_id = ? and open_cnt > 0 and sent_cnt > 0";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute($tcid, $CIDARR[$i]);
		($click_cnt,$open_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($open_percent eq "")
		{
			$open_percent="0.0";
		}
		if ($open_cnt > 0)
		{
			$percent=($click_cnt/$open_cnt)*100;
		}
		else
		{
			$percent=0.0;
		}
		if ($click_cnt > 0)
		{
			printf "<td><b>%d (%d) %5.2f%</b></td>",$click_cnt,$open_cnt,$percent;
		}
		else
		{
			print "<td>$click_cnt ($open_cnt) 0.0%</td>";
		}
		$i++;
	}
	print "</tr>";
}
$sth->finish();
print<<"end_of_html";
<tr><td><b>Creative Index</b></td></tr>
end_of_html
$i=0;
$sql="select creative_name,creative.creative_id,sum(click_cnt),sum(sent_cnt) from creative,email_log where creative.creative_id=email_log.creative_id and campaign_id in ($cid_str) and open_cnt > 0 and sent_cnt > 0 group by creative_name,creative.creative_id order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($creative_name,$tcid,$click_cnt,$sent_cnt) = $sth->fetchrow_array())
{
	$sql="select sum(block)+sum(hard)+sum(soft)+sum(tech)+sum(unk) from strmail_failure_summary where crID=? and campID in ($cid_str)";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute($tcid);
	($bounce_cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$sent_cnt=$sent_cnt-$bounce_cnt;
	print "<tr><td>$creative_name</td><td><b>$click_cnt ($sent_cnt)</b></td>";
	$i=0;
	while ($i < $cidcnt)
	{
		$sql = "select sum(click_cnt),sum(sent_cnt) from email_log where creative_id=? and campaign_id = ? and open_cnt > 0 and sent_cnt > 0";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute($tcid, $CIDARR[$i]);
		($click_cnt,$sent_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
	$sql="select sum(block)+sum(hard)+sum(soft)+sum(tech)+sum(unk) from strmail_failure_summary where crID=? and campID=?";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute($tcid,$CIDARR[$i]);
	($bounce_cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$sent_cnt=$sent_cnt-$bounce_cnt;
		print "<td>$click_cnt ($sent_cnt)</td>";
		$i++;
	}
	print "</tr>";
}
$sth->finish();
print<<"end_of_html";
<tr><td><b>Subject Open(s)</b></td></tr>
end_of_html
$i=0;
$sql="select advertiser_subject,advertiser_subject.subject_id,sum(open_cnt),sum(sent_cnt) from advertiser_subject,email_log where advertiser_subject.subject_id=email_log.subject_id and campaign_id in ($cid_str) and open_cnt > 0 and sent_cnt > 0 group by advertiser_subject,advertiser_subject.subject_id and status='A' order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $sid;
while (($subject,$sid,$open_cnt,$sent_cnt) = $sth->fetchrow_array())
{
		$percent=($open_cnt/$sent_cnt)*100;
	printf "<tr><td>%s</td><td><b>%d (%d) %5.2f%</b></td>",$subject,$open_cnt,$sent_cnt,$percent;
	$i=0;
	while ($i < $cidcnt)
	{
		$sql = "select sum(open_cnt),sum(sent_cnt) from email_log where subject_id=? and campaign_id = ? and open_cnt > 0 and sent_cnt > 0";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute($sid, $CIDARR[$i]);
		($open_cnt,$sent_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($open_percent eq "")
		{
			$open_percent="0.0";
		}
		$percent="0.0";
		if ($sent_cnt > 0)
		{
			$percent=($open_cnt/$sent_cnt)*100;
		}
		printf "<td>%d (%d) %5.2f%</td>",$open_cnt,$sent_cnt,$percent;
		$i++;
	}
	print "</tr>";
}
$sth->finish();
print<<"end_of_html";
<tr><td><b>From Open(s)</b></td></tr>
end_of_html
$i=0;
$sql="select advertiser_from,advertiser_from.from_id,sum(open_cnt),sum(sent_cnt) from advertiser_from,email_log where advertiser_from.from_id=email_log.from_id and campaign_id in ($cid_str) and open_cnt > 0 and sent_cnt > 0 group by advertiser_from,advertiser_from.from_id order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $fid;
while (($from,$fid,$open_cnt,$sent_cnt) = $sth->fetchrow_array())
{
		$percent=($open_cnt/$sent_cnt)*100;
	printf "<tr><td>%s</td><td><b>%d (%d) %5.2f%</b></td>",$from,$open_cnt,$sent_cnt,$percent;
	$i=0;
	while ($i < $cidcnt)
	{
		$sql = "select sum(open_cnt),sum(sent_cnt) from email_log where from_id=? and campaign_id = ? and open_cnt > 0 and sent_cnt > 0";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute($fid, $CIDARR[$i]);
		($open_cnt,$sent_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($open_percent eq "")
		{
			$open_percent="0.0";
		}
		$percent=($open_cnt/$sent_cnt)*100;
		printf "<td>%d (%d) %5.2f%</td>",$open_cnt,$sent_cnt,$percent;
		$i++;
	}
	print "</tr>";
}
$sth->finish();
print<<"end_of_html";
</table>
</body>
</html>
end_of_html
$sth->finish();

$util->clean_up();
exit(0);
