#!/usr/bin/perl

# *****************************************************************************************
# list_list.cgi
#
# this page displays the list of lists and lets the user edit / add
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
my $user_id;
my $list_id;
my $list_name;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;

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

# print out the html page

util::header("LISTS");

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
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a list to edit or click Add to create a new list</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="4" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>List of Current Lists</b></font></TD>
		</TR>
		<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="70%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>List Name</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="20%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Status</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="08%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
			<b>&nbsp;</b></font></td>
		</TR> 
end_of_html

# read info about the lists

$sql = "select list_id,list_name,status from list
	where user_id = $user_id order by list_name";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id,$list_name,$status) = $sth->fetchrow_array())
{
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }

	if ($status eq "A")
	{
		$status_name = "Active";
	}
	else
	{
		$status_name = "Inactive";
	}

	print qq { <TR bgColor=$bgcolor> 
		<TD>&nbsp;</td> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			<A HREF="list_edit.cgi?list_id=$list_id&mode=EDIT">$list_name</a>
			</font></TD> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			$status_name</font></TD> 
 		<TD>&nbsp;</TD> 
 		</TR> \n };
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			<td width="50%" align="center">
				<a href="list_edit.cgi?mode=ADD">
				<img src="$images/add.gif" border=0></a></td>
			</tr>
			</table>

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
<TD noWrap align=left height=17>
end_of_html

$util->footer();

# exit function

$util->clean_up();
exit(0);
