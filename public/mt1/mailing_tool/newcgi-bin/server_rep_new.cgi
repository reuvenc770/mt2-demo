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

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

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
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
$sql = "select month(date_sub(curdate(),interval 1 month)),year(date_sub(curdate(),interval 1 month))";
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$sth->finish();
$cdate1 = $year_str . "-" . $month_str . "-01";

my $date_clause="";
if ($cmonth eq "0")
{
	$date_clause=qq|c.sent_datetime>='cdate'|;
}
elsif ($cmonth eq "C")
{
	$date_clause=qq|c.sent_datetime>=CURDATE()|;
}
elsif ($cmonth eq "Y")
{
	$date_clause=qq|c.sent_datetime>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND c.sent_datetime<CURDATE()|;
}
elsif ($cmonth eq "7")
{
	$date_clause=qq|c.sent_datetime>=DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND sent_datetime<CURDATE()|;
}
else
{
	$date_clause=qq|c.sent_datetime>='$cdate1' AND c.sent_datetime<'$cdate'|;
}
my $server_clause="";
if ($cserver) {
	$server_clause=qq^AND sl.id='$cserver'^;
}


my $quer=qq|SELECT sl.id, c.campaign_id,campaign_name AS title ,scheduled_cnt, sl.sent_cnt, class_name, domain_id,client_id,|
		.qq|advertiser_id,server, delivered_cnt, queued_cnt FROM server_config sc, campaign c, campaign_log cl, server_log sl, |
		.qq|list_profile p, email_class e WHERE c.campaign_id=sl.campaign_id AND c.campaign_id=cl.campaign_id AND |
		.qq|cl.campaign_id=sl.campaign_id AND c.profile_id=p.profile_id e.class_id=sl.domain_id AND $date_clause AND |
		.qq|scheduled_cnt>0 $server_clause ORDER BY sent_datetime ASC|;

$sth = $dbhq->prepare($quer);
$sth->execute();
while (my $hrInfo=$sth->fetchrow_hashref) {

print <<end_of_html;
	<tr><td>$sent_datetime</td><td>$sname</td><td>$cname</td><td>$domain_name</td><td align=middle>$scheduled_cnt</td><td align=middle>$sent_cnt</td><td align=middle>$delivered_cnt</td><td align=middle>$queued_cnt</td></tr>
end_of_html
	}
	$sth1->finish();
}
$sth->finish();

print<<"end_of_html";
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
