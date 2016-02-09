#!/usr/bin/perl
# *****************************************************************************************
# rep_record_proc.cgi
#
# this page displays the Record Processing For the Current Month 
#
# History
# Jim Sobeck, 9/08/05, Creation
# Jim Sobeck, 09/09/05, Add logic for All Networks
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
my $cid;
my $pid;
my $cid_str;
my $company;
my $subject;
my $creative_name;
my $from;
my $open_percent;
my $sdate;
my @CIDARR;
my @CDATEARR;
my @RECS;
my @DUPS;
my @BAD_FORMAT;
my @BAD_DOMAIN;
my @SUPP;
my @YAHOO;
my @OTHERS;
my @AOL;
my @HOTMAIL;
my @YAHOO1;
my @OTHERS1;
my @AOL1;
my @HOTMAIL1;
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

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

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
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($client_id,$set_id) = $sth->fetchrow_array();
	$sth->finish();
}

#
my $cdate;
my $cdate1;
my $month_str;
my $year_str;

# print out html page
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Record Processing by Network</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<h3>Record Processing Report -Current Month</h3>
end_of_html
#
$sql = "select month(date_sub(curdate(),interval 1 month)),year(date_sub(curdate
(),interval 1 month))";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate1 = $year_str . "-" . $month_str . "-01";
$sth->finish();
$sql = "select month(curdate()),year(curdate())";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
$sth->finish();
while ($client_id <= $set_id)
{
	$cidcnt = 0;
	$sql = "select company from user where user_id=$client_id"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($company) = $sth->fetchrow_array();
	$sth->finish();
$sql = "select date_format(date_processed,'%m/%d/%Y'),sum(records_received),sum(duplicates),sum(bad_format),sum(bad_domain),sum(supp_cnt),sum(yahoo_cnt),sum(others_cnt),sum(aol_cnt),sum(hotmail_cnt) from record_processing where client_id=$client_id and date_processed >= '$cdate' group by 1 order by 1";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($sdate,$recs,$dups,$bad_format,$bad_domain,$supp_cnt,$yahoo_cnt,$others_cnt,$aol_cnt,$hotmail_cnt) = $sth->fetchrow_array())
{
	$CDATEARR[$cidcnt] = $sdate;	
	$RECS[$cidcnt] = $recs;	
	$DUPS[$cidcnt] = $dups;	
	$BAD_FORMAT[$cidcnt] = $bad_format;	
	$BAD_DOMAIN[$cidcnt] = $bad_domain;	
	$SUPP[$cidcnt] = $supp_cnt;	
	$YAHOO[$cidcnt] = $yahoo_cnt;	
	$OTHERS[$cidcnt] = $others_cnt;	
	$AOL[$cidcnt] = $aol_cnt;	
	$HOTMAIL[$cidcnt] = $hotmail_cnt;	
	$sql = "select yahoo_cnt,others_cnt,aol_cnt,hotmail_cnt from total_mailable where client_id=$client_id and date_processed=str_to_date('$CDATEARR[$cidcnt]','%m/%d/%Y')";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	if (($yahoo_cnt,$others_cnt,$aol_cnt,$hotmail_cnt) = $sth1->fetchrow_array())
	{
		$YAHOO1[$cidcnt]=$yahoo_cnt;
		$OTHERS1[$cidcnt] = $others_cnt;	
		$AOL1[$cidcnt] = $aol_cnt;	
		$HOTMAIL1[$cidcnt] = $hotmail_cnt;	
	}
	else
	{
		$YAHOO1[$cidcnt]="";
		$OTHERS1[$cidcnt] = "";	
		$AOL1[$cidcnt] = "";	
		$HOTMAIL1[$cidcnt] = "";	
	}
	$sth1->finish();
	$cidcnt++;
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
while ($i < $cidcnt)
{
	print "<TD align=middle width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif size=1><b>$CDATEARR[$i]</b></font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Records Received</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$RECS[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Duplicates</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$DUPS[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Bad formats</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$BAD_FORMAT[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Bad domains</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$BAD_DOMAIN[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Suppression</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$SUPP[$i]</font></td>\n";
	$i++;
}
print "</tr><tr><td><b>Net Mailable Records Added</b></td></tr>";
print "<TR><td>Yahoo</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$YAHOO[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Others</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$OTHERS[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>AOL</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$AOL[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Hotmail/MSN</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$HOTMAIL[$i]</font></td>\n";
	$i++;
}
print "</tr><tr><td><b>Total Mailable Records</b></td></tr>";
print "<TR><td>Yahoo</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$YAHOO1[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Others</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$OTHERS1[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>AOL</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$AOL1[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Hotmail/MSN</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$HOTMAIL1[$i]</font></td>\n";
	$i++;
}
print "</tr></table>";
}
$client_id++;
}
print<<"end_of_html";
<br>
            <a href="mainmenu.cgi">
            <IMG src="$images/home_blkline.gif" border=0></a>
</body>
</html>
end_of_html

$util->clean_up();
exit(0);
