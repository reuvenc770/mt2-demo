#!/usr/bin/perl
# *****************************************************************************************
# edit_category.cgi
#
# this page is to edit a category 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $cat_id = $query->param('cat_id');
my $userid = $query->param('userid');
my $user_id;
my $cname;
my $dname;
my $images = $pms->get_images_url;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# read info for this category 

	$sql = "select category_name,domain_name from category_info,client_category_info where category_info.category_id = $cat_id and category_info.category_id=client_category_info.category_id and user_id=$userid";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($cname,$dname) = $sth->fetchrow_array();
	$sth->finish();

# print out the html page

util::header("Edit Category");

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
			color=#509C10 size=3><B>$cname</B></FONT> </TD>
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
			Use this screen to Edit Categories.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="add_category.cgi" method="post">
		<input type="hidden" name="cat_id" value="$cat_id">
		<input type="hidden" name="userid" value="$userid">
		<input type="hidden" name="mode" value="EDIT">

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
				color=#ffffff size=2><B>Category</B></FONT></td>
			<TD vAlign=top align=left height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD>&nbsp;</TD>
			</TR>

			<TR>
			<TD colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				Category</TD>
			<td>Domain Name</td>
			</TR>
			<TR>
			<TD colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<INPUT type="text" size=25 maxlength=25 name=cname value="$cname"></TD>
			<td><input type="text" size=25 maxlength=50 name=dname value="$dname"></td>
			</TR>
			<TR>
			<TD colspan=3>&nbsp;</TD>
			</TR>
			<TR><TD colspan=3>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=center width="50%">
					<INPUT TYPE=IMAGE src="$images/save.gif" border=0 
						width="81" height="22"></TD>
end_of_html
print <<"end_of_html";
				<TD align=middle width="50%"> 
					<a href="list_category.cgi"><img src="$images/cancel.gif" border=0></a></TD>
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

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
