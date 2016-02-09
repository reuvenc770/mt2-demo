#!/usr/bin/perl

# *****************************************************************************************
# build_past_schedule.pl 
#
# This program builds the past 7 or 14 days schedule info 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
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
my $bdate1;
my $edate1;
my $old_date;
my $old_property;
my $drop_cnt;
my $total_cnt;
my $grand_total_cnt;
my $total_properties;
my $mode = $query->param('mode');
my @CNAMEARR = [];
my $i;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
#
if ($mode eq "7")
{
	$sql = "select date_format(date_sub(curdate(),interval 7 day),\"%d-%M-%y\"),date_format(curdate(),\"%d-%M-%y\"),date_sub(curdate(),interval 7 day),curdate()";
}
else
{
	$sql = "select date_format(date_sub(curdate(),interval 14 day),\"%d-%M-%y\"),date_format(curdate(),\"%d-%M-%y\"),date_sub(curdate(),interval 14 day), curdate()";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
($bdate,$edate,$bdate1,$edate1) = $sth->fetchrow_array();
$sth->finish();

    print "Content-Type: text/html\n\n";
print <<"end_of_html";
<html>
<head><title>Offer Schedule</title></head>
<body>
<h2>Offer Schedule - Data for $bdate thru $edate</h2>
<p>
<center><a href="/schedule_info.html">Offer Schedule</a>&nbsp;&nbsp;&nbsp;<a href="/mail-bin/build_past_schedule.pl?mode=7">Last 7 Days</a>&nbsp;&nbsp;&nbsp;<a href="/mail-bin/build_past_schedule.pl?mode=14">Last 14 Days</a>
</center>
<br>
<table width = 1200 border=0>
<tr bgcolor="grey"><th>Date</th>
end_of_html
#
$sql = "select distinct property from schedule_info where scheduled_datetime >= '$bdate1' and scheduled_datetime <= '$edate1' order by property";
$sth = $dbhq->prepare($sql);
$sth->execute();
$i = 0;
while (($property) = $sth->fetchrow_array())
{
	print "<th><b>$property</b></th>\n";
	$CNAMEARR[$i] = $property;
	$i++;
}
$sth->finish();
$total_properties = $i;
$total_properties--;
print "<th>TOTAL</th>\n";
print "</tr>\n";
#
$sql = "select date_format(scheduled_datetime,\"%d-%M-%y\"),property,campaign_name,drop_cnt from schedule_info where scheduled_datetime >= '$bdate1' and scheduled_datetime <= '$edate1' order by scheduled_datetime,property";
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
		print "<tr><td valign=top>$cdate</td>\n";
		$old_date = $cdate;
	}
	if ($cdate ne $old_date)
	{
		$total_cnt = commify($total_cnt);
		$grand_total_cnt = commify($grand_total_cnt);
		print "<b>Total: $total_cnt</b>\n";
        while ($i < $total_properties)
        {
            print "</td><td valign=top>\n";
            $i++;
        }
        print "</td><td valign=top><b>$grand_total_cnt</b>\n";
		print "</td></tr><tr><td>&nbsp;</td></tr><tr><td valign=top>$cdate</td>\n";
		$old_date = $cdate;
		$old_property="";
		$total_cnt = 0;
		$grand_total_cnt = 0;
	}
	if ($old_property eq "")
	{
		$old_property = $property;
		$i = 0;
		print "<td valign=top>\n";
	}
	if ($old_property ne $property) 
	{
		$total_cnt = commify($total_cnt);
		print "<b>Total: $total_cnt</b>\n";
		print "</td><td valign=top>\n";
		$old_property = $property;
		$total_cnt = 0;
		$i++;
	}
	while ($CNAMEARR[$i] ne $property)
	{
		print "</td><td valign=top>\n";
		$i++;
	}
	$old_property = $CNAMEARR[$i];
	print "<font size=-1>$cname/$drop_cnt</font><br>\n";
	$total_cnt = $total_cnt + $drop_cnt;
    $grand_total_cnt = $grand_total_cnt + $drop_cnt;
}
$sth->finish();
$total_cnt = commify($total_cnt);
$grand_total_cnt = commify($grand_total_cnt);
print <<"end_of_html";
<b>Total: $total_cnt</b>
</td>
end_of_html
while ($i < $total_properties)
{
    print "<td>&nbsp;</td>\n";
    $i++;
}
print <<"end_of_html";
<td valign=top><b>$grand_total_cnt</b>
</td>
</tr>
</table>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);

sub commify
{
	my $text = reverse $_[0];
	$text =~ s/(\d\d\d)(?=\d)(?!\d*\.)/$1,/g;
	return scalar reverse $text;
}
