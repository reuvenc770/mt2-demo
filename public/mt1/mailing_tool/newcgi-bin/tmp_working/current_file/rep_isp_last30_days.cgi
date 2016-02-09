#!/usr/bin/perl
# *****************************************************************************************
# rep_isp_current_month.cgi
#
# this page displays the ISP Open/Clicks Last 30 Days Report 
#
# History
# Jim Sobeck, 6/06/05, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $profile_id;
my $sql;
my $dbh;
my $errmsg;
my $campaign_name;
my $list_str;
my $aol_click_cnt;
my $cemail;
my $brand_id;
my $brand_name;
my $aol_open_cnt;
my $yahoo_click_cnt;	
my $yahoo_open_cnt;	
my $others_click_cnt;	
my $others_open_cnt;	
my $hotmail_click_cnt;	
my $hotmail_open_cnt;	
my $caddr;
my $cdomain;
my $list_name;
my $company;
my $campaign_id;
my $sent_datetime;
my $cday;
my $action;
my $company;
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

$util->db_connect();
$dbh = $util->get_dbh;

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
$sth = $dbh->prepare($sql);
$sth->execute();
($company) = $sth->fetchrow_array();
$sth->finish();
}
else
{
    $company="ALL";
}
# print out html page
print "Content-Type: text/html\n\n";
print << "end_of_html";
<!doctype html public "-//w3c//dtd html 4.0 strict//en">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>EMail System</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<h3>Open/Click by ISP Report - Last 30 Days</h3>
<p>
<font face="verdana,arial,helvetica,sans serif" color="#509C10" size="3"><b>Network: $company</b></font><p>
</center>
		<div style="OVERFLOW:auto;HEIGHT:700px">
            <TABLE cellSpacing=0 cellPadding=0 border=1>
				<tr bgColor="$table_header_bg" style="position:relative;top:-1px;">
            <TH align=left width=70px><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Date Sent</B> </FONT></TH>
            <TH align=left><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Day</B> </FONT></TH>
            <TH align=left width=100px><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Client</B> </FONT></TH>
            <TH align=left width=150px><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Profile Name</B> </FONT></TH>
            <TH align=left width=100px><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Domains</B> </FONT></TH>
            <TH align=left width=150px ><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Title</B> </FONT></TH>
            <TH align=middle width=75px><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Total Sent</B> </FONT></TH>
             <TH align=middle width=75px><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Total Clicks</B> </FONT></TH> 
             <TH align=middle width=175px><table><tr><td colspan=3 align=center><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>AOL</font></td></tr><tr><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Opens</font></td><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Clicks</font></td><td width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>% of Total Clicks</font></td></tr></table></TH>
             <TH align=middle width=175px><table><tr><td colspan=3 align=center><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Yahoo</font></td></tr><tr><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Opens</font></td><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Clicks</font></td><td width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>% of Total Clicks</font></td></tr></table></TH>
             <TH align=middle width=175px><table><tr><td colspan=3 align=center><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Others</font></td></tr><tr><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Opens</font></td><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Clicks</font></td><td width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>% of Total Clicks</font></td></tr></table></TH>
             <TH align=middle width=175px><table><tr><td colspan=3 align=center><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Hotmail/MSN</font></td></tr><tr><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Opens</font></td><td width=50px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>Clicks</font></td><td width=75px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1>% of Total Clicks</font></td></tr></table></TH>
			</TR>
end_of_html
# Get number of messages sent, opened, and clicked-throughed
my $month_str;
my $year_str;
my $cdate;

$sql = "select month(curdate()),year(curdate())";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";
    if ($networkopt > 0)
    {
$sql="(select campaign_id,payout,campaign_name,category_name,date_format(sent_datetime,'%m/%d/%Y'),substr(dayname(sent_datetime),1,3),unsubscribe_cnt,bounce_cnt,fullmbx_cnt,format(unsubscribe_cnt,0),format(bounce_cnt,0),format(fullmbx_cnt,0),id,notdelivered_cnt,format(notdelivered_cnt,0),list_profile.aol_flag,list_profile.hotmail_flag,list_profile.yahoo_flag,list_profile.other_flag,campaign.profile_id,sent_datetime,brand_id from campaign,advertiser_info,category_info,list_profile where sent_datetime >= date_sub(curdate(),interval 30 day) and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.category_id=category_info.category_id and campaign.profile_id=list_profile.profile_id and list_profile.client_id=$networkopt) order by sent_datetime";
    }
	else
	{
$sql = "select campaign_id,payout,campaign_name,category_name,date_format(sent_datetime,'%m/%d/%Y'),substr(dayname(sent_datetime),1,3),unsubscribe_cnt,bounce_cnt,fullmbx_cnt,format(unsubscribe_cnt,0),format(bounce_cnt,0),format(fullmbx_cnt,0),id,notdelivered_cnt,format(notdelivered_cnt,0),aol_flag,hotmail_flag,yahoo_flag,other_flag,campaign.profile_id,sent_datetime,brand_id from campaign,advertiser_info,category_info where sent_datetime >= date_sub(curdate(),interval 30 day) and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.category_id=category_info.category_id order by sent_datetime"; 
	}
$sth = $dbh->prepare($sql);
$sth->execute();
open(LOG,">/tmp/log.a");
print LOG "$sql\n";
close LOG;
my $temp_date;
while (($cid,$payment_per_conversion,$cname,$category_name,$sent_datetime,$cday,$uns_cnt,$bounce_cnt,$fullmbx_cnt,$uns_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$tid,$notdelivered_cnt,$notdelivered_cnt_str,$aol_flag,$hotmail_flag,$yahoo_flag,$other_flag,$profile_id,$temp_date,$brand_id) = $sth->fetchrow_array())
{
	$sql = "select brand_name from client_brand_info where brand_id=$brand_id"; 
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($brand_name) = $sth1->fetchrow_array();
	$sth1->finish();
		if ($profile_id == 0)
		{
			$sql = "select company,list_name from campaign_list,list,user where campaign_id=$cid and campaign_list.list_id=list.list_id and list.user_id=user.user_id and list.list_id != 3";
			$sth1 = $dbh->prepare($sql);
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
		}
		else
		{
			$sql = "select company,profile_name from list_profile,user where profile_id=$profile_id and list_profile.client_id=user.user_id";
			$sth1 = $dbh->prepare($sql);
			$sth1->execute();
			($company,$list_str) = $sth1->fetchrow_array();
			$sth1->finish();
		}


		$sql = "select sum(sent_cnt),format(sum(sent_cnt),0),sum(open_cnt),format(sum(open_cnt),0),sum(click_cnt),format(sum(click_cnt),0),sum(aol_click_cnt),sum(aol_open_cnt),sum(yahoo_click_cnt),sum(yahoo_open_cnt),sum(others_click_cnt),sum(others_open_cnt),sum(hotmail_click_cnt),sum(hotmail_open_cnt) from campaign_log where campaign_id=$cid";
		$sth1 = $dbh->prepare($sql);
		$sth1->execute();
		($sent_cnt,$sent_cnt_str,$opened_cnt,$opened_cnt_str,$click_cnt,$click_str,$aol_click_cnt,$aol_open_cnt,$yahoo_click_cnt,$yahoo_open_cnt,$others_click_cnt,$others_open_cnt,$hotmail_click_cnt,$hotmail_open_cnt) = $sth1->fetchrow_array();
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
		}
	my $tr_str;
	$tr_str="";
	if (($cday eq "Sat") or ($cday eq "Sun"))
	{
		$tr_str = "bgColor=lightblue";
	}
        $total_sent_cnt = $total_sent_cnt + $sent_cnt;
        $total_click_cnt = $total_click_cnt + $click_cnt;
print <<end_of_html;
	<tr $tr_str><td align=left><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$sent_datetime</font></td><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$cday</td><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$company - $brand_name</font></td><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$list_str</font></td><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$sendto_str</font></td><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$cname</font></td><td ><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$sent_cnt_str</font></td><td align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$click_str</font></td>
<td><table><tr><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$aol_open_cnt</font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$aol_click_cnt</font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>
end_of_html
if ($click_cnt > 0)
{
	$click_percent = ($aol_click_cnt/$click_cnt) * 100;
	printf "%4.2f%</font></td>",$click_percent;
}
else
{
	printf "0.0%</font></td>";
}
print <<end_of_html;
</tr></table></td><td><table><tr><td width=50px aligN=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$yahoo_open_cnt</font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$yahoo_click_cnt</font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>
end_of_html
if ($click_cnt > 0)
{
$click_percent = ($yahoo_click_cnt/$click_cnt) * 100;
printf "%4.2f%</font></td>",$click_percent;
}
else
{
printf "0.0%</font></td>";
}
print <<end_of_html;
</tr></table></td><td><table><tr><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$others_open_cnt</font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$others_click_cnt</font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>
end_of_html
if ($click_cnt > 0)
{
$click_percent = ($others_click_cnt/$click_cnt) * 100;
printf "%4.2f%</font></td>",$click_percent;
}
else
{
printf "0.0%</font></td>";
}
print <<end_of_html;
</tr></table></td><td><table><tr><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$hotmail_open_cnt</font></td><td width=50px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>$hotmail_click_cnt</font></td><td width=75px align=middle><FONT face=Verdana,Arial,Helvetica,sans-serif size=1>
end_of_html
if ($click_cnt > 0)
{
$click_percent = ($hotmail_click_cnt/$click_cnt) * 100;
printf "%4.2f%</font></td>",$click_percent;
}
else
{
printf "0.0%</font></td>";
}
print "</tr></table></td></tr>\n";
}
$sth->finish();

$sql = "select format($total_sent_cnt,0),format($total_open_cnt,0),format($total_bounce_cnt,0),format($total_fullmbx_cnt,0),format($total_uns_cnt,0),format($total_aol_cnt,0),format($total_click_cnt,0),format($total_notdelivered_cnt,0),format($total_delivered_cnt,0)";
$sth = $dbh->prepare($sql);
$sth->execute();
($sent_cnt_str,$opened_cnt_str,$bounce_cnt_str,$fullmbx_cnt_str,$uns_cnt_str,$aol_cnt_str,$click_str,$notdelivered_cnt_str,$delivered_cnt_str) = $sth->fetchrow_array();
$sth->finish();

print<<"end_of_html";
<tr><td colspan=23><hr width=100% height=2></td></tr>
<tr><td><b>TOTAL</b></td><td></td><td></td><td></td><td></td><td></td><td align=middle>$sent_cnt_str</td><td align=middle>$click_str</td>
</tr>
			</TABLE>
</div>
<center>
			<a href="mainmenu.cgi">
			<IMG src="$images/home_blkline.gif" border=0></a>
</body>
</html>
end_of_html
$sth->finish();

$util->clean_up();
exit(0);
