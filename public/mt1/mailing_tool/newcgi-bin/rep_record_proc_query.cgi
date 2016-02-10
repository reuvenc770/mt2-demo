#!/usr/bin/perl
# *****************************************************************************************
# rep_record_proc_query.cgi
#
# this page displays the Record Processing For the Specified Month 
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
my @UNQ;
my @YAHOO;
my @OTHERS;
my @AOL;
my @HOTMAIL;
my @COMCAST;
my @PHONE;
my @POSTAL;
my @DELIVERY;
my @OPEN;
my @CLICK;
my @YAHOO1;
my @OTHERS1;
my @AOL1;
my @HOTMAIL1;
my @COMCAST1;
my $i;
my $cidcnt;
my $advertiser_name;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;
my ($recs,$dups,$bad_format,$bad_domain,$supp_cnt,$yahoo_cnt,$others_cnt,$aol_cnt,$hotmail_cnt, $comcast_cnt,$phoneCnt, $postalCnt, $delCnt, $openCnt, $clickCnt); 
my $unq_cnt;
my $set_id;
my @mon_str = (
'','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec' );
my $cmonth=$query->param('cmonth');
my $cyear=$query->param('cyear');
my $current_month;
my $current_year;
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select month(curdate()),year(curdate())"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($current_month,$current_year) = $sth->fetchrow_array();
$sth->finish();
if ($cmonth eq "")
{
	$cmonth=$current_month;
}
if ($cyear eq "")
{
	$cyear=$current_year;
}



# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

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

my $client_id = $query->param('client_id');

#if ($client_id)
#{
#	$set_id = $client_id;
#}
#else
#{
#	$sql = "select min(user_id),max(user_id) from user where status='A'";
#	$sth = $dbhq->prepare($sql);
#	$sth->execute();
#	($client_id,$set_id) = $sth->fetchrow_array();
#	$sth->finish();
#}

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
<h3>Record Processing Report - $mon_str[$cmonth]/$cyear</h3>
<center>
<form method=post action="/cgi-bin/rep_record_proc_query.cgi">
Month: <select name=cmonth>
end_of_html
my $i=1;
while ($i <= $#mon_str)
{
	if ($cmonth == $i)
	{
		print "<option value=$i selected>$mon_str[$i]</option>";	
	}
	else
	{
		print "<option value=$i>$mon_str[$i]</option>";	
	}
	$i++;
}
print << "end_of_html";
</select>&nbsp;&nbsp;&nbsp;Year: <select name=cyear>
end_of_html
my $i = $current_year - 2;
while ($i <= $current_year)
{
	if ($i == $cyear)
	{
		print "<option value=$i selected>$i</option>";
	}
	else
	{
		print "<option value=$i>$i</option>";
	} 
	$i++;
}

print << "end_of_html";
</select>&nbsp;&nbsp;&nbsp;Client: <select name=client_id>
end_of_html

	$sql = "select user_id, username from user order by username";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (my $userData = $sth->fetchrow_hashref()){
		if ($client_id == $userData->{'user_id'})
		{	
			print "<option selected value=$userData->{'user_id'} >$userData->{'username'}</option>";	
		}
		else
		{
			print "<option value=$userData->{'user_id'} >$userData->{'username'}</option>";	
		}
	}

print << "end_of_html";
</select>&nbsp;&nbsp;<input type=submit value="Go">
</form>
<p>
end_of_html
#
$cdate = $cyear . "-" . $cmonth . "-01";
$sql = "select month(date_add('$cdate',interval 1 month)),year(date_add('$cdate',interval 1 month))";
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$sth->finish();
$cdate1 = $year_str . "-" . $month_str . "-01";

if($client_id){
	
#while ($client_id <= $set_id)
#{
	$cidcnt = 0;
	$sql = "select first_name from user where user_id=$client_id"; ### use first_name rather than company ##10/5/06 by ray
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($company) = $sth->fetchrow_array();
	$sth->finish();
#$sql = qq|
#select 
#date_format(date_processed,'%m/%d/%Y'),sum(records_received),sum(duplicates),
#sum(bad_format),sum(bad_domain),sum(supp_cnt),
#sum(hotmail_cnt),sum(unique_records), sum(comcast_cnt),
#sum(phoneCount),
#sum(fullPostalCount),
#sum(recordDeliverables),
#sum(recordPreviousOpeners),
#sum(recordPreviousClickers)
#from 
#	record_processing 
#where 
#	client_id = $client_id 
#	and date_processed >= '$cdate' 
#	and date_processed < '$cdate1' 
#group by 1 
#order by 1|;

$sql = qq|
select
temp.*,
sum(if(class_name = 'AOL', totalRecords, 0) + temp.aol_cnt) as aol_cnt,
sum(if(class_name = 'Hotmail', totalRecords, 0) + temp.hotmail_cnt) as hotmail_cnt,
sum(if(class_name = 'Yahoo', totalRecords, 0) + temp.yahoo_cnt) as yahoo_cnt,
sum(if(class_name = 'Others', totalRecords, 0) + temp.others_cnt) as others_cnt,
sum(if(class_name = 'Comcast', totalRecords, 0) + temp.comcast_cnt) as comcast_cnt
from
(
select
date_format(date_processed,'%Y-%m-%d') as processedDate,
sum(records_received) as records_received,
sum(duplicates) as duplicates,
sum(bad_format) as bad_format,
sum(bad_domain) as bad_domain,
sum(supp_cnt) as supp_cnt,
sum(unique_records) as unique_records,
sum(phoneCount) as phoneRecords,
sum(fullPostalCount) as fullPostalCount,
sum(recordDeliverables) as recordDeliverables,
sum(recordPreviousOpeners) as recordPreviousOpeners,
sum(recordPreviousClickers) as recordPreviousClickers,
sum(aol_cnt) as aol_cnt,
sum(hotmail_cnt) as hotmail_cnt,
sum(yahoo_cnt) as yahoo_cnt,
sum(others_cnt) as others_cnt,
sum(comcast_cnt) as comcast_cnt

from
	record_processing
where
	client_id = $client_id
	and date_processed >= '$cdate'
	and date_processed < '$cdate1'  
group by processedDate

) as temp

LEFT OUTER JOIN ClientRecordTotalsByIsp c on temp.processedDate = c.processedDate and c.clientID = $client_id
LEFT OUTER JOIN user u1 on c.clientID = u1.user_id
LEFT OUTER JOIN email_class ec on c.classID = ec.class_id

where
1=1

group by

temp.processedDate,
records_received,
duplicates,
bad_format,
bad_domain,
supp_cnt,
unique_records,
phoneRecords,
fullPostalCount,
recordDeliverables,
recordPreviousOpeners,
recordPreviousClickers,
hotmail_cnt,
comcast_cnt,
aol_cnt,
others_cnt,
yahoo_cnt

order by processedDate

|;
$sth = $dbhq->prepare($sql);
$sth->execute();

my ($OLD_aol_cnt,$OLD_hotmail_cnt,$OLD_yahoo_cnt,$OLD_others_cnt,$OLD_comcast_cnt);

while (($sdate,$recs,$dups,$bad_format,$bad_domain,
$supp_cnt,$unq_cnt, $phoneCnt, $postalCnt, $delCnt, $openCnt, $clickCnt, 
$OLD_aol_cnt,$OLD_hotmail_cnt,$OLD_yahoo_cnt,$OLD_others_cnt,$OLD_comcast_cnt,
$aol_cnt,$hotmail_cnt,$yahoo_cnt,$others_cnt,$comcast_cnt) = $sth->fetchrow_array())
{
	$CDATEARR[$cidcnt] = $sdate;	
	$RECS[$cidcnt] = $recs;	
	$DUPS[$cidcnt] = $dups;	
	$BAD_FORMAT[$cidcnt] = $bad_format;	
	$BAD_DOMAIN[$cidcnt] = $bad_domain;	
	$SUPP[$cidcnt] = $supp_cnt;	
	$UNQ[$cidcnt] = $unq_cnt;	
	$YAHOO[$cidcnt] = $yahoo_cnt;	
	$OTHERS[$cidcnt] = $others_cnt;	
	$AOL[$cidcnt] = $aol_cnt;	
	$HOTMAIL[$cidcnt] = $hotmail_cnt;
	$COMCAST[$cidcnt] =  $comcast_cnt;	
	
	$PHONE[$cidcnt] =  $phoneCnt;
	$POSTAL[$cidcnt] =  $postalCnt;
	$DELIVERY[$cidcnt] =  $delCnt;
	$OPEN[$cidcnt] =  $openCnt;
	$CLICK[$cidcnt] =  $clickCnt;	

	$sql = qq|select comcast_cnt, yahoo_cnt,others_cnt,aol_cnt,hotmail_cnt from total_mailable where 
	client_id=$client_id and date_processed="$sdate"|;
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	if (my ($comcast_cnt_mailable, $yahoo_cnt_mailable,$others_cnt_mailable,$aol_cnt_mailable,$hotmail_cnt_mailable) = $sth1->fetchrow_array())
	{
		$YAHOO1[$cidcnt]=$yahoo_cnt_mailable;
		$OTHERS1[$cidcnt] = $others_cnt_mailable;	
		$AOL1[$cidcnt] = $aol_cnt_mailable;	
		$HOTMAIL1[$cidcnt] = $hotmail_cnt_mailable;	
		$COMCAST1[$cidcnt] = $comcast_cnt_mailable;	
	}
	else
	{
		$YAHOO1[$cidcnt]="";
		$OTHERS1[$cidcnt] = "";	
		$AOL1[$cidcnt] = "";	
		$HOTMAIL1[$cidcnt] = "";
		$COMCAST1[$cidcnt] = "";	
	}
	
	
	
	
	$sth1->finish();
	$cidcnt++;
}
$sth->finish();
# print out html page
print << "end_of_html";
<center>
<h3>Network: $company</h3>
end_of_html
if ($cidcnt > 0)
{
print << "end_of_html";
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
print "</tr>";
print "<TR><td>Unique</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$UNQ[$i]</font></td>\n";
	$i++;
}
print "</tr>";
print "<TR><td>Unique %</td>";
$i = 0;
while ($i < $cidcnt)
{
	my $perc;
	if ($RECS[$i] > 0)
	{
		$perc=($UNQ[$i]/$RECS[$i])*100;
	}
	else
	{
		$perc=0;
	}
	printf "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>%4.2f\%</font></td>\n",$perc;
	$i++;
}

print "<TR><td>Phone</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$PHONE[$i]</font></td>\n";
	$i++;
}
print "</tr>";

print "<TR><td>Full Postal</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$POSTAL[$i]</font></td>\n";
	$i++;
}
print "</tr>";

print "<TR><td>Deliverables</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$DELIVERY[$i]</font></td>\n";
	$i++;
}
print "</tr>";

print "<TR><td>Prev. Openers</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$OPEN[$i]</font></td>\n";
	$i++;
}
print "</tr>";

print "<TR><td>Prev. Clickers</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$CLICK[$i]</font></td>\n";
	$i++;
}
print "</tr>";

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

print "<TR><td>Comcast</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$COMCAST[$i]</font></td>\n";
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

print "<TR><td>Comcast</td>";
$i = 0;
while ($i < $cidcnt)
{
	print "<TD align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$COMCAST1[$i]</font></td>\n";
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

print "</tr>" . getUnsubCountsByIsp($client_id, $cdate, $cdate1) . "</table>";
}
$client_id++;

#
#}

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

#sub getUnsubCountsByIsp
#{
#	my ($client_id, $cdate, $cdate1) = @_;
#	
#	my $sql = qq|
#	select
#		ec.class_name as className,
#		coalesce(sum(recordCount), 0) as recordCount
#	from 
#		CaptureDateEmailRecordCounts c
#		LEFT OUTER JOIN email_class ec on c.classID = ec.class_id
#	where 
#		clientID = $client_id
#	group by 
#		className
#	order by class_id
#	
#	between "$cdate" and "$cdate1"
#	|;
#	
#	my $sth = $dbhq->prepare($sql);
#	$sth->execute();
#	
#	my $html = qq|<tr><td colspan='2'><b>Email Records Counts By ISP</b></td></tr>|;
#		
#	while(my $data = $sth->fetchrow_hashref())
#	{
#
#		$html .= qq|<tr>
#			<td align=middle>$data->{'className'}</td>
#			<td align=middle><font face=Verdana,Arial,Helvetica,sans-serif size=1>$data->{'recordCount'}</font></td>
#		</tr>|;
#		
#	}
#
#	return($html);
#	
#}

sub getRecordCountsByIsp 
{
	
	my ($client_id) = @_;
	
#	my $sql = qq|
#	select
#		ec.class_name as className,
#		coalesce(sum(recordCount), 0) as recordCount
#	from 
#		CaptureDateEmailRecordCounts c
#		LEFT OUTER JOIN email_class ec on c.classID = ec.class_id
#	where 
#		clientID = $client_id
#	group by 
#		className
#	order by class_id
#	|;
#	
#	my $sth = $dbhq->prepare($sql);
#	$sth->execute();
#	
#	my $html = qq|<tr><td colspan='2'><b>Email Records Counts By ISP</b></td></tr>|;
#		
#	while(my $data = $sth->fetchrow_hashref())
#	{
#
#		$html .= qq|<tr>
#			<td align=middle>$data->{'className'}</td>
#			<td align=middle><font face=Verdana,Arial,Helvetica,sans-serif size=1>$data->{'recordCount'}</font></td>
#		</tr>|;
#		
#	}
#
#	return($html);
	
}



sub getEmailClasses
{
	
	my $sth = $dbhq->prepare(qq|select * from email_class where status = 'Active' order by class_name|);
	$sth->execute();
	
	my $classData = []; 
	while (my $data = $sth->fetchrow_hashref())
	{
		push(@{$classData}, $data->{'class_name'});
	}
	
	return($classData);
}