#!/usr/bin/perl
# *****************************************************************************************
# dailydeals_report.cgi
#
# this page displays the Daily Deals report 
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
my $sql;
my $dbh;
my $aid;
my $date_sent;
my $errmsg;
my $campaign_name;
my $list_str;
my $list_name;
my $company;
my $campaign_id;
my $sent_datetime;
my $action;
my $cnt;
my $cid;
my $cname;
my $from_addr;
my $category_name;
my $subject;
my $sendto_str;
my $aol_flag;
my $hotmail_flag;
my $yahoo_flag;
my $other_flag;
my $camp_type;
my $aid;
my $tid;
my $datestr;
my $aname;
my $sent_cnt = 0;
my $opened_cnt = 0;
my $delivered_cnt = 0;
my $opened_percent;
my $unsub_percent;
my $click_percent;
my $bounced_percent;
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
my $payment_per_conversion;
my $total_conversions;
my $grand_total_conversions = 0;
my $grand_total_money = 0;
my $networkopt;
my $company;
my $filename;
my $cmonth=$query->param('cmonth');
if ($cmonth eq "")
{
	$cmonth = 0;
}
my $export=$query->param('export');
if ($export eq "")
{
	$export = 0;
}

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
$user_id = 1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
if ($export ==1)
{
    $filename=$user_id."_trigger_".$cmonth.".csv";
    open(LOG,">/data3/3rdparty/$filename");
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
$networkopt = $cookies{'networkopt'};
if ($networkopt > 0)
{
$sql = "select company from user where user_id=$networkopt"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($company) = $sth->fetchrow_array();
$sth->finish();
}
else
{
	$company="ALL";
}
# print out html page
if ($export == 0)
{
if ($cmonth == 1)
{
    util::header("Trigger Report - Last Month");
}
else
{
    util::header("Trigger Report - Current Month");
}

print <<"end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left>
	<center>
    <TABLE cellSpacing=0 cellPadding=10 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=1100 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><font face="verdana,arial,helvetica,sans serif" 
			color="#509C10" size="3"><b>Campaign Report - $company</b></font></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		</TD>
		</TR>
        <TR>
        <TD><IMG height="20" src="$images/spacer.gif" border=0></TD>
		</TR>
        <tr><td><a href=trigger_report.cgi?cmonth=$cmonth&export=1>Export Report</a></td></tr>
        <TR>
        <TD align=middle><font face="Verdana,Arial,Helvetica,sans-serif" 
			color="#509C10" size="3"><b>Campaigns</b></font></TD>
		</TR>
<!--        <TR>
        <TD><IMG height=7 src="$images/spacer.gif" border=0></TD>
		</TR> -->
        <TR>
        <TD align=middle>
		
            <TABLE cellSpacing=0 cellPadding=0 width=1100 border=1>
            <TBODY>
            <TR bgColor="$table_header_bg">
<!--            <TD vAlign=top align=left><IMG 
                src="$images/blue_tl.gif" border=0 width="7" height="7"></TD> -->
            <TD align=left width=250 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Campaign Name</B> </FONT></TD>
            <TD align=left width=100 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Category</B> </FONT></TD>
            <TD align=left width=100 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Client</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Date</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B># Sent</B> </FONT></TD>
             <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Bounced</B> </FONT></TD> 
             <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Bounce %</B> </FONT></TD> 
             <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Full<br>MBX</B> </FONT></TD> 
             <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Not<br>Delivered</B> </FONT></TD> 
             <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B># Received</B> </FONT></TD> 
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>&nbsp;&nbsp;&nbsp;Opened</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>&nbsp;&nbsp;&nbsp;Open Rate</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Clicks</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Clicks/<br>Opens (%)</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Unsubscribed</B> </FONT></TD>
            <TD align=middle height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Unsub/<br>Received (%)</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Payment Per<br>Conversion</B> </FONT></TD>
            <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Total Conversions</B> </FONT></TD>
                <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Convert %</B> </FONT></TD>
                <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Total \$\$</B> </FONT></TD>
                <TD align=middle height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>eCPM</B> </FONT></TD>
            <!-- <TD vAlign=top align=right height=15><IMG 
                src="$images/blue_tr.gif" border=0 width="7" height="7"></TD> -->
			</TR>
end_of_html
}
# Get number of messages sent, opened, and clicked-throughed
my $month_str;
my $year_str;
my $cdate;
my $cdate1;

$sql = "select month(curdate()),year(curdate())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
$sth->finish();
if ($cmonth == 1)
{
	$sql="select date_sub('$cdate',interval 1 month)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($cdate1) = $sth->fetchrow_array();
	$sth->finish();
} 
else
{
	$cdate1 = $cdate;
	$sql="select date_add('$cdate1',interval 1 month)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($cdate) = $sth->fetchrow_array();
	$sth->finish();
}

if ($networkopt > 0)
{
$sql = "select campaign_trigger_log.campaign_id,campaign_trigger_log.user_id,campaign_trigger_log.date_sent,sum(unsubscribe_cnt),sum(campaign_trigger_log.bounce_cnt),sum(campaign_trigger_log.fullmbx_cnt),format(sum(unsubscribe_cnt),0),format(sum(campaign_trigger_log.bounce_cnt),0),format(sum(campaign_trigger_log.fullmbx_cnt),0),sum(campaign_trigger_log.notdelivered_cnt),format(sum(campaign_trigger_log.notdelivered_cnt),0),sum(campaign_trigger_log.sent_cnt),format(sum(campaign_trigger_log.sent_cnt),0),sum(campaign_trigger_log.open_cnt),format(sum(campaign_trigger_log.open_cnt),0),sum(campaign_trigger_log.click_cnt),format(sum(campaign_trigger_log.click_cnt),0) from campaign_trigger_log,campaign where date_sent >= '$cdate1' and date_sent < '$cdate' and campaign_trigger_log.campaign_id=campaign.campaign_id and campaign_trigger_log.user_id=$networkopt and campaign.status='T' group by campaign_trigger_log.campaign_id,campaign_trigger_log.user_id,date_sent order by date_sent";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$user_id,$date_sent,$uns_cnt,$bounce_cnt,$fullmbx_cnt,$uns_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$notdelivered_cnt,$notdelivered_cnt_str,$sent_cnt,$sent_cnt_str,$opened_cnt,$opened_cnt_str,$click_cnt,$click_str) = $sth->fetchrow_array())
{
	$sql = "select campaign_name,category_name from advertiser_info,category_info,campaign where campaign_id=$cid and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.category_id=category_info.category_id"; 
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($aname,$category_name) = $sth1->fetchrow_array();
	$sth1->finish();

	$delivered_cnt=$sent_cnt-$bounce_cnt-$fullmbx_cnt-$notdelivered_cnt;

    if ($delivered_cnt > 0)
    {
        $opened_percent = ($opened_cnt/$delivered_cnt) * 100;
        $unsub_percent = ($uns_cnt/$delivered_cnt) * 100;
    }
    else
    {
        $opened_percent = 0;
        $unsub_percent = 0;
    }
    if ($sent_cnt > 0)
    {
        $bounced_percent = ($bounce_cnt/$sent_cnt) * 100;
    }
    else
    {
        $bounced_percent = 0;
    }
	if ($opened_cnt > 0)
	{
		$click_percent = ($click_cnt/$opened_cnt) * 100;
	}
	else
	{
		$click_percent = 0;
	}
	if ($export == 0)
	{
print <<end_of_html;
	<tr><td>$aname</td><td>$category_name</td><td>$company</td><td align=middle>$date_sent</td><td align=middle>$sent_cnt_str</td><td align=middle>$bounce_cnt_str</td><td align=middle>
end_of_html
printf "%4.2f\%</font></TD>",$bounced_percent;
print <<end_of_html;
<td>$fullmbx_cnt_str</td><td alig=middle>$notdelivered_cnt_str</td><td align=middle>$delivered_cnt</td><td align=middle>$opened_cnt_str</td><td align=middle>
end_of_html
printf "%4.2f\%</font></TD>",$opened_percent;
print <<end_of_html;
	<td align=middle>$click_str</td><td align=middle>
end_of_html
printf "%4.2f\%</font></TD>",$click_percent;
print <<end_of_html;
<td align=middle>$uns_cnt_str</td>
end_of_html

printf "<td align=middle>%4.2f\%</font></TD>",$unsub_percent;
printf "<td align=middle>\$%4.2f</TD>",$payment_per_conversion;
print "<td align=middle>$total_conversions</td>\n";
my $conversion_percent;
if ($click_cnt > 0)
{
	$conversion_percent = ($total_conversions/$click_cnt) * 100;
}
else
{
	$conversion_percent = 0;
}
printf "<td align=middle>%4.2f\%</font></TD>\n",$conversion_percent;
my $total_money = $payment_per_conversion * $total_conversions;
printf "<td align=middle>\$%6.2f</font></TD>\n",$total_money;
$grand_total_money = $total_money + $grand_total_money;
my $ecpm;
if ($sent_cnt > 0)
{
	$ecpm = $total_money/($sent_cnt/1000);
}
else
{
	$ecpm = 0;
}
printf "<td align=middle>\$%6.2f</font></TD>\n",$ecpm;
print "</tr>\n";
	}
	else
	{
		print LOG "$aname,$category_name,$company,$date_sent,$sent_cnt_str,$bounce_cnt_str,";
		printf LOG "%4.2f\%,",$bounced_percent;
		print LOG "$fullmbx_cnt_str,$notdelivered_cnt_str,$delivered_cnt,$opened_cnt_str,";
		printf LOG "%4.2f\%,",$opened_percent;
		print LOG "$click_str,";
		printf LOG "%4.2f\%,",$click_percent;
		print LOG "$uns_cnt_str,";
		printf LOG "%4.2f\%,",$unsub_percent;
		printf LOG "\$%4.2f,",$payment_per_conversion;
		print LOG "$total_conversions,";
		my $conversion_percent;
		if ($click_cnt > 0)
		{
			$conversion_percent = ($total_conversions/$click_cnt) * 100;
		}
		else
		{
			$conversion_percent = 0;
		}
		printf LOG "%4.2f\%,",$conversion_percent;
		my $total_money = $payment_per_conversion * $total_conversions;
		printf LOG "\$%6.2f,",$total_money;
		$grand_total_money = $total_money + $grand_total_money;
		my $ecpm;
		if ($sent_cnt > 0)
		{
			$ecpm = $total_money/($sent_cnt/1000);
		}
		else
		{
			$ecpm = 0;
		}
		printf LOG "\$%6.2f,\n",$ecpm;
	}
		$total_sent_cnt = $total_sent_cnt + $sent_cnt;
		$total_open_cnt = $total_open_cnt + $opened_cnt;
		$total_delivered_cnt = $total_delivered_cnt + $delivered_cnt;
		$total_bounce_cnt = $total_bounce_cnt + $bounce_cnt;
		$total_fullmbx_cnt = $total_fullmbx_cnt + $fullmbx_cnt;
		$total_aol_cnt = $total_aol_cnt + $aol_cnt;
		$total_uns_cnt = $total_uns_cnt + $uns_cnt;
		$total_notdelivered_cnt = $total_notdelivered_cnt + $notdelivered_cnt;
		$total_click_cnt = $total_click_cnt + $click_cnt;
		$grand_total_conversions = $grand_total_conversions + $total_conversions;
}

$sth->finish();
}
else
{
	my $i=1;
	my $max_client;
	$sql = "select max(user_id) from user where status='A'";	
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($max_client) = $sth->fetchrow_array();
	$sth->finish();
	while ($i <= $max_client)
	{
$sql = "select campaign_trigger_log.campaign_id,campaign_trigger_log.user_id,date_sent,sum(unsubscribe_cnt),sum(campaign_trigger_log.bounce_cnt),sum(campaign_trigger_log.fullmbx_cnt),format(sum(unsubscribe_cnt),0),format(sum(campaign_trigger_log.bounce_cnt),0),format(sum(campaign_trigger_log.fullmbx_cnt),0),sum(campaign_trigger_log.notdelivered_cnt),format(sum(campaign_trigger_log.notdelivered_cnt),0),sum(campaign_trigger_log.sent_cnt),format(sum(campaign_trigger_log.sent_cnt),0),sum(campaign_trigger_log.open_cnt),format(sum(campaign_trigger_log.open_cnt),0),sum(campaign_trigger_log.click_cnt),format(sum(campaign_trigger_log.click_cnt),0) from campaign_trigger_log,campaign where date_sent >= '$cdate1' and date_sent < '$cdate' and campaign_trigger_log.campaign_id=campaign.campaign_id and campaign.status='T' and campaign_trigger_log.user_id=$i group by campaign_trigger_log.campaign_id,campaign_trigger_log.user_id,date_sent order by date_sent";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$user_id,$date_sent,$uns_cnt,$bounce_cnt,$fullmbx_cnt,$uns_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$notdelivered_cnt,$notdelivered_cnt_str,$sent_cnt,$sent_cnt_str,$opened_cnt,$opened_cnt_str,$click_cnt,$click_str) = $sth->fetchrow_array())
{
	$sql = "select campaign_name,category_name from advertiser_info,category_info,campaign where campaign_id=$cid and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.category_id=category_info.category_id"; 
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($aname,$category_name) = $sth1->fetchrow_array();
	$sth1->finish();

	$delivered_cnt=$sent_cnt-$bounce_cnt-$fullmbx_cnt-$notdelivered_cnt;

    if ($delivered_cnt > 0)
    {
        $opened_percent = ($opened_cnt/$delivered_cnt) * 100;
        $unsub_percent = ($uns_cnt/$delivered_cnt) * 100;
    }
    else
    {
        $opened_percent = 0;
        $unsub_percent = 0;
    }
    if ($sent_cnt > 0)
    {
        $bounced_percent = ($bounce_cnt/$sent_cnt) * 100;
    }
    else
    {
        $bounced_percent = 0;
    }
	if ($opened_cnt > 0)
	{
		$click_percent = ($click_cnt/$opened_cnt) * 100;
	}
	else
	{
		$click_percent = 0;
	}
	if ($export == 0)
	{
print <<end_of_html;
	<tr><td>$aname</td><td>$category_name</td><td>$company</td><td align=middle>$sent_cnt_str</td><td align=middle>$bounce_cnt_str</td><td align=middle>
end_of_html
printf "%4.2f\%</font></TD>",$bounced_percent;
print <<end_of_html;
<td>$fullmbx_cnt_str</td><td alig=middle>$notdelivered_cnt_str</td><td align=middle>$delivered_cnt_str</td><td align=middle>$opened_cnt_str</td><td align=middle>
end_of_html
printf "%4.2f\%</font></TD>",$opened_percent;
print <<end_of_html;
	<td align=middle>$click_str</td><td align=middle>
end_of_html
printf "%4.2f\%</font></TD>",$click_percent;
print <<end_of_html;
<td align=middle>$uns_cnt_str</td>
end_of_html

printf "<td align=middle>%4.2f\%</font></TD>",$unsub_percent;
printf "<td align=middle>\$%4.2f</TD>",$payment_per_conversion;
print "<td align=middle>$total_conversions</td>\n";
my $conversion_percent;
if ($click_cnt > 0)
{
	$conversion_percent = ($total_conversions/$click_cnt) * 100;
}
else
{
	$conversion_percent = 0;
}
printf "<td align=middle>%4.2f\%</font></TD>\n",$conversion_percent;
my $total_money = $payment_per_conversion * $total_conversions;
printf "<td align=middle>\$%6.2f</font></TD>\n",$total_money;
$grand_total_money = $total_money + $grand_total_money;
my $ecpm;
if ($sent_cnt > 0)
{
	$ecpm = $total_money/($sent_cnt/1000);
}
else
{
	$ecpm = 0;
}
printf "<td align=middle>\$%6.2f</font></TD>\n",$ecpm;
print "</tr>\n";
	}
	else
	{
		print LOG "$aname,$category_name,$company,$date_sent,$sent_cnt_str,$bounce_cnt_str,";
		printf LOG "%4.2f\%,",$bounced_percent;
		print LOG "$fullmbx_cnt_str,$notdelivered_cnt_str,$delivered_cnt,$opened_cnt_str,";
		printf LOG "%4.2f\%,",$opened_percent;
		print LOG "$click_str,";
		printf LOG "%4.2f\%,",$click_percent;
		print LOG "$uns_cnt_str,";
		printf LOG "%4.2f\%,",$unsub_percent;
		printf LOG "\$%4.2f,",$payment_per_conversion;
		print LOG "$total_conversions,";
		my $conversion_percent;
		if ($click_cnt > 0)
		{
			$conversion_percent = ($total_conversions/$click_cnt) * 100;
		}
		else
		{
			$conversion_percent = 0;
		}
		printf LOG "%4.2f\%,",$conversion_percent;
		my $total_money = $payment_per_conversion * $total_conversions;
		printf LOG "\$%6.2f,",$total_money;
		$grand_total_money = $total_money + $grand_total_money;
		my $ecpm;
		if ($sent_cnt > 0)
		{
			$ecpm = $total_money/($sent_cnt/1000);
		}
		else
		{
			$ecpm = 0;
		}
		printf LOG "\$%6.2f,\n",$ecpm;
	}
		$total_sent_cnt = $total_sent_cnt + $sent_cnt;
		$total_open_cnt = $total_open_cnt + $opened_cnt;
		$total_delivered_cnt = $total_delivered_cnt + $delivered_cnt;
		$total_bounce_cnt = $total_bounce_cnt + $bounce_cnt;
		$total_fullmbx_cnt = $total_fullmbx_cnt + $fullmbx_cnt;
		$total_aol_cnt = $total_aol_cnt + $aol_cnt;
		$total_uns_cnt = $total_uns_cnt + $uns_cnt;
		$total_notdelivered_cnt = $total_notdelivered_cnt + $notdelivered_cnt;
		$total_click_cnt = $total_click_cnt + $click_cnt;
		$grand_total_conversions = $grand_total_conversions + $total_conversions;
}
$sth->finish();
$i++;
}
}
$sql = "select format($total_sent_cnt,0),format($total_open_cnt,0),format($total_bounce_cnt,0),format($total_fullmbx_cnt,0),format($total_uns_cnt,0),format($total_aol_cnt,0),format($total_click_cnt,0),format($total_notdelivered_cnt,0),format($total_delivered_cnt,0)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($sent_cnt_str,$opened_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$uns_cnt_str,$aol_cnt_str,$click_str,$notdelivered_cnt_str,$delivered_cnt_str) = $sth->fetchrow_array();
$sth->finish();

if ($export == 0)
{
print<<"end_of_html";
<tr><td colspan=22><hr width=100% height=2></td></tr>
<tr><td><b>TOTAL</b></td><td></td><td></td><td></td><td align=middle>$sent_cnt_str</td><td align=middle>$bounce_cnt_str</td><td></td><td align=middle>$fullmbx_cnt_str</td><td align=middle>$notdelivered_cnt_str</td><td align=middle>$delivered_cnt_str</td><td align=middle>$opened_cnt_str</td><td></td><td align=middle>$click_str</td><td></td><td align=middle>$uns_cnt_str</td><td></td><td></td><td align=middle>$grand_total_conversions</td><td></td>
end_of_html
printf "<td align=middle>\$%10.2f</td>\n",$grand_total_money;
my $ecpm;
if ($total_sent_cnt > 0)
{
	$ecpm = $grand_total_money/($total_sent_cnt/1000);
}
else
{
	$ecpm = 0;
}
printf "<td align=middle>\$%4.2f</td>\n",$ecpm;
print<<"end_of_html";
</tr>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
        <TR>
        <TD><IMG height="20" src="$images/spacer.gif" border=0></TD>
		</TR>
        <TR>
        <TD align="center">
			<a href="mainmenu.cgi">
			<IMG src="$images/home_blkline.gif" border=0></a></TD>
		</TD>
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
}
else
{
	print LOG "TOTAL,,,,$sent_cnt_str,$bounce_cnt_str,,$fullmbx_cnt_str,$notdelivered_cnt_str,$delivered_cnt_str,$opened_cnt_str,,$click_str,,$uns_cnt_str,,,$grand_total_conversions,";
	printf LOG "\$%10.2f,",$grand_total_money;
	my $ecpm;
	if ($total_sent_cnt > 0)
	{
		$ecpm = $grand_total_money/($total_sent_cnt/1000);
	}
	else
	{
		$ecpm = 0;
	}
	printf LOG "\$%4.2f,",$ecpm;
}
$sth->finish();

if ($export == 0)
{
	$util->footer();
}
else
{
	close(LOG);
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Exported Trigger Report</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font: .75em/1.3em Tahoma, Arial, sans-serif;
	color: #4d4d4d;
  }

h1, h2 {
	font-family: 'Trebuchet MS', Arial, san-serif;
	text-align: center;
	font-weight: normal;
  }

h1 {
	font-size: 2em;
  }

h2 {
	font-size: 1.2em;
  }

h4 {
	font-weight: normal;
	margin: 1em 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

a:link, a:visited {
	color: #33f;
	text-decoration: none;
  }

a:hover, a:focus {
	color: #66f;
	text-decoration: underline;
  }

div.filter {
	text-align: center;
  }

div.filter select {
	font: 11px/14px Tahoma, Arial, sans-serif;
  }

#container {
	width: 90%;
	padding-top: 5%;
	width: expression( document.body.clientWidth < 1025 ? "1024px" : "auto" ); /* set min-width for IE */
	min-width: 1024px;
	margin: 0 auto;
  }

div.overflow {
	/* overflow: auto; */
  }

table {
	background: #FFF;
	border: 1px solid #666;
	width: 780px;
	margin: 0 auto;
	margin-bottom: .5em;
  }

table td {
	padding: .325em;
	border: 1px solid #ABC;
	text-align: center;
  }

table .label {
	font-weight: bold;
	color: #000;
  }

table tr.alt {
	background: #DDD;
  }

table tr.label {
	background: #6C3;
  }

table td.label {
	text-align: left;
	background: #6C3;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }

</style>

</head>

<body>
<center>
<h4><a href="/downloads/$filename">Click here</a> to download file</h4>
</center>
<br>
</body>
</html>
end_of_html
}

$util->clean_up();
exit(0);
