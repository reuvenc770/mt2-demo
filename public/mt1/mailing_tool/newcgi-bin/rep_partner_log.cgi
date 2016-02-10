#!/usr/bin/perl
# ******************************************************************************
# rep_partner_log.cgi
#
# this page displays the PartnerLog data for the specified day 
#
# History
# Jim Sobeck, 9/16/08, Creation
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
my $sth2;
my $sql;
my $dbh;
my $errmsg;
my $cid;
my $pdelay;
my $cnt;
my $cname;
my $bdate;
my $edate;
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
my @UNQ;
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
my $unq_cnt;
my $set_id;
my @mon_str = (
'','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec' );
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
#
$bdate = $query->param('bdate');
$edate = $query->param('edate');
if ($bdate eq "")
{
    $sql = "select curdate()";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($bdate) = $sth->fetchrow_array();
    $sth->finish();
    $edate = $bdate;
}


# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $client_id;
#
my $CID;

# print out html page
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Records Sent by Partner</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<h3>Records Sent Report</h3>
<center>
<form method=post action="/cgi-bin/rep_partner_log.cgi">
            Begin Date: <input type=text name=bdate value="$bdate" size=10 maxle
ngth=10>&nbsp;&nbsp;&nbsp;End Date: <input type=text name=edate value="$edate" s
ize=10 maxlength=10>&nbsp;&nbsp;&nbsp;<input type=submit value="Go">
</form>
<p>
end_of_html
#
# print out html page
print << "end_of_html";
<TABLE cellSpacing=0 cellPadding=0 border=1 width=100%>
<TR>
<th><b>Partner</b></th>
<th>TOTAL</th>
end_of_html
#
$sql="select class_id,class_name from email_class where status='Active' order by class_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	$CID->{$cid}=1;
	print "<th>$cname</th>\n";
}
print "</tr>";
#$sql="select partner_id,partner_name from PartnerInfo where enable_flag='Y' order by partner_name";
$sql="select partner_id,partner_name from PartnerInfo order by partner_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $pid;
my $pname;
my $rows;
$rows=0;
while (($pid,$pname)=$sth->fetchrow_array())
{
	my $delay;
	$sql="select delay from PartnerClientInfo where partner_id=? limit 1";
	my $sth2=$dbhq->prepare($sql);
	$sth2->execute($pid);
	($delay)=$sth2->fetchrow_array();
	$sth2->finish();
	$pdelay=$delay;
	if ($pdelay > 0)
	{
		$pdelay=$pdelay/3600;
	}
	if ($rows % 2)
	{
		print "<tr bgcolor=lightblue><td>$pname<br><b>Delay:</b> $pdelay Hour(s)</td></tr>\n";
	}
	else
	{
		print "<tr bgcolor=#COCOCO><td>$pname<br><b>Delay:</b> $pdelay Hour(s)</td></tr>\n";
	}

	my $client_id;
	my $username;
	$sql="select distinct client_id,username from PartnerLog, user where partner_id=? and date_processed>=? and date_processed <= ? and PartnerLog.client_id=user.user_id";
	my $sth2a=$dbhq->prepare($sql);
	$sth2a->execute($pid,$bdate,$edate);
	while (($client_id,$username)=$sth2a->fetchrow_array())
	{
		my $tstr="";
		my $tcnt=0;
		$sql="select class_id,class_name from email_class where status='Active' order by class_id";
		$sth2 = $dbhq->prepare($sql);
		$sth2->execute();
		while (($cid,$cname)=$sth2->fetchrow_array())
		{
			$sql="select sum(cnt) from PartnerLog where partner_id=? and date_processed>=? and date_processed <= ? and class_id=? and client_id=?";
			my $sth1=$dbhq->prepare($sql);
			$sth1->execute($pid,$bdate,$edate,$cid,$client_id);
			($cnt)=$sth1->fetchrow_array();
			$sth1->finish();
			$tcnt=$tcnt+$cnt;
			$tstr=$tstr."<td align=center>$cnt</td>";
		}
		$sth2->finish();
		if ($rows % 2)
		{
			print "<tr bgcolor=lightblue><td>&nbsp;&nbsp;<b>$username</b></td>";
		}
		else
		{
			print "<tr bgcolor=#COCOCO><td>&nbsp;&nbsp;<b>$username</b></td>";
		}
		print "<td align=center>$tcnt</td>$tstr\n";
		print "</tr>\n";
	}
	$sth2a->finish();
	$rows++;
}
$sth->finish();
print<<"end_of_html";
</table>
<br>
            <a href="mainmenu.cgi">
            <IMG src="$images/home_blkline.gif" border=0></a>
</body>
</html>
end_of_html

$util->clean_up();
exit(0);
