#!/usr/bin/perl
# *****************************************************************************************
# rep_client_record_proc.cgi
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
my $sth;
my $sth1;
my $html_template;
my $sql;
my $dbh;
my $errmsg;
my $campcnt;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;
my $userid;
my $company;
my $upl_freq;
my $avgcnt;
my $unqcnt;
my $mailcnt;
my $mail_per;
my $mail_unq_per;
my $maxdate;
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# print out html page

util::header("Daily Records Sent by Client Report");

print <<"end_of_html";
<tr><td>
	<center>
    <TABLE cellSpacing=0 cellPadding=10 border=0 width="100%">
    <TBODY>
        <TR>
        <TD align=middle>
	<center>	
            <TABLE cellSpacing=0 cellPadding=0 width=900 border=1>
            <TBODY>
            <TR bgColor="$table_header_bg">
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Client Name</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Upload<br>Frequency</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Average Daily<br>Records Sent</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Avg Daily<br>Mailable Records</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>% Records<br>Mailable</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Avg #<br>Uniques</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>% Uniques</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Campaigns<br>per Day</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Last Processed<br>Date</B> </FONT></TD>
			</TR>
end_of_html

$sql = "select user_id,username,upl_freq from user where status='A' and upl_freq !='TBD' order by username";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($userid,$company,$upl_freq) = $sth1->fetchrow_array())
{
	$sql="select sum(records_received)/30,sum(yahoo_cnt+others_cnt+aol_cnt+hotmail_cnt)/30,sum(unique_records)/30 from record_processing where client_id=? and date_processed >= date_sub(curdate(),interval 30 day)";
	my $sth=$dbhq->prepare($sql);
	$sth->execute($userid);
	($avgcnt,$mailcnt,$unqcnt)=$sth->fetchrow_array();
	$sth->finish();
	if (($avgcnt eq "") or ($avgcnt == 0))
	{
		$mail_per=0;
		$mail_unq_per=0;
	}
	else
	{
		$mail_per=($mailcnt/$avgcnt) * 100;
		$mail_unq_per=($unqcnt/$avgcnt) * 100;
	}
	$sql="select max(date_processed) from record_processing where client_id=?";
	my $sth=$dbhq->prepare($sql);
	$sth->execute($userid);
	($maxdate)=$sth->fetchrow_array();
	$sth->finish();
	$sql="select count(*) from campaign,list_profile where scheduled_date=curdate() and campaign.profile_id=list_profile.profile_id and deleted_date is null and list_profile.client_id=?";
	my $sth=$dbhq->prepare($sql);
	$sth->execute($userid);
	($campcnt)=$sth->fetchrow_array();
	$sth->finish();
	print "<tr><td>$company</td><td>$upl_freq</td><td>$avgcnt</td><td>$mailcnt</td><td>";
	printf "%4.2f\%</TD>",$mail_per;
	printf "<td>$unqcnt</td><td>%4.2f\%</td>",$mail_unq_per;
	print "<td align=middle>$campcnt</td><td>$maxdate</td></tr>\n"
}
$sth1->finish();
$sql = "select user_id,username,upl_freq from user where status='A' and upl_freq ='TBD' order by username";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($userid,$company,$upl_freq) = $sth1->fetchrow_array())
{
	$sql="select sum(records_received)/30,sum(yahoo_cnt+others_cnt+aol_cnt+hotmail_cnt)/30,sum(unique_records)/30 from record_processing where client_id=? and date_processed >= date_sub(curdate(),interval 30 day)";
	my $sth=$dbhq->prepare($sql);
	$sth->execute($userid);
	($avgcnt,$mailcnt,$unqcnt)=$sth->fetchrow_array();
	$sth->finish();
	$mail_per=0;
	$mail_unq_per=0;
	if ($avgcnt > 0)
	{
		$mail_per=($mailcnt/$avgcnt) * 100;
		$mail_unq_per=($unqcnt/$avgcnt) * 100;
	}
	$sql="select max(date_processed) from record_processing where client_id=?";
	my $sth=$dbhq->prepare($sql);
	$sth->execute($userid);
	($maxdate)=$sth->fetchrow_array();
	$sth->finish();
	$sql="select count(*) from campaign,list_profile where scheduled_date=curdate() and campaign.profile_id=list_profile.profile_id and deleted_date is null and list_profile.client_id=?";
	my $sth=$dbhq->prepare($sql);
	$sth->execute($userid);
	($campcnt)=$sth->fetchrow_array();
	$sth->finish();
	print "<tr><td>$company</td><td>$upl_freq</td><td>$avgcnt</td><td>$mailcnt</td><td>";
	printf "%4.2f\%</TD>",$mail_per;
	printf "<td>$unqcnt</td><td>%4.2f\%</td>",$mail_unq_per;
	print "<td align=middle>$campcnt</td><td>$maxdate</td></tr>\n"
}
$sth1->finish();
print<<"end_of_html";
			</TBODY>
			</TABLE>
</TD>
</TR>
</tbody>
</table>
<center>
<p><a href="/newcgi-bin/mainmenu.cgi" target=_top><img src="/mail-images/home_blkline.gif" border=0></a></center>
</body>
</html>
end_of_html
exit(0);
