#!/usr/bin/perl
# *****************************************************************************************
# list_edit.cgi
#
# this page is to edit a list
#
# History
# Grady Nash, 8/30/01, Creation
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
my $list_id = $query->param('list_id');
my $mode = $query->param('mode');
my $user_id;
my $list_name;
my ($status, $optin_flag, $list_name, $double_mail_template, $thankyou_mail_template);
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
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

# read info for this list

if ($mode eq "EDIT")
{
	$sql = "select list_name, status, optin_flag, double_mail_template, thankyou_mail_template
		from list where list_id = $list_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($list_name, $status, $optin_flag, $double_mail_template, 
		$thankyou_mail_template) = $sth->fetchrow_array();
	$sth->finish();
}

# print out the html page

util::header("$mode LIST");

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
			color=#509C10 size=3><B>$list_name</B></FONT> </TD>
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
			Use this screen to add or edit email lists.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="list_save.cgi" method="post">
		<input type="hidden" name="list_id" value="$list_id">
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
				border=0 width="7" height="7"></TD>
			<TD align=middle height=15><FONT face="verdana,arial,helvetica,sans serif" 
				color=#ffffff size=2><B>List</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>List Name</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<INPUT type="text" size=40 maxlength=40 name=list_name value="$list_name"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Optin Option</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<select name="optin_flag">
				<option value="S">Single Optin</option>
				<option value="D">Double Optin</option>
				</select></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>List Status</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<select name="status">
				<option value="A">Active</option>
				<option value="I">Inactive</option>
				</select></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Thank You Mail Template</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA name=thankyou_mail_template rows=8 
				cols=70>$thankyou_mail_template</TEXTAREA></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Double Optin Mail Template</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA name=double_mail_template rows=8 
				cols=70>$double_mail_template</TEXTAREA></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=center width="50%">
					<INPUT TYPE=IMAGE src="$images/save.gif" border=0 
						width="81" height="22"></TD>
				<TD align=middle width="50%"> 
					<a href="list_list.cgi"><img src="$images/cancel.gif" border=0></a></TD>
				</TR>
				</TBODY>
				</TABLE>

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
<TD noWrap align=left height=17>
end_of_html

$util->footer();

# exit function

$util->clean_up();
exit(0);
