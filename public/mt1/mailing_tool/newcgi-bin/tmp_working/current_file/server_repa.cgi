#!/usr/bin/perl
# *****************************************************************************************
# powermail_report_detail.cgi
#
# this page displays the powermail report 
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
my $csite;
my $server;
my $dbh;
my $errmsg;
my $campaign_name;
my $campaign_id;
my $sent_datetime;
my $action;
my $cnt;
my $cid;
my $cname;
my $tid;
my $datestr;
my $sent_cnt = 0;
my $scheduled_cnt = 0;
my $queued_cnt = 0;
my $domain_name;
my $id;
my $sname;
my $opened_cnt = 0;
my $delivered_cnt = 0;
my $opened_percent;
my $bounce_cnt = 0;
my $fullmbx_cnt = 0;
my $aol_cnt = 0;
my $uns_cnt = 0;
my $notdelivered_cnt = 0;
my $sent_cnt_str; 
my $opened_cnt_str;
my $delivered_cnt_str;
my $bounce_cnt_str;
my $fullmbx_cnt_str;
my $aol_cnt_str;
my $uns_cnt_str;
my $notdelivered_cnt_str;
my $clicked_cnt = 0;
my $this_month_cnt;
my $last_month_cnt;
my $click_str;
my $click_cnt = 0;
my $total_uns_cnt = 0;
my $total_notdelivered_cnt = 0;
my $total_click_cnt = 0;
my $total_sent_cnt = 0;
my $total_open_cnt = 0;
my $total_delivered_cnt = 0;
my $total_bounce_cnt = 0;
my $total_fullmbx_cnt = 0;
my $total_aol_cnt = 0;
my $three_month_str;
my $opened_percent;
my $clicked_percent;
my $sth1;
my $sth2;
my $reccnt;
my $bgcolor;
my $email_user_id;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;
my $count;
my $cserver=$query->param('cserver');
my $cmonth=$query->param('cmonth');

if ($cmonth eq "")
{
	$cmonth=0;
}

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
$csite = $cookies{'site'};
if ($csite eq "")
{
    $csite = "VA";
}

# print out html page

util::header("Server Report");

print <<"end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=center>
	<center>
    <TABLE cellSpacing=0 cellPadding=10 border=0 width="80%">
    <TBODY>
    <TR>
    <TD vAlign=top align=center bgColor=#ffffff colSpan=10>
		</TD>
		</TR>
        <TR>
        <TD align=middle><font face="Verdana,Arial,Helvetica,sans-serif" 
			color="#509C10" size="3"><b>Campaigns</b></font></TD>
		</TR>
        <TR>
        <TD align=middle>
	<center>	
            <TABLE cellSpacing=0 cellPadding=0 width=900 border=1>
            <TBODY>
            <TR bgColor="$table_header_bg">
            <TD align=left width=100 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Date Sent</B> </FONT></TD>
            <TD align=left width=230 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Server</B> </FONT></TD>
            <TD align=left width=230 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Title</B> </FONT></TD>
            <TD align=left height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Domain</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Scheduled</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Sent</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Delivered</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Initially<br>Queued</B> </FONT></TD>
			</TR>
end_of_html
my $month_str;
my $year_str;
my $cdate;
my $cdate1;

$sql = "select month(curdate()),year(curdate())";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
$sql = "select month(date_sub(curdate(),interval 1 month)),year(date_sub(curdate(),interval 1 month))";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$sth->finish();
$cdate1 = $year_str . "-" . $month_str . "-01";

if ($cmonth eq "0")
{
$sql = "select campaign.campaign_id,campaign_name,date_format(sent_datetime,'%m/%d/%Y') from campaign where sent_datetime >= '$cdate' and status != 'T' and deleted_date is null order by sent_datetime,campaign_name"; 
}
elsif ($cmonth eq "C")
{
$sql = "select campaign.campaign_id,campaign_name,date_format(sent_datetime,'%m/%d/%Y') from campaign where sent_datetime >= curdate() and status != 'T' and deleted_date is null order by sent_datetime,campaign_name"; 
}
elsif ($cmonth eq "Y")
{
$sql = "select campaign.campaign_id,campaign_name,date_format(sent_datetime,'%m/%d/%Y') from campaign where sent_datetime >= date_sub(curdate(),interval 1 day) and sent_datetime < curdate() and status != 'T' and deleted_date is null order by sent_datetime,campaign_name"; 
}
elsif ($cmonth eq "7")
{
$sql = "select campaign.campaign_id,campaign_name,date_format(sent_datetime,'%m/%d/%Y') from campaign where sent_datetime >= date_sub(curdate(),interval 7 day) and sent_datetime < curdate() and status != 'T' and deleted_date is null order by sent_datetime,campaign_name"; 
}
else
{
$sql = "select campaign.campaign_id,campaign_name,date_format(sent_datetime,'%m/%d/%Y') from campaign where sent_datetime >= '$cdate1' and sent_datetime < '$cdate' and status != 'T' and deleted_date is null order by sent_datetime,campaign_name"; 
}
$sth = $dbh->prepare($sql);
$sth->execute();
while (($cid,$cname,$sent_datetime) = $sth->fetchrow_array())
{
	if ($cserver > 0)
	{ 
		$sql = "select id,email_class.class_name,sum(scheduled_cnt),sum(sent_cnt),sum(delivered_cnt),sum(queued_cnt) from server_log,email_class where server_log.campaign_id=$cid and server_log.id=$cserver and server_log.domain_id=email_class.class_id and scheduled_cnt > 0 group by id,email_class.class_name order by id"; 
	}
	else
	{
		$sql = "select id,email_class.class_name,sum(scheduled_cnt),sum(sent_cnt),sum(delivered_cnt),sum(queued_cnt) from server_log,email_class where server_log.campaign_id=$cid and server_log.domain_id=email_class.class_id and scheduled_cnt > 0 group by id,email_class.class_name order by id"; 
	}
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($id,$domain_name,$scheduled_cnt,$sent_cnt,$delivered_cnt,$queued_cnt) = $sth1->fetchrow_array())
	{
		$sql = "select server from server_config where id=$id";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		($sname) = $sth2->fetchrow_array();
		$sth2->finish();
print <<end_of_html;
	<tr><td>$sent_datetime</td><td>$sname</td><td>$cname</td><td>$domain_name</td><td align=middle>$scheduled_cnt</td><td align=middle>$sent_cnt</td><td align=middle>$delivered_cnt</td><td align=middle>$queued_cnt</td></tr>
end_of_html
	}
	$sth1->finish();
		$total_sent_cnt = $total_sent_cnt + $sent_cnt;
		$total_delivered_cnt = $total_delivered_cnt + $delivered_cnt;
}
$sth->finish();

print<<"end_of_html";
<tr><td colspan=7><hr width=100% height=2></td></tr>
<tr><td><b>TOTAL</b></td><td></td><td></td><td align=middle>$sent_cnt_str</td><td align=middle>$opened_cnt_str</td><td align=middle>$delivered_cnt_str</td><td></td><td align=middle>$click_str</td><td align=middle>$uns_cnt_str</td><td align=middle>$bounce_cnt_str</td><td>$fullmbx_cnt_str</td></tr>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
        <TR>
        <TD><IMG height="20" src="$images/spacer.gif" border=0></TD>
		</TR>
        <TD><IMG height="20" src="$images/spacer.gif" border=0></TD>
		</TR>
        <TR>
        <TD>
	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html
$sth->finish();

$util->footer();

$util->clean_up();
exit(0);
