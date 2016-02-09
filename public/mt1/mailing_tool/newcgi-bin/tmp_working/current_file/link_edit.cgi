#!/usr/bin/perl
# *****************************************************************************************
# link.cgi
#
# this page is to edit a redirect 
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
my $errmsg;
my $link_id = $query->param('link_id');
my $mode = $query->param('mode');
my $user_id;
my $refurl;
my $images = $util->get_images_url;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# read info for this link 

if (($mode eq "EDIT") || ($mode eq "VIEW"))
{
	$sql = "select refurl from links where link_id = $link_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($refurl) = $sth->fetchrow_array();
	$sth->finish();
}

# print out the html page

util::header("$mode Redirect");

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
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>$refurl</B></FONT> </TD>
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
			Use this screen to add or edit redirects.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="link_save.cgi" method="post">
		<input type="hidden" name="link_id" value="$link_id">
		<input type="hidden" name="mode" value="$mode">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<!-- Begin main body area -->

			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
			<TBODY>
			<TR bgColor=#509C10>
			<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
				border=0 width="7" height="7"></td>
			<td align=center><FONT face="verdana,arial,helvetica,sans serif" 
				color=#ffffff size=2><B>Redirect</B></FONT></td>
			<TD vAlign=top align=left height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD>&nbsp;</TD>
			</TR>

			<TR>
			<TD colspan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>URL</B></FONT></TD>
			</TR>
			<TR>
			<TD colspan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<INPUT type="text" size=80 maxlength=255 name=refurl value="$refurl"></TD>
			</TR>
			<TR>
			<TD colspan=3>&nbsp;</TD>
			</TR>
			<TR><TD colspan=3>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
end_of_html
if ($mode eq "VIEW")
{
print <<"end_of_html";
				<TD align=center width="50%">&nbsp;</TD>
end_of_html
}
else
{
print <<"end_of_html";
				<TD align=center width="50%">
					<INPUT TYPE=IMAGE src="$images/save.gif" border=0 
						width="81" height="22"></TD>
end_of_html
}
print <<"end_of_html";
				<TD align=middle width="50%"> 
					<a href="list_refurl.cgi"><img src="$images/cancel.gif" border=0></a></TD>
				</TR>
				</TBODY>
				</TABLE>

			</TD>
			<TD>&nbsp;</TD>
			</TR>
			<TR>
			<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
				width=7 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
				width=7 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

			<!-- End main body area -->

		</TD>
		</TR>
		<TR>
		<TD>&nbsp;</TD>
		</TR>
		<TR>
		<TD><IMG height=7 src="$images/spacer.gif"></TD>
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
<TD noWrap align=left height=1>
end_of_html

$util->footer();

# exit function

$util->clean_up();
exit(0);
