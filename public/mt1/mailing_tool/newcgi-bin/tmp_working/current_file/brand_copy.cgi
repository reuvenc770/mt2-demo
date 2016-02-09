#!/usr/bin/perl

# *****************************************************************************************
# brand_copy.cgi
#
# this page is to copy a brand 
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
my $aname;
my $template_id;
my $images = $util->get_images_url;
my $client_id =$query->param('cid');

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


# print html page out

util::header("Copy a Brand");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			You need to select a brand to copy.  You need to give this brand a unique name.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="brand_save.cgi" method=post>
		<input type=hidden name=cid value=$client_id>
		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><b>Brand</b></td><td><select name="oldaid">
end_of_html
$sql = "select brand_id,brand_name from client_brand_info where status ='A' order by brand_name"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($aid,$aname) = $sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
<tr>
<td><B>New Brand Name</B></FONT></TD>
<td><input type=text name="brand_name"></td>
					</TR>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
			<TR>
			<TD colspan=2 align=right>
				<a href="mainmenu.cgi">
				<IMG hspace=7 src="$images/exit_wizard.gif" border=0 width="90" height="22"></a>
				<IMG height=1 src="$images/spacer.gif" width=340 border=0> 
				<INPUT type=image src="$images/next_arrow.gif" border=0> 
			</TD>
			</TR>
			</TBODY>
			</TABLE>
		</FORM>

	</TD></TR>
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
