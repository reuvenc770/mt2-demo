#!/usr/bin/perl
# *****************************************************************************************
# server_rep1.cgi
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
my $deleted_cnt = 0;
my $scheduled_cnt = 0;
my $queued_cnt = 0;
my $domain_name;
my $id;
my $old_id;
my $sname;
my ($ip,$email_addr,$cname,$result_msg);
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
my $total_deleted_cnt = 0;
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
my $sent_percent;
my $q_percent;
my $total_sent;
my $total_queued;
my $total_scheduled;
my $old_server;
my $bname;
my $cname;
my $cday=$query->param('cday');

if ($cday eq "")
{
	$cday=0;
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

# print out html page

util::header("Test Email Report");

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
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Server</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>IP Addr</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Email Addr</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Campaign</B> </FONT></TD>
            <TD align=left height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Result</B> </FONT></TD>
			</TR>
end_of_html

	if ($cday == 0)
	{
		$sql = "select hostname,ip_addr,email_addr,campaign_name,result_msg from test_emails_log,campaign where test_emails_log.campaign_id=campaign.campaign_id and test_date >= curdate() order by test_id"; 
	}
	else
	{
		$sql = "select hostname,ip_addr,email_addr,campaign_name,result_msg from test_emails_log,campaign where test_emails_log.campaign_id=campaign.campaign_id and test_date >= date_sub(curdate(),interval 1 day) and test_date < curdate() order by test_id"; 
	}
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($sname,$ip,$email_addr,$cname,$result_msg) = $sth1->fetchrow_array())
	{
		print "<tr><td>$sname</td><td>$ip</td><td>$email_addr</td><td>$cname</td><td>$result_msg</td></tr>\n"
	}
	$sth1->finish();
print<<"end_of_html";
			</TBODY>
			</TABLE>
</TD>
</TR>
</tbody>
</table>
</body>
</html>
end_of_html
$sth->finish();


$util->clean_up();
exit(0);
