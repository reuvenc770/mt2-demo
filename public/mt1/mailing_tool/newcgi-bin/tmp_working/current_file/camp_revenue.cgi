#!/usr/bin/perl

# *****************************************************************************************
# camp_revenue.cgi
#
# this page is for setting up revenue information for a campaign 
#
# History
# Jim Sobeck, 7/30/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth2;
my $sql;
my $dbh;
my $campaign_id = $query->param('campaign_id');
my $list_id;
my $status;
my $schedule_date;
my $max_emails;
my $light_table_bg = $util->get_light_table_bg;
my $images = $util->get_images_url;
my $list_members = 1;
my $counter;
my $ctype;
my $crevenue;
my $caction_cnt;
my $cinput;

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

# print out the html page

util::header("Campaign Revenue Info");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10><!-- doing ct-table-open -->

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
			<TD vAlign=center align=left><font face="verdana,arial,helvetica,sans serif" 
				color="#509C10" size="3"><b>Campaign Revenue Info</b></font></TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Set your Campaign to either CPA or CPM. Also set the Cost Per Action/Cost Per Thousand Amount.  If CPA, enter number of actions.  If need to override calculated revenue, then enter value for Actual Revenue.
			<BR></FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="camp_revenue_save.cgi" method=post>
		<INPUT type=hidden name="campaign_id" value="$campaign_id">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=middle>

				<TABLE cellSpacing=0 cellPadding=0 width=500 bgColor=$light_table_bg border=0>
				<TBODY>
				<TR align=top bgColor=#509C10 height=18>
				<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" border=0 
					width="7" height="7"></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD align=middle height=15>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR bgColor=#509C10 height=15>
					<TD align=middle width="100%" height=15>
						<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Campaign Revenue Info</B> </FONT> </TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
				<TR bgColor=$light_table_bg>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=$light_table_bg>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR>
					<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					<TR>
					<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
						color=#509C10 size=2>
end_of_html

# get schedule information for this campaign

$sql = "select campaign_type, revenue, action_cnt, input_revenue from campaign where campaign_id=$campaign_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($ctype,$crevenue,$caction_cnt,$cinput) = $sth->fetchrow_array();
$sth->finish();
if ($ctype eq "CPA")
{
print<<"end_of_html"; 
<pre>
<font size="+1">
Revenue Type:                <select name=ctype>
<option selected value="CPA">CPA</option>
<option value="CPM">CPM</option>
</select><br>
Cost per Action/CPM:         <INPUT type="text" name="crevenue" size="10" value="$crevenue"><br>
Number of Actions(only CPA): <INPUT type="text" name="caction_cnt" size="10" value="$caction_cnt"><br>
Actual Revenue:              <INPUT type="text" name="cinput" size="10" value="$cinput">
</pre>
end_of_html
}
else
{
print<<"end_of_html"; 
<pre>
<font size="+1">
Revenue Type:                <select name=ctype>
<option value="CPA">CPA</option>
<option selected value="CPM">CPM</option>
</select><br>
Cost per Action/CPM:         <INPUT type="text" name="crevenue" size="10" value="$crevenue"><br>
Number of Actions(only CPA): <INPUT type="text" name="caction_cnt" size="10" value="$caction_cnt"><br>
Actual Revenue:              <INPUT type="text" name="cinput" size="10" value="$cinput">
</pre>
end_of_html
}
$sth->finish();

print << "end_of_html";
						<BR></FONT></TD>
					</TR>
					<TR>
					<TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
				<TR bgColor=$light_table_bg>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=$light_table_bg height=10>
				<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD align=middle bgColor=$light_table_bg><IMG height=3 
					src="$images/spacer.gif" width=1 
					border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" width=7 
					border=0></TD>
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
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<td align="center" width="50%">
				<a href="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></td>
			<td align="center" width="50%">
				<INPUT type=image src="$images/save.gif" border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

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
