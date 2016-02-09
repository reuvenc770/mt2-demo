#!/usr/bin/perl
# *****************************************************************************************
# edit_medical.cgi
#
# this page is to edit the information for a medical condition 
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
my $user_id;
my $cname;
my $info_id;
my $type_id;
my $text_str;
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

# read info for this medical condition 

	$sql = "select name from medical_condition where medical_id = $cat_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($cname) = $sth->fetchrow_array();
	$sth->finish();

# print out the html page

util::header("Edit Medical Condition");

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
			Use this screen to Edit Information about a Medical Condition.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<!-- Begin main body area -->
			<form method=post action="add_medical_info.cgi">
			<input type=hidden name=cat_id value=$cat_id>
			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
			<tr><td align=middle colspan=2><b>Add New Information</b></td></tr>
			<tr><td colspan=2>&nbsp;</td></tr>
			<tr><td width=80 align=right>Info Type:</td>
			<td>
			<select name=info_type>
			<option value="S">Symptoms</option>
			<option value="T">Treatment</option>
			<option value="W">What</option>
			</select>
			</td></tr>
			<tr><td valign=top align=right>Text:</td><td><textarea rows=10 cols=70 name=info_text></textarea></td></tr>
			<tr><td align=middle colspan=2><INPUT TYPE=submit value="Add"></td></tr>
			</table>
			</form>
			<p>
			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=1>
			<tr><th>Type</th><th>Text</th><th>Function</th></tr>
end_of_html
$sql = "select info_id,type_id,text_str from medical_info where medical_id=$cat_id order by type_id,info_id";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($info_id,$type_id,$text_str) = $sth->fetchrow_array())
{
	my $type_str;
	if ($type_id == "S")
	{
		$type_str = "Symptom";
	}	
	elsif ($type_id == "T")
	{
		$type_str = "Treatment";
	}	
	if ($type_id == "W")
	{
		$type_str = "What";
	}	
	print "<tr><td>$type_str</td><td>$text_str</td><td><a href=\"edit_medical_info.cgi?cat_id=$cat_id&info_id=$info_id\">Edit</a>&nbsp;&nbsp;<a href=\"delete_medical_info.cgi?cat_id=$cat_id&info_id=$info_id\">Delete</a></td></tr>\n";
}
$sth->finish();
print<<end_of_html;
				</TABLE>
				<center>
				<p>
				<a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a>
				</center>

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
