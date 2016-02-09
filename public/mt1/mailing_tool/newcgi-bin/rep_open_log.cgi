#!/usr/bin/perl
# *****************************************************************************************
# rep_open_log.cgi
#
# this page displays the Move_open_log For the Current Month 
#
# History
# Jim Sobeck, 9/23/05, Creation
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
my $reccnt;
my $reccnt_total;
my $sent_cnt;
my $total_sent_cnt;
my $sublist_index;
my $cid;
my $pid;
my $cid_str;
my $company;
my $subject;
my $creative_name;
my $from;
my $open_percent;
my $sdate;
my $sdate1;
my $i;
my $cidcnt;
my $advertiser_name;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;
my ($recs,$dups,$bad_format,$bad_domain,$supp_cnt,$yahoo_cnt,$others_cnt,$aol_cnt,$hotmail_cnt); 
my $set_id;
my $list_name;
my $list_id;
my $list_cnt;

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
my $client_id;
my @raw_cookies;
my %cookies;
my $key;
my $val;
@raw_cookies = split (/; /,$ENV{'HTTP_COOKIE'});
foreach (@raw_cookies)
{
    ($key, $val) = split (/=/,$_);
    $cookies{$key} = $val;
}
$set_id = $cookies{'networkopt'};
if ($set_id > 0)
{
	$client_id=$set_id;
}
else
{
	$sql = "select min(user_id),max(user_id) from user where status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($client_id,$set_id) = $sth->fetchrow_array();
	$sth->finish();
}

#
my $cdate;
my $cdate1;
my $month_str;
my $year_str;
my @CDATEARR;
my @CDATEARR1;
my @OPENTOT;
my @SENTTOT;
my @RECS;
my @LNAME;
my @LID;

# print out html page
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Open Log by Network</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<h3>Open Log Report -Current Month</h3>
end_of_html
#
$sql = "select month(date_sub(curdate(),interval 1 month)),year(date_sub(curdate(),interval 1 month))";
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate1 = $year_str . "-" . $month_str . "-01";
$sth->finish();
$sql = "select month(curdate()),year(curdate())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
$sth->finish();
while ($client_id <= $set_id)
{
	$cidcnt = 0;
	$sql = "select company from user where user_id=$client_id"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($company) = $sth->fetchrow_array();
	$sth->finish();
	$sql = "select distinct date_format(date_processed,'%m/%d/%Y'),date_processed from move_open_log where client_id=$client_id and date_processed >= '$cdate' order by 1"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($sdate,$sdate1) = $sth->fetchrow_array())
	{
    	$CDATEARR[$cidcnt] = $sdate;
    	$CDATEARR1[$cidcnt] = $sdate1;
		$sql = "select sum(reccnt) from move_open_log where client_id=$client_id and date_processed = '$sdate1'"; 
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($reccnt_total) = $sth1->fetchrow_array();
		$sth1->finish();
		$OPENTOT[$cidcnt] = $reccnt_total;
##		$sql = "select sum(sent_cnt) from profile_log where campaign_id in (select campaign_id from campaign where sent_datetime= '$sdate1') and list_id in (select list_id from list where user_id=$client_id)";
        $sql = "select sum(sent_cnt) from profile_log,campaign,list_profile where sent_datetime= '$sdate1' and campaign.profile_id=list_profile.profile_id and list_profile.client_id=$client_id and campaign.campaign_id=profile_log.campaign_id and campaign.profile_id=profile_log.profile_id";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($total_sent_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
		$SENTTOT[$cidcnt] = $total_sent_cnt;
		$cidcnt++;
	}
	$list_cnt = 0;
	$sql = "select distinct move_open_log.list_id,list_name from move_open_log,list where client_id=$client_id and date_processed >= '$cdate' and move_open_log.list_id=list.list_id order by 1"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($list_id,$list_name) = $sth->fetchrow_array())
	{
    	$LID[$list_cnt] = $list_id;
    	$LNAME[$list_cnt] = $list_name;
		$list_cnt++;
	}
	$sth->finish();
# print out html page
if ($cidcnt > 0)
{
print << "end_of_html";
<center>
<h3>Network: $company</h3>
<TABLE cellSpacing=0 cellPadding=0 border=1>
<TR>
<td><b>Process Date</b></td>
end_of_html
#
$i = 0;
open(LOG,">/tmp/acgi.log");
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1><b>$CDATEARR[$i]</b></font></td>\n";
	$i++;
}
print "</tr>";
$i = 0;
print "<tr><td></td>\n";
while ($i < $cidcnt)
{
	print "<TD align=middle><table border=1 cellspacing=2><tr><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>New<br>Openers</font></td><td width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>Records<br>Mailed<br>in Sublist</font></td><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>Sublist<br>Index</font></td></tr></table></td>\n";
	$i++;
}
print "</tr>";
my $j=0;
while ($j < $list_cnt)
{
print "<TR><td>$LNAME[$j]</td>";
$i = 0;
while ($i < $cidcnt)
{
	$sql = "select reccnt from move_open_log where client_id=$client_id and date_processed = '$CDATEARR1[$i]' and list_id=$LID[$j]"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	print LOG "<$sql>\n";
	if (($reccnt) = $sth->fetchrow_array())
	{
		$sth->finish();
#		$sql = "select sum(sent_cnt) from profile_log where list_id=$LID[$j] and campaign_id in (select campaign_id from campaign where sent_datetime= '$CDATEARR1[$i]')";
		$sql = "select sum(sent_cnt) from profile_log,campaign where list_id=$LID[$j] and campaign.campaign_id=profile_log.campaign_id and sent_datetime= '$CDATEARR1[$i]'";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		print LOG "<$sql>\n";
		($sent_cnt) = $sth->fetchrow_array();
		$sth->finish();
#
#		Calculate sublist index
#
		if ($sent_cnt > 0)
		{
			$sublist_index = ($reccnt/$OPENTOT[$i])/($sent_cnt/$SENTTOT[$i]);
		}
		else
		{
			$sublist_index = 0;
		}
		printf "<TD align=middle><table border=0 cellspacing=2><tr><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$reccnt</font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$sent_cnt</font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>%4.2f</font></td></tr></table></td>\n",$sublist_index;
	}
	else
	{
		$sth->finish();
		print "<TD align=middle><table border=0 cellspacing=2><tr><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1></font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1></font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1></font></td></tr></table></td>\n";
	}
	$i++;
}
print "</tr>";
$j++;
}
print "<tr><td><b>TOTAL</b></td>\n";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><table border=0 cellspacing=2><tr><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1><b>$OPENTOT[$i]</b></font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1><b>$SENTTOT[$i]</b></font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1></font></td></tr></table></td>\n";
	$i++;
}
print "</tr>";
print "</table>";
}
$client_id++;
}
close(LOG);
print<<"end_of_html";
<br>
            <a href="mainmenu.cgi">
            <IMG src="$images/home_blkline.gif" border=0></a>
</body>
</html>
end_of_html

$util->clean_up();
exit(0);
