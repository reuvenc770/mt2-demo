#!/usr/bin/perl

# *****************************************************************************************
# mainmenu.cgi
#
# History
# Grady Nash, 7/30/01, Creation
# Mike Baker, 7/31/01  Added code to Display LISTs
# Jim Sobeck, 01/28/03 Added logic for computing Revenue
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
my $sth;
my $sth1;
my $BASE_DIR;
my $list_cnt;
my $sql;
my $dbh;
my $tid;
my $size;
my ($campaign_id, $campaign_name, $status, $sent_datetime, $scheduled_date,$clast60,$last60_flag,$aol_flag,$copen,$open_flag,$hotmail_flag,$yahoo_flag,$other_flag,$supp_name,$last_updated,$sid);
my ($fm_campaign_id, $fm_campaign_name);
#my $filteropt = $query->param('filteropt');
my $filteropt;
my $cstring;
my $filteropt_sel_a = "";
my $filteropt_sel_s = "";
my $filteropt_sel_d = "";
my $filteropt_sel_approved = "";
my $filteropt_sel_c = "";
my $filteropt_sel_l = "";
my $filteropt_sel_7 = "";
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

my $images = $util->get_images_url;

#------ connect to the util database -------------------
$util->db_connect();
$dbh = $util->get_dbh;

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
$cstring = $cookies{'cstring'};

if ($filteropt eq "A")
{
	$filteropt_sel_a = "selected";
}
elsif (($filteropt eq "L") || ($filteropt eq ""))
{
	$filteropt_sel_l = "selected";
}
elsif ($filteropt eq "7") 
{
	$filteropt_sel_7 = "selected";
}
elsif ($filteropt eq "3") 
{
	$filteropt_sel_30 = "selected";
}
elsif ($filteropt eq "D")
{
	$filteropt_sel_d = "selected";
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
$sth = $dbh->prepare($sql) ;
$sth->execute();
($ads_url) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------------
# Get User_Type, UserName from user 
#--------------------------------------

$sql = "select user_type, username, first_name, last_name from user where user_id = $user_id";
$sth = $dbh->prepare($sql) ;
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
		<TD vAlign=top align=middle width=223>
		<IMG height=7 src="$images/spacer.gif" width=223>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
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
				<a href="camp_step1.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Build A New Campaign</FONT></a></TD>
			</TR>
			<!-- <TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="fm_camp.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Build A Follow-Me Campaign</FONT></a></TD>
			</TR> -->
<!--
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="fm_rules_review.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Follow-Me Rules Wizard</FONT></a></TD>
			</TR>
-->
			<!-- <TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="upload_images.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Upload Graphics</FONT></a></TD>
			</TR> 
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="upload_images_browse.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				View Graphics Directory</FONT></a></TD>
			</TR> 
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="user_page_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Maintain Pages for Tracking</FONT></a></TD>
			</TR> -->
			<TR>
			<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
			<TD vAlign=bottom align=right width=9 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=223>

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
    			<a href="list_list.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
    			Add or Update Lists</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="sub_disp_add.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Add Subscribers</FONT></a></TD>
			</TR>
<!--			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="sub_disp_del.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Remove Subscribers</FONT></a></TD>
			</TR> -->
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="sub_disp_uns.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Unsubscribe Subscribers</FONT></a></TD>
			</TR>
			<!-- <TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp; 
				<a href="sub_disp_exp.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Export Subscribers</font></a></TD>
			</TR> 
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp; 
				<a href="list_seg_step1.cgi">
    			<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Segment Your Lists</font></a></TD>
			</TR> -->
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
print<<"end_of_html";
			<IMG height=7 src="$images/spacer.gif" width=223>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=left width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tl.gif" border=0 width="8" height="8"></font></TD>
    		<TD vAlign=bottom width="100%" height=17><B><FONT face=Arial 
    			color=#000000 size=2>Reports</FONT></B></TD>
    		<TD vAlign=top align=right width=9 height=17><font face="Arial"><IMG 
    			src="$images/yellow_tr.gif" border=0 width="8" height="8"></font></TD></TR>
			<!-- <TR>

    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="snapshot_report.cgi"> 
    			<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Snapshot</FONT></a></TD>
			</TR> -->

			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="powermail_report1.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Campaigns - Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="powermail_report.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Campaigns - Last 30 Days</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="aol_powermail_report1.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				AOL-Current Month</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="aol_powermail_report.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				AOL-Last 30 Days</FONT></a></TD>
			</TR>
			<TR>
    		<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
    			<a href="/schedule_frame.html">
				<FONT face="verdana,arial,helvetica,sans serif" size=2>
				Scheduled Campaigns</FONT></a></TD>
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

			<IMG height=7 src="$images/spacer.gif" width=223>

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
			<IMG height=7 src="$images/spacer.gif" width=223>


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
    		<TD vAlign=bottom align=left width=9 colSpan=2 height=7><font face="Arial">
				<IMG height=7 src="$images/yellow_bl.gif" width=8 border=0></font></TD>
    		<TD vAlign=bottom align=right width=9 height=7><font face="Arial"><IMG height=7 
    			src="$images/yellow_br.gif" width=8 border=0></font></TD>
			</TR>
			</TBODY>
			</TABLE>
			<IMG height=7 src="$images/spacer.gif" width=223>

			<TABLE cellSpacing=0 cellPadding=0 width=208 bgColor=$alt_light_table_bg border=0>
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
			<IMG height=7 src="$images/spacer.gif" width=223>
			
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
	#---------------------------------------------------------------------
	# Only Display these links if the user is an ADMIN user
	#---------------------------------------------------------------------
	if ( $user_type eq "A" )
	{
		print << "end_of_html";
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
				Update Advertisers</FONT></a></TD>
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
				<a href="template_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Maintain Templates</FONT></a></TD>
			</TR>
			<!-- <TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="fm_template_list.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				Follow Me Templates</FONT></a></TD>
			</TR> 
			<TR>
			<TD colSpan=3 height=12>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="wss_support_view.cgi">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>
				View Support Issues</FONT></a></TD>
			</TR> -->
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
			<IMG height=7 src="$images/spacer.gif" width=223>

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
			<IMG height=7 src="$images/spacer.gif" width=223>

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
			<IMG height=7 src="$images/spacer.gif" width=223> -->
			
			
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
#-----------------------------------------------------
# Count Number of Email Addresses in ALL Users Lists
#-----------------------------------------------------
if (($user_id == 1) or ($user_type eq "R"))
{
$sql = "select count(distinct(email_user_id)) 
	from list l, list_member lm where  
	l.list_id = lm.list_id and 
	l.status = 'A' and 
	lm.status = 'A'";
}
else
{
$sql = "select count(distinct(email_user_id)) 
	from list l, list_member lm where l.user_id = $user_id and 
	l.list_id = lm.list_id and 
	l.status = 'A' and 
	lm.status = 'A'";
}
#$sth = $dbh->prepare($sql);
#$sth->execute();
#$nbr_subscribers = $sth->fetchrow_array();
#$sth->finish();
$nbr_subscribers = 0;
$total_aol = 0;
$total_hotmail = 0;
$total_yahoo = 0;
$total_foreign = 0;
$total_other = 0;

#---------------------
# Begin HTML Prints
#---------------------
print << "end_of_html";
<p>
        <center>
<a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a>
        </center>
	<TABLE cellSpacing=0 cellPadding=0 width=740 bgColor=$alt_light_table_bg border=0>
	<TBODY>
	<TR bgColor=$table_header_bg>
	<TD vAlign=top align=left><IMG src="$images/blue_tl.gif" border=0 width="7" height="7"></TD>
	<TD align=left width=275 height=15><b><font color="white" size="1" 
		face="Arial">Lists</font></b></TD>
	<TD align=left width=75 height=15><B><FONT face=Arial color=white 
		size=1>Subscribers </FONT></B> </TD>
	<TD align=left width=75 height=15><B><FONT face=Arial color=white 
		size=1>AOL Cnt</FONT></B> </TD>
	<TD align=left width=75 height=15><B><FONT face=Arial color=white 
		size=1>Hotmail/MSN Cnt</FONT></B> </TD>
	<TD align=left width=75 height=15><B><FONT face=Arial color=white 
		size=1>Yahoo Cnt</FONT></B> </TD>
	<TD align=left width=75 height=15><B><FONT face=Arial color=white 
		size=1>Foreign Cnt</FONT></B> </TD>
	<TD align=left width=75 height=15><B><FONT face=Arial color=white 
		size=1>Other Cnt</FONT></B> </TD>
	<TD align=middle height=15><B><FONT face=Arial color=white 
		size=1>Functions </FONT></B> </TD>
	<TD vAlign=top align=right><IMG src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
	</TR>
end_of_html

	#-----------------------------------------------
	# Loop - Get ALL Lists that belong to the User
	#-----------------------------------------------

#	if (($user_id == 1) || ($user_type eq "R"))
#	{
		$sql = "select list.list_id, list.list_name, list.status, user.company from list,user where list.user_id = user.user_id and list.status='A' order by list_name";
#	}
#	else
#	{
#		$sql = "select list.list_id, list.list_name, list.status, user.company 
#			from list,user where list.user_id = $user_id and
#			list.user_id = user.user_id and 
#			list.status='A' order by list_name";
#	}

	$sth = $dbh->prepare($sql);
	$sth2 = $dbh->prepare("select member_cnt,aol_cnt,hotmail_cnt,msn_cnt,yahoo_cnt,foreign_cnt from list where list_id = ? and status = 'A' ") ;
	$sth->execute();
	$reccnt = 0 ;
	while ( ($list_id, $list_name, $status, $username) = $sth->fetchrow_array() )
	{
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "$light_table_bg" ;
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;
		}

		$sth2->execute($list_id) ;
		($nbr_list_members,$aol_cnt,$hotmail_cnt,$msn_cnt,$yahoo_cnt,$foreign_cnt) = $sth2->fetchrow_array() ;
		$hotmail_cnt = $hotmail_cnt + $msn_cnt;
		$nbr_subscribers = $nbr_subscribers + $nbr_list_members;

		$nonaol_cnt = $nbr_list_members - $aol_cnt - $hotmail_cnt - $yahoo_cnt - $foreign_cnt;
		$total_aol = $total_aol + $aol_cnt;
		$total_hotmail = $total_hotmail + $hotmail_cnt;
		$total_yahoo = $total_yahoo + $yahoo_cnt;
		$total_foreign = $total_foreign + $foreign_cnt;
		$total_other = $total_other + $nonaol_cnt;
		if (($user_id == 1) || ($user_type eq "A"))
		{	
			print qq { 
				<TR bgColor=$bgcolor>
        		<TD>&nbsp;</TD>
        		<TD align=left width=300><font color="#000000" face="Arial" 
					size="1">$list_name ($username)</font></TD>
        		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$nbr_list_members</font></TD>
        		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$aol_cnt</font></TD>
        		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$hotmail_cnt</font></TD>
        		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$yahoo_cnt</font></TD>
        		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$foreign_cnt</font></TD>
        		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$nonaol_cnt</font></TD>
        		<TD><a href="list_del.cgi?list_id=$list_id"><font color="#000000"
                	face="Arial" size="1">Delete</font></a></TD>
        		<TD>&nbsp;</TD>
				</TR> \n } ;
		}
		else
		{
			if ($user_type eq "R")
			{
			print qq { 
				<TR bgColor=$bgcolor>
        		<TD>&nbsp;</TD>
        		<TD align=left width=300><font color="#000000" face="Arial" 
					size="1">$list_name</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$nbr_list_members</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$aol_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$hotmail_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$yahoo_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$foreign_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$nonaol_cnt</font></TD>
        		<TD>&nbsp;</TD>
				</TR> \n } ;
			}
			else
			{
			print qq { 
				<TR bgColor=$bgcolor>
        		<TD>&nbsp;</TD>
        		<TD align=left width=300><font color="#000000" face="Arial" 
					size="1">$list_name</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$nbr_list_members</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$aol_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$hotmail_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$yahoo_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$foreign_cnt</font></TD>
     	   		<TD align=left width=75><font color="#000000" 
					face="Arial" size="1">$nonaol_cnt</font></TD>
        		<TD><a href="list_del.cgi?list_id=$list_id"><font color="#000000"
                    face="Arial" size="1">Delete</font></a></TD>
        		<TD>&nbsp;</TD>
				</TR> \n } ;
			}
		}
	}  

	$sth->finish();
	$sth2->finish();

	if ((($reccnt + 1) % 2) == 0) 
	{
		$bgcolor = "$light_table_bg";
	}
	else 
	{
		$bgcolor = "$alt_light_table_bg";
	}

	$sql = "select format($nbr_subscribers,0),format($total_aol,0),format($total_hotmail,0),format($total_yahoo,0),format($total_foreign,0),format($total_other,0)";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($nbr_subscribers,$total_aol,$total_hotmail,$total_yahoo,$total_foreign,$total_other) = $sth1->fetchrow_array();
	$sth1->finish;
	print << "end_of_html";
    <TR bgColor=$bgcolor>
	<TD vAlign=bottom align=middle colSpan=2 height=16><B><FONT face="Arial" color=#000000 size=2>Totals</font></B></TD>
	<TD vAlign=bottom align=left height=16><FONT face="Arial" color=#000000 size=2><B>$nbr_subscribers</B></font></TD>
	<TD vAlign=bottom align=left height=16><FONT face="Arial" color=#000000 size=2><B>$total_aol</B></font></TD>
	<TD vAlign=bottom align=left height=16><FONT face="Arial" color=#000000 size=2><B>$total_hotmail</B></font></TD>
	<TD vAlign=bottom align=left height=16><FONT face="Arial" color=#000000 size=2><B>$total_yahoo</B></font></TD>
	<TD vAlign=bottom align=left height=16><FONT face="Arial" color=#000000 size=2><B>$total_foreign</B></font></TD>
	<TD vAlign=bottom align=left height=16><FONT face="Arial" color=#000000 size=2><B>$total_other</B></font></TD>
<!--	<TD vAlign=bottom align=middle colSpan=9 height=16><FONT face="Arial" color=#000000 
		size=1>Total Unique Subscribers: $nbr_subscribers </FONT></TD> -->
	</TR>
    <TR bgColor=$alt_light_table_bg>
   	<TD vAlign=bottom align=left><IMG height=7 src="$images/yellow_bl.gif" width=7 border=0></TD>
   	<TD colSpan=4>&nbsp;</TD>
   	<TD vAlign=bottom align=right><IMG height=7 src="$images/yellow_br.gif" width=7 border=0></TD>
	</TR>
	</TBODY>
	</TABLE>
<p>
	<center>
<a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a>
	</center>
end_of_html

#-------------------------------------------------------------------------------
# End code to Display Users LISTS
#-------------------------------------------------------------------------------

print << "end_of_html";
</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

util::footer();

$util->clean_up();
exit(0);
