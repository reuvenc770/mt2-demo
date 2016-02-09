#!/usr/bin/perl
# ******************************************************************************
# camp_send_test_sm.cgi
#
# this page sends a test email using strongmail
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $count;
my $sth;
my $sql;
my $dbh;
my $template_name;
my $campaign_id = $query->param('campaign_id');
my $campaign_name;
my $brand_id;
my $profile_id;
my $ptype;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# get campaign name

$sql = "select campaign_name,brand_id from campaign where campaign_id=$campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($campaign_name,$brand_id) = $sth->fetchrow_array();
$sth->finish();

# print html page out

util::header("Send Strongmail Email Test");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>Enter email address(es) seperate by commas.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<FORM name=campform action=camp_send_test_sm_save.cgi method=post target=_top>
		<INPUT type=hidden value=$campaign_id name=campaign_id> 
		<TABLE cellSpacing=0 cellPadding=0 width=800 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
    		<TBODY>
    		<TR>
    		<TD align=middle>
			<table width=100%>
			<tr>
			<TD>Email Address: </td><td><input type=text size=80 name=email_addr></td></tr>
			<tr><td>Mailing Domain: </td><td><select name=url>
end_of_html
			$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O' order by url";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			my $url;
			while (($url) = $sth->fetchrow_array())
			{
				print "<option value=\"$url\">$url</option>\n";
			}
$sth->finish();
print<<"end_of_html";
			</td></tr>
			<tr><td>Include Wiki: </td><td><input type=radio value="Y" name="wiki">Yes&nbsp;&nbsp;<input type=radio value="N" checked name="wiki">No
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
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD width="50%" align="center">
				<a href="mainmenu.cgi"><img src="$images/cancel.gif" border=0></a></td>
			<td width="50%" align="center">
				<INPUT type=image src="$images/next.gif" border=0></TD>
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
