#!/usr/bin/perl

# *****************************************************************************************
# build_schedule_info.pl 
#
# This program builds the HTML file: schedule_info.html 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $errmsg;
my $sth;
my $sth2;
my $sql;
my $dbh;
my $dbh1;
my $cstatus;
my $cname;
my $emails_sent;
my $cdate;
my $property;
my $bdate;
my $edate;
my $old_date;
my $old_property;
my $drop_cnt;
my $total_cnt;
my $grand_total_cnt;
my @CNAMEARR = [];
my $i;
my $total_properties;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select date_format(curdate(),\"%d-%M-%y\"),date_format(date_add(curdate(),interval 6 day),\"%d-%M-%y\")";
$sth = $dbhq->prepare($sql);
$sth->execute();
($bdate,$edate) = $sth->fetchrow_array();
$sth->finish();

open (LOG, "> /var/www/util/html/schedule_info.html");
print LOG <<"end_of_html";
<html>
<head><title>Offer Schedule</title></head>
<body>
<h2>Offer Schedule - Week of $bdate thru $edate</h2>
<p>
<center><a href="/schedule_info.html">Offer Schedule</a>&nbsp;&nbsp;&nbsp;<a href="/mail-bin/build_past_schedule.pl?mode=7">Last 7 Days</a>&nbsp;&nbsp;&nbsp;<a href="/mail-bin/build_past_schedule.pl?mode=14">Last 14 Days</a>
</center>
<br>
<table width = 1200 border=0>
<tr bgcolor="grey"><th>Date</th>
end_of_html
#
$sql = "select distinct property from schedule_info where scheduled_datetime >= curdate() order by property";
$sth = $dbhq->prepare($sql);
$sth->execute();
$i = 0;
while (($property) = $sth->fetchrow_array())
{
	print LOG "<th><b>$property</b></th>\n";
	$CNAMEARR[$i] = $property;
	$i++;
}
$sth->finish();
$total_properties = $i;
$total_properties--;
print LOG "<th>TOTAL</th>\n";
print LOG "</tr>\n";
#
$sql = "select date_format(scheduled_datetime,\"%d-%b-%y\"),property,campaign_name,drop_cnt from schedule_info where scheduled_datetime >= curdate() order by scheduled_datetime,property";
$sth = $dbhq->prepare($sql);
$sth->execute();
$old_date = "";
$old_property = "";
$total_cnt = 0;
$grand_total_cnt = 0;
while (($cdate,$property,$cname,$drop_cnt) = $sth->fetchrow_array())
{
	if ($old_date eq "")
	{
		print LOG "<tr><td valign=top>$cdate</td>\n";
		$old_date = $cdate;
	}
	if ($cdate ne $old_date)
	{
		$total_cnt = commify($total_cnt);
		print LOG "<b>Total: $total_cnt</b>\n";
		$grand_total_cnt = commify($grand_total_cnt);
		while ($i < $total_properties)
		{
			print LOG "</td><td valign=top>\n";
			$i++;
		}
		print LOG "</td><td valign=top><b>$grand_total_cnt</b>\n";
		print LOG "</td></tr><tr><td>&nbsp;</td></tr><tr><td valign=top>$cdate</td>\n";
		$old_date = $cdate;
		$old_property="";
		$total_cnt = 0;
		$grand_total_cnt = 0;
	}
	if ($old_property eq "")
	{
		$old_property = $property;
		$i = 0;
		print LOG "<td valign=top>\n";
	}
	if ($old_property ne $property) 
	{
		$total_cnt = commify($total_cnt);
		print LOG "<b>Total: $total_cnt</b>\n";
		print LOG "</td><td valign=top>\n";
		$old_property = $property;
		$total_cnt = 0;
		$i++;
	}
	while ($CNAMEARR[$i] ne $property)
	{
		print LOG "</td><td valign=top>\n";
		$i++;
	}
	$old_property = $CNAMEARR[$i];
	print LOG "<font size=-1>$cname/$drop_cnt</font><br>\n";
	$total_cnt = $total_cnt + $drop_cnt;
	$grand_total_cnt = $grand_total_cnt + $drop_cnt;
}
$sth->finish();
$total_cnt = commify($total_cnt);
$grand_total_cnt = commify($grand_total_cnt);
print LOG <<"end_of_html";
<b>Total: $total_cnt</b>
</td>
end_of_html
while ($i < $total_properties)
{
	print LOG "<td>&nbsp;</td>\n";
	$i++;
}
print LOG <<"end_of_html";
<td valign=top><b>$grand_total_cnt</b>
</td>
</tr>
</table>
</body>
</html>
end_of_html
close LOG;
$util->clean_up();
exit(0);

sub commify
{
	my $text = reverse $_[0];
	$text =~ s/(\d\d\d)(?=\d)(?!\d*\.)/$1,/g;
	return scalar reverse $text;
}
