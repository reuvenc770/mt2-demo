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
$user_id=1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# print out html page

util::header("Server Summary Report");

print <<"end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=center>
	<center>
    <TABLE cellSpacing=0 cellPadding=10 border=0 width="80%">
    <TBODY>
    <TR>
    <TD vAlign=top align=center bgColor=#ffffff colSpan=8>
		</TD>
		</TR>
        <TR>
        <TD align=middle><font face="Verdana,Arial,Helvetica,sans-serif" 
			color="#509C10" size="3"><b>Servers</b></font></TD>
		</TR>
        <TR>
        <TD align=middle>
	<center>	
            <TABLE cellSpacing=0 cellPadding=0 width=900 border=1>
            <TBODY>
            <TR bgColor="$table_header_bg">
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Server</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Clients<br>Affected</B> </FONT></TD>
            <TD align=left height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Brands</br>Mailed</B> </FONT></TD>
            <TD align=left height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Domain</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Scheduled</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Sent</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>% Sent</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Delivered</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B># Initially<br>Queued</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>% Initially<br>Queued</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Deleted</B> </FONT></TD>
			</TR>
end_of_html

	if ($cday == 0)
	{
$sql = "select server,server_log.id,class_name,sum(scheduled_cnt),sum(sent_cnt),sum(delivered_cnt),sum(queued_cnt),sum(deleted_cnt) from server_log,email_class,server_config where server_log.id=server_config.id and server_log.domain_id=email_class.class_id and (scheduled_cnt > 0 or sent_cnt > 0 or deleted_cnt > 0) and log_date >= curdate() group by server,server_log.id,email_class.class_id order by server"; 
	}
	else
	{
$sql = "select server,server_log.id,class_name,sum(scheduled_cnt),sum(sent_cnt),sum(delivered_cnt),sum(queued_cnt),sum(deleted_cnt) from server_log,email_class,server_config where server_log.id=server_config.id and server_log.domain_id=email_class.class_id and log_date >= date_sub(curdate(),interval 1 day) and (scheduled_cnt > 0 or sent_cnt > 0 or deleted_cnt > 0) and log_date < curdate() group by server,server_log.id,email_class.class_id order by server"; 
	}
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	$total_sent = 0;
	$total_queued = 0;
	$total_scheduled = 0;
	$total_deleted_cnt = 0;
	$old_server = "";
	while (($sname,$id,$domain_name,$scheduled_cnt,$sent_cnt,$delivered_cnt,$queued_cnt,$deleted_cnt) = $sth1->fetchrow_array())
	{
		if ($old_server eq "")
		{
			$old_server = $sname;
			$old_id = $id;	
		}
		if ($old_server ne $sname)
		{
			$sql="select distinct brand_name,company from campaign,client_brand_info,user,server_log where campaign.brand_id != 0 and campaign.campaign_id=server_log.campaign_id and server_log.id=$old_id and log_date >= date_sub(curdate(),interval 2 day) and campaign.brand_id=client_brand_info.brand_id and client_brand_info.client_id=user.user_id";
			$sth2 = $dbh->prepare($sql);
			$sth2->execute();
			my $b_str="";
			my $c_str="";
			while (($bname,$cname) = $sth2->fetchrow_array())
			{
				$b_str = $b_str . $bname . "<br>";
				$c_str = $c_str . $cname . "<br>";
			}
			$sth2->finish();
			$_ = $b_str;
			$b_str = $_;
			$_ = $c_str;
			$c_str = $_;
			$sent_percent=0;
			if ($total_scheduled > 0)
			{
				$sent_percent = ($total_sent/$total_scheduled)*100;
			}
			if ($total_sent > 0)
			{
				$q_percent = ($total_queued/$total_sent)*100;
			}
			else
			{
				$q_percent=0;
			}
			print "<tr><td><b>$old_server total</b></td><td><b>$c_str</b></td><td><b>$b_str</b></td><td><b>ALL</b></td><td align=middle><b>$total_scheduled</b></td><td align=middle><b>$total_sent</b></td>";
			printf "<td align=middle></b>%3.0f \%</b></td>",$sent_percent;
			print "<td align=middle></td><td align=middle><b>$total_queued</b></td>\n";
			printf "<td align=middle><b>%3.0f \%</b></td>",$q_percent;
			print "<td align=middle><b>$total_deleted_cnt</b></td></tr>\n";
			$old_server=$sname;
			$old_id=$id;
			$total_sent = 0;
			$total_queued = 0;
			$total_scheduled = 0;
			$total_deleted_cnt = 0;
		}
		$total_sent = $total_sent + $sent_cnt;
		$total_deleted_cnt = $total_deleted_cnt + $deleted_cnt;
		$total_queued = $total_queued + $queued_cnt;
		$total_scheduled = $total_scheduled + $scheduled_cnt;
		$sent_percent = 0;
		if ($scheduled_cnt > 0)
		{
			$sent_percent = ($sent_cnt/$scheduled_cnt)*100;
		}
		if ($sent_cnt > 0)
		{
			$q_percent = ($queued_cnt/$sent_cnt)*100;
		}
		else
		{
			$q_percent=0;
		}
		print "<tr><td>$sname</td><td></td><td></td><td>$domain_name</td><td align=middle>$scheduled_cnt</td><td align=middle>$sent_cnt</td>";
		printf "<td align=middle>%3.0f \%</td>",$sent_percent;
		print "<td align=middle>$delivered_cnt</td><td align=middle>$queued_cnt</td>\n";
		printf "<td align=middle>%3.0f \%</td>",$q_percent;
		print "<td align=middle>$deleted_cnt</td></tr>\n";
	}
	$sth1->finish();
	$sql="select distinct brand_name,company from campaign,client_brand_info,user,server_log where campaign.brand_id != 0 and campaign.campaign_id =server_log.campaign_id and server_log.id=$old_id and log_date >= date_sub(curdate(),interval 2 day) and campaign.brand_id=client_brand_info.brand_id and client_brand_info.client_id=user.user_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	my $b_str="";
	my $c_str="";
	while (($bname,$cname) = $sth1->fetchrow_array())
	{
		$b_str = $b_str . $bname . "<br>";
		$c_str = $c_str . $cname . "<br>";
	}
	$sth1->finish();
			$sent_percent = ($total_sent/$total_scheduled)*100;
			if ($total_sent > 0)
			{
				$q_percent = ($total_queued/$total_sent)*100;
			}
			else
			{
				$q_percent=0;
			}
			print "<tr><td><b>$old_server total</b></td><td><b>$c_str</b></td><td><b>$b_str</b></td><td><b>ALL</b></td><td align=middle><b>$total_scheduled</b></td><td align=middle><b>$total_sent</b></td>";
			printf "<td align=middle></b>%3.0f \%</b></td>",$sent_percent;
			print "<td align=middle></td><td align=middle><b>$total_queued</b></td>\n";
			printf "<td align=middle><b>%3.0f \%</b></td>",$q_percent;
			print "<td align=middle><b>$total_deleted_cnt</b></td></tr>\n";

print<<"end_of_html";
			</TBODY>
			</TABLE>

		</TD>
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

$util->footer();

$util->clean_up();
exit(0);
