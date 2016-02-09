#!/usr/bin/perl

# *****************************************************************************************
# list_refurl.cgi
#
# this page displays the list of redirects and lets the user edit / add
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
my $user_id;
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;
my $INDIQUEST_USER = 4;

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
my $csite;
$sql = "select website_url from user where user_id=$user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($csite) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("Redirects");

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
		<tr>
		<td><form method=POST action=link_edit.cgi><input type=hidden name="mode" value="EDIT">Enter old ID to lookup: <input type=text name=link_id size=5 maxlength=5>&nbsp;&nbsp;&nbsp;<input type=submit value="Lookup"></form>
		</td>
<td width=20% align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
            <td width="20%" align="center" valign="top">
                <a href="link_edit.cgi?mode=ADD">
                <img src="$images/add.gif" border=0></a></td>
		</tr>
		<tr>
		<td colspan=3><form method=post action=link_search.cgi><input type=hidden name="mode" value="EDIT">Enter URL to lookup: <input type=text name=link_id size=80 maxlength=80>&nbsp;&nbsp;&nbsp;<input type=submit value="Lookup"></form>
		</td>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a redirect to edit or click Add to create a new redirect</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="5" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>List of Redirects in Last 30 days</b></font></TD>
		</TR>
		<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="2%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="10%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>ID</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="40%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Redirect</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="40%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>URL</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="8%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
			<b>&nbsp;</b></font></td>
		</TR> 
end_of_html

# read info about the lists

$sql = "select link_id,refurl from links where date_added >= date_sub(curdate(),interval 1 month) order by link_id desc";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($link_id,$refurl) = $sth->fetchrow_array())
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

	print qq { <TR bgColor=$bgcolor> 
		<TD>&nbsp;</td> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			<A HREF="link_edit.cgi?link_id=$link_id&mode=EDIT">$link_id</a>
			</font></TD> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			$refurl</font></TD> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2">http://{{SUB_DOMAIN}}/cgi-bin/redir1.cgi?cid={{CID}}&eid={{EMAIL_USER_ID}}&id=$link_id</font></TD> 
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
				<a href="link_edit.cgi?mode=ADD">
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
