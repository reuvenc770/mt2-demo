#!/usr/bin/perl

# *****************************************************************************************
# sysparm_edit.cgi
#
# this page is to edit a system parameter
#
# History
# Grady Nash, 11/09/01, Creation
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
my $parmkey = $query->param('parmkey');
my $parmval;
my $user_id;
my $images = $util->get_images_url;
my $table_text_color = $util->get_table_text_color;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# read info for this parm

$sql = "select parmval from sysparm where parmkey = '$parmkey'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($parmval) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("Edit System Parameter");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>$parmkey</B></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Use this screen to edit the $parmkey value</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="sysparm_save.cgi" method="post">
		<input type="hidden" name="parmkey" value="$parmkey">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
			<TBODY>
			<TR bgColor=#509C10>
			<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
				border=0 width="7" height="7"></TD>
			<TD align=middle height=15><FONT face="verdana,arial,helvetica,sans serif" 
				color=#ffffff size=2><B>System Parameter</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Parameter Value</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA name="parmval" rows="10" cols="70">$parmval</TEXTAREA></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
				<td align="center" width="50%">
					<a href="sysparm_list.cgi">
					<img src="$images/previous_arrow.gif" border="0"></a></td>
				<td align="center" width="50%">
					<input type="image" src="$images/save.gif" border=0></TD>
				</tr>
				</table>

			</TD>
			<TD>&nbsp;</TD>
			</TR>
			<TR>
			<TD vAlign=bottom align=left colSpan=2><IMG height=7 src="$images/lt_purp_bl.gif" 
				width=7 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
				width=7 border=0></TD>
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

# exit function

$util->clean_up();
exit(0);
