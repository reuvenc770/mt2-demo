#!/usr/bin/perl
# *****************************************************************************************
# user_page_edit.cgi
#
# this page is to edit a user page
#
# History
# Grady Nash, 11/1/2001, Creation
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
my $user_page_id = $query->param('user_page_id');
my $mode = $query->param('mode');
my $user_id;
my $page_name;
my $status;
my $images = $util->get_images_url;
my $bin_dir_http;

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

# lookup directory from sysparm

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'";
$sth = $dbh->prepare($sql);
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
$sth->finish();

# read info for this page

if ($mode eq "EDIT")
{
	$sql = "select page_name, status
		from user_page where user_page_id = $user_page_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($page_name, $status) = $sth->fetchrow_array();
	$sth->finish();
}

# print out the html page

util::header("$mode User Page");

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
			color=#509C10 size=3><B>$page_name</B></FONT> </TD>
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
			Use this screen to add or edit User Pages. </FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="user_page_save.cgi" method="post">
		<input type="hidden" name="user_page_id" value="$user_page_id">
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
				color=#ffffff size=2><B>User Page</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Page Name</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<INPUT type="text" size=40 maxlength=255 name="page_name" value="$page_name"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Status</B></FONT></TD>
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
end_of_html

if ($mode eq "EDIT")
{
	print << "end_of_html";
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Code To Place On Your Website To Track This Page</B>
				</FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				&lt;img src="${bin_dir_http}utilaction.cgi?id=emailid&p=$user_page_id" height="1" width="1" border="0"&gt;
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
end_of_html
}

print << "end_of_html";
			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=center width="50%">
					<a href="user_page_list.cgi"><img src="$images/cancel.gif" border=0></a></TD>
				<TD align=middle width="50%"> 
					<INPUT TYPE=IMAGE src="$images/save.gif" border=0></TD>
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
