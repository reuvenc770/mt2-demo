#!/usr/bin/perl
# *****************************************************************************************
# user_page_list.cgi
#
# displays user page tracking ids
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
my $user_id;
my $user_page_id;
my $page_name;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;

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

# print out the html page

util::header("User Page Tracking IDs");

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
			Select a page to edit or click Add to create a new page. To view the code to 
			place on your website to track visits to a page, click on the page name 
			to edit.</FONT></TD>
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
			<b>List of Current Pages</b></font></TD>
		</TR>
		<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="70%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Name</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="20%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Status</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="08%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
			<b>&nbsp;</b></font></td>
		</TR> 
end_of_html

# read info about the pages

$sql = "select user_page_id,page_name,status from user_page
	where user_id = $user_id order by page_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($user_page_id,$page_name,$status) = $sth->fetchrow_array())
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
 			<A HREF="user_page_edit.cgi?user_page_id=$user_page_id&mode=EDIT">$page_name</a>
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
				<a href="user_page_edit.cgi?mode=ADD">
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
