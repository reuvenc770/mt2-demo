#!/usr/bin/perl

# *****************************************************************************************
# mainmenu.cgi
#
# History
# Grady Nash, 7/30/01, Creation
# Mike Baker, 7/31/01  Added code to Display LISTs
# Jim Sobeck, 01/28/03 Added logic for computing Revenue
# Jim Sobeck, 02/08/05 Added Creative List Link
# *****************************************************************************************

#------------- include Perl Modules ------------------
use strict;
use CGI;
use util;

#------- get some objects to use later ---------------
my $EDEALSDIRECT_LIST = 48;
my $DUMMY_LIST = 82;
my $IMEDIA_LIST1 = 52;
my $IMEDIA_LIST2 = 53;
my $util = util->new;
my $query = CGI->new;
my $count;
my $pname;
my $brand_id;
my $brand_name;
my $client_id;
my $reccnt1;
my $temp_cnt;
my $sth;
my $sth1;
my $sth1a;
my $BASE_DIR;
my $list_cnt;
my $camp_cnt;
my $deal_cnt;
my $aol_deal_cnt;
my $t_deal_cnt;
my $t_aol_deal_cnt;
my $sql;
my $dbh;
my $tid;
my $size;
my $temp_name;
my ($campaign_id, $campaign_name, $status, $sent_datetime, $scheduled_date,$clast60,$last60_flag,$aol_flag,$copen,$open_flag,$hotmail_flag,$yahoo_flag,$other_flag,$supp_name,$last_updated,$sid,$aid,$day_cnt,$filedate);
my $profile_id;
my $company_name;
my $disable_flag;
my ($fm_campaign_id, $fm_campaign_name);
#my $filteropt = $query->param('filteropt');
my $filteropt;
my $networkopt;
my $cstring;
my $filteropt_sel_a = "";
my $filteropt_sel_s = "";
my $filteropt_sel_d = "";
my $filteropt_sel_w = "";
my $filteropt_sel_t = "";
my $filteropt_sel_aol = "";
my $filteropt_sel_hotmail = "";
my $filteropt_sel_sentzero = "";
my $filteropt_sel_3_7 = "";
my $filteropt_sel_3_30 = "";
my $filteropt_sel_approved = "";
my $filteropt_sel_c = "";
my $filteropt_sel_l = "";
my $filteropt_sel_7 = "";
my $filteropt_sel_3 = "";
my $filteropt_sel_30 = "";
my $status_name;
my $sendto_str;
my $date_str;
my ($username, $user_type);
my $first_name;
my $last_name;
my $light_table_bg = $util->get_light_table_bg;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $ads_url;
my $network_str;
my $images = $util->get_images_url;

#------ connect to the util database -------------------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
	print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# look for cookie that sets this users campaign filter option

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
$filteropt = $cookies{'filteropt'};
$networkopt = $cookies{'networkopt'};
$cstring = $cookies{'cstring'};

if ($filteropt eq "A")
{
	$filteropt_sel_a = "selected";
}
elsif ($filteropt eq "L") 
{
	$filteropt_sel_l = "selected";
}
elsif ($filteropt eq "7") 
{
	$filteropt_sel_7 = "selected";
}
elsif (($filteropt eq "E") || ($filteropt eq ""))
{
	$filteropt_sel_3 = "selected";
}
elsif ($filteropt eq "3") 
{
	$filteropt_sel_30 = "selected";
}
elsif ($filteropt eq "D")
{
	$filteropt_sel_d = "selected";
}
elsif ($filteropt eq "O")
{
	$filteropt_sel_aol = "selected";
}
elsif ($filteropt eq "H")
{
	$filteropt_sel_hotmail = "selected";
}
elsif ($filteropt eq "Z")
{
	$filteropt_sel_sentzero= "selected";
}
elsif ($filteropt eq "3_7")
{
	$filteropt_sel_3_7 = "selected";
}
elsif ($filteropt eq "3_30")
{
	$filteropt_sel_3_30 = "selected";
}
elsif ($filteropt eq "W")
{
	$filteropt_sel_w = "selected";
}
elsif ($filteropt eq "T")
{
	$filteropt_sel_t = "selected";
}
elsif ($filteropt eq "P")
{
	$filteropt_sel_approved = "selected";
}
elsif ($filteropt eq "S")
{
	$filteropt_sel_s = "selected";
}
elsif ($filteropt eq "C")
{
	$filteropt_sel_c = "selected";
}

$sql = "select parmval from sysparm where parmkey = 'ADS_URL'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($ads_url) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------------
# Get User_Type, UserName from user 
#--------------------------------------

$sql = "select user_type, username, first_name, last_name from user where user_id = $user_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($user_type, $username, $first_name, $last_name) = $sth->fetchrow_array();
$sth->finish();

util::header("Welcome Back $first_name");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#ffffff>
	<TABLE cellSpacing=0 cellPadding=0 bgColor=#999999 border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
end_of_html
	if ( $user_type ne "R" )
	{
print<<"end_of_html";

		<TABLE cellSpacing=0 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top align=middle width=213>
		<IMG height=7 src="$images/spacer.gif" width=213>

			<TABLE cellSpacing=0 cellPadding=0 width=198 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
				src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
			<TD vAlign=bottom width="100%" height=17><B><FONT 
				face=Arial color=#000000 size=2>Create Email Campaigns</FONT></B> </TD>
			<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
				src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="show_campaign.cgi?mode=A">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Build A New Campaign</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="show_campaign.cgi?mode=A&daily_flag=Y">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Build A New Daily </FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="3rdparty_deploy_main.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Build A New 3rd Party Campaign</FONT></a></TD>
			</TR>
			<TR> <TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;<a href="dosmonos_deploy_main.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Build A New Dos Monos Campaign</FONT></a></TD>
			</TR>
			<TR>
			<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
			<TD vAlign=bottom align=right width=9 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
end_of_html
if ($user_type eq "A")
{
print<<"end_of_html";
			<IMG height=7 src="$images/spacer.gif" width=213>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
    		<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT 
    			face=Arial color=#000000 size=2>Manage Email Lists</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="find_info_id.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
    			Get Info for Email Id</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="find_info.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
    			Get Info for Email Addr</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="complaint_list.cgi"> <FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2> View Complaint History</FONT></a></TD>
			</TR>
			<TR><TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;<a href="listprofile_add.cgi"><FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Add List Profile</FONT></a></TD></TR>
			<TR><TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;<a href="listprofile_list.cgi"><FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Update List Profile</FONT></a></TD></TR>
			<TR><TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;<a href="listprofile_list.cgi?tflag=Y"><FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Update 3rd Party Profiles</FONT></a></TD></TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="list_menu.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
    			List Management</FONT></a></TD>
			</TR>
			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
end_of_html
}
}
print<<"end_of_html";
			<IMG height=7 src="$images/spacer.gif" width=213>
			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Advertisers/Clients</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD></TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="creative_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Add/Update Creative</FONT></a></TD>
			</TR>
end_of_html
if ($user_type eq "A")
{
print<<"end_of_html";
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="advertiser_disp.cgi?pmode=A">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Add a New Advertiser</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="advertiser_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Update All Advertisers</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/adv_search.html">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Update Select Advertisers</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="adv_copy.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Copy Advertiser</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="client_disp.cgi?pmode=A">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Add a New Client</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="client_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Update Clients</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/client_schedule.html">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Clients Schedule</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/cgi-bin/3rdparty_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>3rd Party Mailers</FONT></a></TD>
			</TR>
end_of_html
}
print<<"end_of_html";
			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
            <IMG height=7 src="$images/spacer.gif" width=213>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Reports</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD></TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="advertiser_report.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Advertiser - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="advertiser_report_lastmonth.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Advertiser - Last Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="dailydeals_report.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Daily Deals</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="trigger_report.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Trigger Deals</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_current_month.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Campaigns - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_last30_days.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Campaigns - Last 30 Days</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_aol_current_month.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				AOL - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_aol_last30_days.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				AOL - Last 30 Days</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_hotmail.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Hotmail - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_hotmail.cgi?cdays=30">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Hotmail - Last 30 Days</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/3rdparty_rep.html">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				3rdParty Campaigns </FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_isp_current_month.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				ISP Open/Clicks - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_isp_last30_days.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				ISP Open/Clicks - Last 30</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_record_proc.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Record Processing - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_record_proc_last.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Record Processing - Last Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_open_log.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Open - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="rep_open_log_last.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Open - Last Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/schedule_frame.html">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Scheduled Campaigns</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/server_rep.html">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Server Report</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/server_rep1.html">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Server Summary Report</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/rep_test_email.html">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Test Email Report</FONT></a></TD>
			</TR>

			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
end_of_html
if ($user_type ne "R")
{
print<<"end_of_html";

			<IMG height=7 src="$images/spacer.gif" width=213>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Manage Suppress Lists</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD></TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="supplist_add.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Add a New Suppression List</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="supplist_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Add To Suppression List</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="domain_supplist_add.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Add a New Domain List</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="domain_supplist_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Add To Domain List</FONT></a></TD>
			</TR>

			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=213>
end_of_html
if ($user_type eq "A")
{
print<<"end_of_html";


			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Campaign Categories</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD></TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="list_category.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Add/Edit Categories</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="new_list_category.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				New Categories/Brand</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/cgi-bin/category_trigger_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Category Triggers</FONT></a></TD>
			</TR>
			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=213>

<!--			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Medical Conditions</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD></TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="list_medical.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Add/Edit Medical Conditions</FONT></a></TD>
			</TR>
			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=213> -->
			
			<!-- Additional Site Features menu box -->
			
			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$light_table_bg border=0>
			<TBODY>
			<TR>
    			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    				src="$images/lt_purp_tl.gif" border=0 width="8" height="8"></font></TD>
    			<TD vAlign=bottom width="100%" height=17><B><FONT 
    				face=Arial color=#000000 size=2>Additional Site Features</FONT></B> </TD>
    			<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    				src="$images/lt_purp_tr.gif" border=0 width="8" height="8"></font></TD>
			</TR>
end_of_html
}
}	
	#---------------------------------------------------------------------
	# Only Display these links if the user is an ADMIN user
	#---------------------------------------------------------------------
	if ( $user_type eq "A" )
	{
		print << "end_of_html";
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="footer_list.cgi"><FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Footer Variations</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="footer_content_list.cgi"><FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Footer Content</FONT></a></TD>
			</TR>
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="sysparm_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Edit System Parameters</FONT></a></TD>
			</TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="list_refurl.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Setup Redirects</FONT></a></TD>
			</TR>
end_of_html
	}
if ($user_type ne "R")
{
	print << "end_of_html" ;
			<!-- <TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial"><IMG 
				height=7 src="$images/lt_purp_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/lt_purp_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=213>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$light_table_bg border=0>
			<TBODY>
			<TR>
    		<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/lt_purp_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Ad Banners</FONT></B> </TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/lt_purp_tr.gif" border=0 width="8" height="8"></font></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp; 
				<a href="${ads_url}manage.cgi?client_id=$user_id"> 
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Banner Ads</font></a></TD>
			</TR> -->
	<!--	
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp; 
				<a href="">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Pop Up Ads</font></a></TD>
			</TR>
	-->
			<!-- <TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7>
				<IMG height=7 src="$images/lt_purp_bl.gif" width=8 border=0></TD>
    		<TD vAlign=bottom align=right width=9 height=7>
				<IMG height=7 src="$images/lt_purp_br.gif" width=8 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=213>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$light_table_bg border=0>
			<TBODY>
			<TR>
    		<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/lt_purp_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Acquire Subscribers</FONT></B> </TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/lt_purp_tr.gif" border=0 width="8" height="8"></font></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp; 
				<a href="client_signup_edit.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Design A Form</font></a></TD>
			</TR>
			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7>
				<IMG height=7 src="$images/lt_purp_bl.gif" width=8 border=0></TD>
    		<TD vAlign=bottom align=right width=9 height=7>
				<IMG height=7 src="$images/lt_purp_br.gif" width=8 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=213> -->
			
			
			<!-- Your Account menu box -->

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$light_table_bg border=0>
			<TBODY>
			<TR>
    		<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/lt_purp_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Your Account </FONT></B> </TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/lt_purp_tr.gif" border=0 width="8" height="8"></font></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp; 
				<a href="client_disp.cgi?pmode=U&puserid=$user_id">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Edit Contact Information</font></a></TD>
			</TR>
			<TR>
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7>
				<IMG height=7 src="$images/lt_purp_bl.gif" width=8 border=0></TD>
    		<TD vAlign=bottom align=right width=9 height=7>
				<IMG height=7 src="$images/lt_purp_br.gif" width=8 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>
end_of_html
}
print<<"end_of_html";

			<!-- End Left nav bar -->

		</TD>
		<TD vAlign=top align=middle width=453>
		<IMG height=7 src="$images/spacer.gif" width=453>
end_of_html
#-------------------------------------------------------------------------------
# Begin code to Display the Users LISTS
#-------------------------------------------------------------------------------

my ($sth2, $nbr_subscribers,$total_aol,$total_hotmail,$total_yahoo,$total_foreign,$total_other) ;
my 	($reccnt, $bgcolor, $list_id, $list_name, $status, $nbr_list_members,$aol_cnt,$nonaol_cnt, $hotmail_cnt, $msn_cnt,$yahoo_cnt,$foreign_cnt) ;
my $t_reccnt;
$nbr_subscribers = 0;
$total_aol = 0;
$total_hotmail = 0;
$total_yahoo = 0;
$total_foreign = 0;
$total_other = 0;

#---------------------
# Begin HTML Prints
#---------------------
#-------------------------------------------------------------------------------
# End code to Display Users LISTS
#-------------------------------------------------------------------------------

print << "end_of_html";
<IMG height=7 src="$images/spacer.gif" width=453>
<TABLE cellSpacing=0 cellPadding=0 width=800 border=0>
<tr><td>
<center>
</td></tr>
<tr><td colspan=7 align=middle><a href="/weekly.html">Weekly Schedule</a>&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/schedule_copy.cgi">Copy Schedule</a>&nbsp;&nbsp;<a href="/cgi-bin/view_schedule.cgi">View/Delete Schedule</a></td></tr>
<tr><td colspan=7 align=middle><a href="/weekly_3rd.html">3rd Party Weekly Schedule</a>&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/3rdparty_schedule_copy.cgi">3rd Party Copy Schedule</a>&nbsp;&nbsp;<a href="/cgi-bin/3rdparty_view_schedule.cgi">View/Delete 3rdparty Schedule</a></td></tr>
<tr><td colspan=7 align=middle><a href="/weekly_aol.html">Weekly AOL Schedule</a>&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/schedule_copy.cgi?stype=A">Copy Schedule</a>&nbsp;&nbsp;<a href="/cgi-bin/view_schedule.cgi?stype=A">View/Delete Schedule</a></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>
<form method="post" action="mainmenu_cook.cgi">
<font face=Arial size=2 color="#509C10"><b>Campaigns: </b>
<select name="filteropt">
<option value="A" $filteropt_sel_a>All Campaigns</option>
<option value="E" $filteropt_sel_3>Last 3 Days</option>
<option value="7" $filteropt_sel_7>Last 7 Days</option>
<option value="L" $filteropt_sel_l>Last 14 Days</option>
<option value="3" $filteropt_sel_30>Last 30 Days</option>
<option value="P" $filteropt_sel_approved>Approved Campaigns</option>
<option value="O" $filteropt_sel_aol>AOL Campaigns - Last 7</option>
<option value="H" $filteropt_sel_hotmail>Hotmail - Last 7</option>
<option value="W" $filteropt_sel_w>Daily Campaigns</option>
<option value="T" $filteropt_sel_t>Trigger Campaigns</option>
<option value="D" $filteropt_sel_d>Draft Campaigns</option>
<option value="S" $filteropt_sel_s>Scheduled Campaigns</option>
<option value="C" $filteropt_sel_c>Completed Campaigns</option>
<option value="Z" $filteropt_sel_sentzero>0 sent - Last 7</option>
<option value="3_7" $filteropt_sel_3_7>3rd Party - Last 7</option>
<option value="3_30" $filteropt_sel_3_30>3rd Party - Last 30</option>
</select>&nbsp;&nbsp;
Network: <select name="networkopt"><option value=0>ALL</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $uid;
my $company_name;
while (($uid,$company_name) = $sth->fetchrow_array())
{
	if ($uid == $networkopt)
	{
		print "<option value=$uid selected>$company_name</option>\n";
	}
	else
	{
		print "<option value=$uid>$company_name</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>
<b>Search</b>: <input type=text size=15 maxlength=30 name=cstr value="$cstring">&nbsp;&nbsp;<input type="submit" value="Refresh"></font>
</form>
</td></tr></table>
<TABLE cellSpacing=0 cellPadding=0 width=745 bgColor=$alt_light_table_bg border=0>
<TBODY>
<TR bgColor=$table_header_bg>
<TD vAlign=top align=left><font face="Arial"><IMG src="$images/blue_tl.gif" 
	border=0 width="7" height="7"></font></TD>
<TD align=left width=150 height=15><b><font color="white" size="1" 
	face="Arial">Email Campaigns: Your Lists</font></b></TD>
<!-- <TD align=middle width=70 height=15><b><font color="white" size="1" 
	face="Arial">ID</font></b></TD> -->
<TD align=left width=70 height=15><b><font color="white" size="1" 
	face="Arial">Servers</font></b></TD>
<TD align=left width=90 height=15><b><font color="white" size="1" 
	face="Arial">Scheduled/<br>Sent</font></b></TD>
<TD align=left width=120 height=15><B><FONT face=Arial color=white 
	size=1>Status </FONT></B> </TD>
<TD align=left width=80 height=15><B><FONT face=Arial color=white 
	size=1>Send To</FONT></B> </TD>
<TD align=left width=90 height=15><B><FONT face=Arial color=white 
	size=1>Suppression List</FONT></B> </TD>
<TD align=left width=100 height=15><B><FONT face=Arial color=white 
	size=1>Functions </FONT></B> </TD>
<TD vAlign=top align=right height=15><font face="Arial"><IMG 
	src="$images/blue_tr.gif" border=0 width="7" height="7"></font></TD>
</TR>
end_of_html

	#-----------------------------------------------
	# Loop - Get ALL Campaigns that belong to the User
	#-----------------------------------------------

	if (($filteropt ne "W") && ($filteropt ne "T"))
	{
		$sql = "select campaign.campaign_id, campaign.advertiser_id, campaign_name, campaign.status,concat(month(sent_datetime),'-',dayofmonth(sent_datetime),'-',year(sent_datetime)),date_format(scheduled_datetime,'%m-%d-%y %h:%i %p'),last60_flag,aol_flag,open_flag,id,campaign.list_cnt,hotmail_flag,yahoo_flag,other_flag,list_name,last_updated,filedate,vendor_supp_list_id,disable_flag,datediff(curdate(),last_updated),campaign.profile_id,campaign.brand_id from campaign,vendor_supp_list_info,advertiser_info where campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id and deleted_date is null ";
	}
	elsif ($filteropt eq "T")
	{
		$sql = "select campaign.campaign_id, campaign.advertiser_id, campaign_name, campaign.status,concat(month(sent_datetime),'-',dayofmonth(sent_datetime),'-',year(sent_datetime)),date_format(scheduled_datetime,'%m-%d-%y %h:%i %p'),last60_flag,aol_flag,open_flag,id,campaign.list_cnt,hotmail_flag,yahoo_flag,other_flag,list_name,last_updated,filedate,vendor_supp_list_id,disable_flag,datediff(curdate(),last_updated),campaign.profile_id,campaign.brand_id from campaign,vendor_supp_list_info,advertiser_info where campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id and deleted_date is null and campaign.status='T' ";
	}
	else
	{
		$sql = "select campaign.campaign_id, campaign.advertiser_id, campaign_name, campaign.status,concat(month(sent_datetime),'-',dayofmonth(sent_datetime),'-',year(sent_datetime)),date_format(scheduled_datetime,'%m-%d-%y %h:%i %p'),last60_flag,aol_flag,open_flag,id,campaign.list_cnt,hotmail_flag,yahoo_flag,other_flag,list_name,last_updated,filedate,vendor_supp_list_id,disable_flag,datediff(curdate(),last_updated),campaign.profile_id,campaign.brand_id from campaign,vendor_supp_list_info,advertiser_info,daily_deals,user where campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id and deleted_date is null and campaign.campaign_id=daily_deals.campaign_id and daily_deals.client_id=user.user_id ";
	}
	if (($networkopt > 0) && ($filteropt ne "T"))
	{
		if ($filteropt ne "W")
		{
			if ($filteropt eq "O")
			{
				$sql = $sql . " and campaign.profile_id in (select profile_id from list_profile where list_profile.client_id=$networkopt and list_profile.aol_flag='Y') and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) "; 
			}
			elsif ($filteropt eq "H")
			{
				$sql = $sql . " and campaign.profile_id in (select profile_id from list_profile where list_profile.client_id=$networkopt and list_profile.hotmail_flag='Y') and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) "; 
			}
			elsif ($filteropt eq "Z")
			{
				$sql = $sql . " and campaign.campaign_id in (select campaign_log.campaign_id from campaign_log where sent_cnt=0) and campaign.profile_id in (select profile_id from list_profile where list_profile.client_id=$networkopt) and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) and campaign.status='C' "; 
			}
			else
			{
				$sql = $sql . " and campaign.profile_id in (select profile_id from list_profile where list_profile.client_id=$networkopt) "; 
			}
		}
		else
		{
			$sql = $sql . " and campaign.campaign_id in (select campaign_id from daily_deals where client_id=$networkopt) ";
		}
	}
	elsif ($filteropt eq "O")
	{
		$sql = $sql . " and ((campaign.profile_id in (select profile_id from list_profile where list_profile.aol_flag='Y'))) and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) "; 
	}
	elsif ($filteropt eq "H")
	{
		$sql = $sql . " and ((campaign.profile_id in (select profile_id from list_profile where list_profile.hotmail_flag='Y'))) and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) "; 
	}
	elsif ($filteropt eq "Z")
	{
		$sql = $sql . " and ((campaign.campaign_id in (select campaign_id from campaign_log where sent_cnt=0))) and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) and campaign.status='C' "; 
	}

	if ($filteropt ne "W")
	{
		$sql = $sql . " and campaign.status != 'W' "; 
	}
	if ($filteropt ne "T")
	{
		$sql = $sql . " and campaign.status != 'T' "; 
	}
	if ($filteropt eq "D")
	{
		$sql = $sql . " and campaign.status = 'D' ";
	}
	elsif ($filteropt eq "W")
	{
		$sql = $sql . " and campaign.status = 'W' and campaign.campaign_id in (select campaign_id from daily_deals) ";
	}
	elsif ($filteropt eq "P")
	{
		$sql = $sql . " and campaign.status = 'A' ";
	}
	elsif ($filteropt eq "S")
	{
		$sql = $sql . " and campaign.status = 'S' ";
	}
	elsif ($filteropt eq "C")
	{
		$sql = $sql . " and campaign.status = 'C' ";
	}
	elsif (($filteropt eq "3") || ($filteropt eq "3_30"))
	{
		$sql = $sql . " and ((campaign.created_datetime >= date_sub(curdate(),interval 30 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 30 day))) ";
#		$sql = $sql . " campaign.created_datetime >= date_sub(curdate(),interval 30 day) ";
	}
	elsif (($filteropt eq "E") || ($filteropt eq ""))
	{
		$sql = $sql . " and ((campaign.created_datetime >= date_sub(curdate(),interval 3 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 3 day))) ";
	}
	elsif (($filteropt eq "7") || ($filteropt eq "3_7"))
	{
		$sql = $sql . " and ((campaign.created_datetime >= date_sub(curdate(),interval 7 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 7 day))) ";
	}
	elsif (($filteropt eq "L") || ($filteropt eq ""))
	{
		$sql = $sql . " and ((campaign.created_datetime >= date_sub(curdate(),interval 14 day)) or (campaign.scheduled_datetime >= date_sub(curdate(),interval 14 day))) ";
#		$sql = $sql . " campaign.created_datetime >= date_sub(curdate(),interval 14 day) ";
	}

	if (($filteropt ne "3_7") && ($filteropt ne "3_30"))
	{
		if ($networkopt > 0)
		{
			$sql = $sql . " and campaign_id not in (select campaign_id from 3rdparty_campaign where client_id=$networkopt) "; 
		}
		else
		{
			$sql = $sql . " and campaign_id not in (select campaign_id from 3rdparty_campaign) "; 
		}
	}
	else
	{
		if ($networkopt > 0)
		{
			$sql = $sql . " and campaign_id in (select campaign_id from 3rdparty_campaign where client_id=$networkopt) "; 
		}
		else
		{
			$sql = $sql . " and campaign_id in (select campaign_id from 3rdparty_campaign) "; 
		}
	}
	if ($cstring ne "")
	{
		$sql = $sql . " and campaign.campaign_name like '%$cstring%' ";
	}
	if ($filteropt ne "W")
	{
		$sql = $sql . " order by sent_datetime,scheduled_datetime,campaign_id";
	}
	else
	{
		$sql = $sql . " order by daily_deals.cday,scheduled_datetime,campaign.campaign_name";
	}
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	$reccnt = 0 ;
	$network_str="Yipes";
	while ( ($campaign_id, $aid, $campaign_name, $status, $sent_datetime, $scheduled_date,$clast60,$aol_flag,$copen,$tid,$list_cnt,$hotmail_flag,$yahoo_flag,$other_flag,$supp_name,$last_updated,$filedate,$sid,$disable_flag,$day_cnt,$profile_id,$brand_id) = $sth->fetchrow_array() )
	{
		$reccnt++;
		if (($reccnt % 2) == 0) 
		{
			$bgcolor = "$light_table_bg";
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg";
		}
		if ($disable_flag eq "Y")
		{
			$bgcolor = "red";
		}
		if ($disable_flag eq "E")
		{
			$bgcolor = "yellow";
		}

		$last60_flag = "";
#		if ($clast60 eq "Y")
#		{
#			$last60_flag = "<font size=+2 color=red>*</font>";
#		}
		$open_flag = "";
		if ($copen eq "Y")
		{
			$open_flag = "<font size=+2 color=green>*</font>";
		}
		
		if ($status eq "D") 
		{
			$status_name = "Draft";
			$date_str = "&nbsp;";
		}
		elsif ($status eq "W") 
		{
			$status_name = "Daily";
			my $cday;
			my $chour;
			$sql = "select cday from daily_deals where campaign_id=$campaign_id";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($cday) = $sth1->fetchrow_array();
			$sth1->finish();
			$sql = "select hour(scheduled_datetime) from campaign where campaign_id=$campaign_id";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($chour) = $sth1->fetchrow_array();
			$sth1->finish();
			if ($chour > 11)
			{
				$chour = $chour . " PM";
			}
			else
			{
				$chour = $chour . " AM";
			}
		
			$date_str = "Day $cday ($chour)";
		}
		elsif ($status eq "A") 
		{
			$status_name = "Approved";
			$date_str = "&nbsp;";
		}
		elsif ($status eq "S")
		{
			$status_name = "Scheduled";
			$date_str = $scheduled_date;
		}
		elsif ($status eq "P")
		{
			$status_name = "Pending";
			$date_str = $scheduled_date;
		}
		elsif ($status eq "C")
		{
			$status_name = "Completed";
			$date_str = $sent_datetime;
		}
		elsif ($status eq "T")
		{
			$status_name = "Trigger";
			$date_str = $sent_datetime;
		}
		else
		{
			$status_name = "Invalid Status";
			$date_str = "";
		}
		if ($profile_id > 0)
		{
			$sql = "select profile_name,aol_flag,yahoo_flag,hotmail_flag,other_flag,day_flag from list_profile where profile_id=$profile_id"; 
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($pname,$aol_flag,$yahoo_flag,$hotmail_flag,$other_flag,$clast60) = $sth1->fetchrow_array();
			$sth1->finish();
		}
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
		if ($yahoo_flag eq "M")
		{
			$sendto_str = $sendto_str . " Yahoo Last 30 Days/Openers";
		}
		if ($other_flag eq "Y")
		{
			$sendto_str = $sendto_str . " Other";
		}
		$sendto_str = $sendto_str . "<br>";
		if ($clast60 eq "Y")
		{
			$sendto_str = $sendto_str . "Last 60 Days";
		}
		elsif ($clast60 eq "7")
		{
			$sendto_str = $sendto_str . "Last 7 Days";
		}
		elsif ($clast60 eq "M")
		{
			$sendto_str = $sendto_str . "Last 30 Days";
		}
		elsif ($clast60 eq "9")
		{
			$sendto_str = $sendto_str . "Last 90 Days";
		}
		elsif ($clast60 eq "3")
		{
			$sendto_str = $sendto_str . "120-180 Days";
		}
		elsif ($clast60 eq "O")
		{
			$sendto_str = $sendto_str . "180 Days and older";
		}
		else	
		{
			$sendto_str = $sendto_str . "All Days";
		}
		if ($profile_id > 0)
		{
			$sendto_str = $sendto_str . "<br>(" . $pname . ")";
		}
		if ($status eq "W")
		{
			$sql = "select company from user where user_id = (select client_id from daily_deals where campaign_id=$campaign_id)";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($company_name) = $sth1->fetchrow_array();
			$sth1->finish();
		}
		else
		{
			if ($profile_id > 0)
			{
				$sql = "select company from user,list_profile where user.user_id=list_profile.client_id and profile_id=$profile_id"; 
			}
			else
			{
				$sql = "select distinct company from campaign_list,list,user where campaign_id=$campaign_id and campaign_list.list_id=list.list_id and list.user_id=user.user_id and list.list_id != 3";
			}
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			$company_name="";
			while (($temp_name) = $sth1->fetchrow_array())
			{
				$company_name = $company_name . $temp_name . " ";
			}
			$sth1->finish();
			$_ = $company_name;
			chop;
			$company_name = $_;
		}
		$brand_name="";
		if ($brand_id > 0)
		{
			$sql="select brand_name from client_brand_info where brand_id=$brand_id";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($brand_name) = $sth1->fetchrow_array();
			$sth1->finish();
			$brand_name = "-" . $brand_name;
		}

#$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
#$sth1 = $dbhq->prepare($sql);
#$sth1->execute();
#($BASE_DIR) = $sth1->fetchrow_array();
#$sth1->finish;
#		my $file = "${BASE_DIR}templates/camp_$campaign_id.txt";
#		$size = (-s $file)/1024;
		if (($status eq "C") || ($status eq "W"))
		{
			$sql="select sent_cnt from campaign_log where campaign_id=$campaign_id";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($camp_cnt) = $sth1->fetchrow_array();
			$sth1->finish();
		}
		else
		{
			if ($profile_id > 0)
			{
				$sql="select sum(member_cnt)-sum(hotmail_cnt)-sum(msn_cnt)-sum(foreign_cnt) from list where list_id in (select list_id from list_profile_list where profile_id=$profile_id)";
			}
			else
			{
				$sql="select sum(member_cnt)-sum(hotmail_cnt)-sum(msn_cnt)-sum(foreign_cnt) from list where list_id in (select list_id from campaign_list where campaign_id=$campaign_id)";
			}
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($camp_cnt) = $sth1->fetchrow_array();
			$sth1->finish();
		}
				$sql="select id from 3rdparty_campaign where campaign_id=$campaign_id";
				$sth1 = $dbhq->prepare($sql) ;
				$sth1->execute();
				if (($temp_cnt) = $sth1->fetchrow_array())
				{
				}
				else
				{
					$temp_cnt=0;
				}
				$sth1->finish();
		if (($user_id == 1) || ($user_type eq "A"))
		{
			if (($status ne "W") && ($status ne "T"))
			{
				if ($temp_cnt > 0)
				{
					print qq { <TR bgColor=$bgcolor>\n <TD>&nbsp;</TD>\n <TD align=left>$last60_flag$open_flag<a href="3rdparty_show_camp.cgi?campaign_id=$temp_cnt"><font color="#000000" face="Arial" size="1">$campaign_name (${company_name}$brand_name)</font></a></TD><font color="#000000" face="Arial" size="1"><td align=center>$network_str</font></td><td align=center><font color="#000000" face="Arial" size="1">$camp_cnt</font></td>\n <TD align=left><font color="#000000" face="Arial" size="1"> $status_name $date_str</font></TD>\n};
				}
				else
				{
					print qq { <TR bgColor=$bgcolor>\n <TD>&nbsp;</TD>\n <TD align=left>$last60_flag$open_flag<a href="show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U"><font color="#000000" face="Arial" size="1">$campaign_name (${company_name}$brand_name)</font></a></TD><font color="#000000" face="Arial" size="1"><td align=center>$network_str</font></td><td align=center><font color="#000000" face="Arial" size="1">$camp_cnt</font></td>\n <TD align=left><font color="#000000" face="Arial" size="1"> $status_name $date_str</font></TD>\n};
				}
			}
			elsif ($status eq "W")
			{
				print qq { <TR bgColor=$bgcolor>\n <TD>&nbsp;</TD>\n <TD align=left>$last60_flag$open_flag<a href="show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U&daily_flag=Y"><font color="#000000" face="Arial" size="1">$campaign_name (${company_name}$brand_name)</font></a></TD><font color="#000000" face="Arial" size="1"><td align=center>$network_str</font></td><td align=center><font color="#000000" face="Arial" size="1">$camp_cnt</font></td>\n <TD align=left><font color="#000000" face="Arial" size="1"> $status_name $date_str</font></TD>\n};
			}
			elsif ($status eq "T")
			{
				print qq { <TR bgColor=$bgcolor>\n <TD>&nbsp;</TD>\n <TD align=left>$last60_flag$open_flag<font color="#000000" face="Arial" size="1">$campaign_name (${company_name}$brand_name)</font></TD><font color="#000000" face="Arial" size="1"><td align=center>$network_str</font></td><td align=center><font color="#000000" face="Arial" size="1">$camp_cnt</font></td>\n <TD align=left><font color="#000000" face="Arial" size="1"> $status_name $date_str</font></TD>\n};
			}
#			if ($size > 10)
#			{
#				printf "<td align=left><font color=red face=Arial size=1>%3.1fk</font></td>\n",$size;
#			}
#			else
#			{
#				printf "<td align=left><font face=Arial size=1>%3.1fk</font></td>\n",$size;
#			}
			if ($supp_name ne "NONE")
			{
				if ($filedate eq "")
				{
					if ($day_cnt <= 7)
					{
						print qq { <TD align=left><font color="#000000" face="Arial" size="1">$sendto_str</font></TD>\n<td><font color="#000000" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid">$supp_name</a><br>$last_updated</font></td><TD align=left> };
					}
					else
					{
						print qq { <TD align=left><font color="#000000" face="Arial" size="1"> $sendto_str</font></TD>\n<td><font color="red" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid">$supp_name</a><br>$last_updated</font></td><TD align=left> };
					}
				}
				else
				{
					$sql = "select datediff(curdate(),'$filedate')";
					$sth1a = $dbhq->prepare($sql) ;
					$sth1a->execute();
					($day_cnt) = $sth1a->fetchrow_array();
					$sth1a->finish();
					if ($day_cnt <= 7)
					{
						print qq { <TD align=left><font color="#000000" face="Arial" size="1"> $sendto_str</font></TD>\n<td><font color="#000000" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid">$supp_name</a><br>$filedate</font></td><TD align=left> };
					}
					else
					{
						print qq { <TD align=left><font color="#000000" face="Arial" size="1"> $sendto_str</font></TD>\n<td><font color="red" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid">$supp_name</a><br>$filedate</font></td><TD align=left> };
					}
				}
			}
			else
			{
			print qq { 
        	<TD align=left><font color="#000000" face="Arial" size="1">
				$sendto_str</font></TD>\n<td><font color="red" face="Arial" size="1">$supp_name<br>$last_updated</font></td><TD align=left> };
			}
		}
		else
		{
			print qq { 
			<TR bgColor=$bgcolor>\n
        	<TD>&nbsp;</TD>\n
        	<TD align=left>$last60_flag$open_flag<a href="show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U"><font 
				color="#000000" face="Arial" size="1">$campaign_name</font></a></TD>\n
			<td align=left><font color="#000000" face="Arial" size="1">$tid</font></td>\n
			<td align=left><font color="#000000" face="Arial" size="1">$list_cnt</font></td>\n
        	<TD align=left><font color="#000000" face="Arial" size="1">
				$status_name $date_str</font></TD>\n
			};
#			if ($size > 10)
#			{
#				printf "<td align=left><font color=red face=Arial size=1>%3.1fk</font></td>\n",$size;
#			}
#			else
#			{
#				printf "<td align=left><font face=Arial size=1>%3.1fk</font></td>\n",$size;
#			}
			print qq { 
        	<TD align=left> };
		}
        if ($user_type ne "R")
        {
			if ($temp_cnt > 0)
			{
            	print qq {  <a href="camp_history.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">History</font></a><br><a href="3rdparty_deploy_main.cgi?id=$temp_cnt&mode=copy"><font color="#000000" face="Arial" size="1">Copy</font></a><br><a href="camp_send_test.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">Test</font></a><br>};
			}
			else
			{
            	print qq {  <a href="camp_history.cgi?campaign_id=$campaign_id">
            <font color="#000000" face="Arial" size="1">History</font></a><br>
                <a href="camp_copy.cgi?campaign_id=$campaign_id">
                <font color="#000000" face="Arial" size="1">Copy</font></a><br><a href="camp_send_test.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">Test</font></a><br>};
			}
        	if ($status eq "A" || $status eq "S" || $status eq "D" || $status eq "W" || $status eq "T") 
        	{
            	print qq { <a href="camp_del.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">Delete</font></a> };
			}
        	if ($status eq "C" || $status eq "P") 
        	{
				if ($temp_cnt == 0)
				{
					if ($disable_flag eq "Y")
            		{
                		print qq { <a href="camp_enable.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">ResumeMailing</font></a>};
            		}
            		elsif ($disable_flag eq "N")
            		{
                		print qq { <a href="camp_disable.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">StopMailing</font></a>};
            		}
        		}
            	if ($disable_flag eq "E")
            	{
                	print qq { <a href="camp_enable.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">ResumeMailing</font></a>};
            	}
			}
			elsif (($status eq "S") && ($disable_flag eq "E"))
			{
               	print qq { <a href="camp_enable.cgi?campaign_id=$campaign_id"><font color="#000000" face="Arial" size="1">ResumeMailing</font></a>};
			}
            print qq {</TD>\n
            <TD>&nbsp;</TD>\n
            </TR>\n };
        }
        else
        {
            print qq {  <a href="camp_history.cgi?campaign_id=$campaign_id">
            <font color="#000000" face="Arial" size="1">History</font></a><br>
            </TD>\n
            <TD>&nbsp;</TD>\n
            </TR>\n };
        }
	}
	$sth->finish();

	if ((($reccnt + 1) % 2) == 0) 
	{
		$bgcolor = "$light_table_bg";
	}
	else 
	{
		$bgcolor = "$alt_light_table_bg";
	}

print << "end_of_html";
			</TBODY>
			</TABLE>
		</form>
			<IMG height=7 src="$images/spacer.gif" width=453>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>
</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

util::footer();

$util->clean_up();
exit(0);
sub commify
{
    my $text = reverse $_[0];
    $text =~ s/(\d\d\d)(?=\d)(?!\d*\.)/$1,/g;
    return scalar reverse $text;
}
