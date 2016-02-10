#!/usr/bin/perl
# *****************************************************************************************
# powermail_report.cgi
#
# this page displays the powermail report 
#
# History
# Jim Sobeck, 8/06/01, Creation
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
my $errmsg;
my $campaign_name;
my $list_str;
my $list_name;
my $company;
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

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
$user_id = 1;
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

util::header("Campaign Report - Current Month");

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
            <TD align=left width=100 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Date Sent</B> </FONT></TD>
            <TD align=left width=100 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Client</B> </FONT></TD>
            <TD align=left width=100 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Sublist</B> </FONT></TD>
            <TD align=left height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Domains</B> </FONT></TD>
            <TD align=left width=230 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Title</B> </FONT></TD>
            <TD align=left width=230 height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Category</B></FONT></TD>
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
# Get number of messages sent, opened, and clicked-throughed
my $month_str;
my $year_str;
my $cdate;

$sql = "select month(curdate()),year(curdate())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
    if ($networkopt > 0)
    {
$sql = "select campaign_id,payout,campaign_name,category_name,date_format(sent_datetime,'%m/%d/%Y'),unsubscribe_cnt,bounce_cnt,fullmbx_cnt,format(unsubscribe_cnt,0),format(bounce_cnt,0),format(fullmbx_cnt,0),id,notdelivered_cnt,format(notdelivered_cnt,0),aol_flag,hotmail_flag,yahoo_flag,other_flag from campaign,advertiser_info,category_info where sent_datetime >= date_sub(curdate(),interval 6 month) and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.category_id=category_info.category_id and campaign.campaign_id in (select distinct campaign_list.campaign_id from campaign_list,list where campaign_list.list_id = list.list_id and list.user_id=$networkopt) order by sent_datetime";
    }
	else
	{
$sql = "select campaign_id,payout,campaign_name,category_name,date_format(sent_datetime,'%m/%d/%Y'),unsubscribe_cnt,bounce_cnt,fullmbx_cnt,format(unsubscribe_cnt,0),format(bounce_cnt,0),format(fullmbx_cnt,0),id,notdelivered_cnt,format(notdelivered_cnt,0),aol_flag,hotmail_flag,yahoo_flag,other_flag from campaign,advertiser_info,category_info where sent_datetime >= date_sub(curdate(),interval 6 month) and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.category_id=category_info.category_id order by sent_datetime";
	}
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$payment_per_conversion,$cname,$category_name,$sent_datetime,$uns_cnt,$bounce_cnt,$fullmbx_cnt,$uns_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$tid,$notdelivered_cnt,$notdelivered_cnt_str,$aol_flag,$hotmail_flag,$yahoo_flag,$other_flag) = $sth->fetchrow_array())
{
		$sql = "select company,list_name from campaign_list,list,user where campaign_id=$cid and campaign_list.list_id=list.list_id and list.user_id=user.user_id and list.list_id != 3";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		$list_str = "";
		$company="";
		my $temp_company;
		while (($temp_company,$list_name) = $sth1->fetchrow_array())
		{
			if ($company eq "")
			{
				$company = $temp_company;
			}
			$list_str = $list_str . $list_name . " ";
		}
		$_ = $list_str;
		chop;
		$list_str = $_;
		$sth1->finish();

		$sql = "select sum(sent_cnt),format(sum(sent_cnt),0),sum(open_cnt),format(sum(open_cnt),0),sum(click_cnt),format(sum(click_cnt),0),sum(sent_cnt)-$bounce_cnt-$fullmbx_cnt-$notdelivered_cnt,format(sum(sent_cnt)-$bounce_cnt-$fullmbx_cnt-$notdelivered_cnt,0) from campaign_log where campaign_id=$cid";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($sent_cnt,$sent_cnt_str,$opened_cnt,$opened_cnt_str,$click_cnt,$click_str,$delivered_cnt,$delivered_cnt_str) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($sent_cnt > 100)
		{
        $sendto_str = "";
        if ($aol_flag eq "Y")
        {
            $sendto_str = "AOL";
        }
        if ($hotmail_flag eq "Y")
        {
            $sendto_str = $sendto_str . " Hotmail";
        }
        if ($yahoo_flag eq "Y")
        {
            $sendto_str = $sendto_str . " Yahoo";
        }
        if ($other_flag eq "Y")
        {
            $sendto_str = $sendto_str . " Other";
        }
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
print <<end_of_html;
	<tr><td>$sent_datetime</td><td>$company</td><td>$list_str</td><td>$sendto_str</td><td>$cname</td><td>$category_name</td><td align=middle>$sent_cnt_str</td><td align=middle>$bounce_cnt_str</td><td align=middle>
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
}
$sth->finish();

$sql = "select format($total_sent_cnt,0),format($total_open_cnt,0),format($total_bounce_cnt,0),format($total_fullmbx_cnt,0),format($total_uns_cnt,0),format($total_aol_cnt,0),format($total_click_cnt,0),format($total_notdelivered_cnt,0),format($total_delivered_cnt,0)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($sent_cnt_str,$opened_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$uns_cnt_str,$aol_cnt_str,$click_str,$notdelivered_cnt_str,$delivered_cnt_str) = $sth->fetchrow_array();
$sth->finish();


print<<"end_of_html";
<tr><td colspan=22><hr width=100% height=2></td></tr>
<tr><td><b>TOTAL</b></td><td></td><td></td><td></td><td></td><td></td><td align=middle>$sent_cnt_str</td><td align=middle>$bounce_cnt_str</td><td></td><td align=middle>$fullmbx_cnt_str</td><td align=middle>$notdelivered_cnt_str</td><td align=middle>$delivered_cnt_str</td><td align=middle>$opened_cnt_str</td><td></td><td align=middle>$click_str</td><td></td><td align=middle>$uns_cnt_str</td><td></td><td></td><td align=middle>$grand_total_conversions</td><td></td>
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
$sth->finish();

$util->footer();

$util->clean_up();
exit(0);
