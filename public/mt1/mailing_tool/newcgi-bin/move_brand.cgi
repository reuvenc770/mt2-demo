#!/usr/bin/perl

# *****************************************************************************************
# move_brand.cgi
#
# this page is to move a brand 
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
my $bid=$query->param('bid');

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


# print html page out

util::header("Move a Brand");

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
			You need to select a client to move the brand to.<BR></FONT>
            </font></td> 
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="brand_move.cgi" method=post>
		<input type=hidden name=bid value=$bid>
		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
end_of_html
my $cname;
$sql = "select first_name from user where user_id=$client_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($cname) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<TR>
		<TD><b>Current Client</b></td><td>$cname</td></tr>
		<TR>
		<TD><b>New Client</b></td><td><select name="newclient">
end_of_html
$sql = "select user_id,first_name from user where status ='A' and user_id!=$client_id order by first_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($aid,$aname) = $sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
	<tr><td><img src=$images/spacer.gif height=10></td><tr>
			<TR>
			<TD colspan=2 align=right>
				<a href="mainmenu.cgi">
				<IMG hspace=7 src="$images/home_blkline.gif" border=0 width="90" height="22"></a>
				<IMG height=1 src="$images/spacer.gif" width=340 border=0> 
				<INPUT type=image src="$images/save.gif" border=0> 
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
