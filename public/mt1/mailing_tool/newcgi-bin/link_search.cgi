#!/usr/bin/perl

# *****************************************************************************************
# link_search.cgi
#
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
my $refurl;
my $bgcolor;
my $reccnt;
my $aname;
my $sth1;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;
my $link_id=$query->param('link_id');

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

util::header("Redirect Search");

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
			Select a redirect to edit </FONT></TD>
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
			<b>List of Redirects </b></font></TD>
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
		<TD bgcolor="#EBFAD1" align="left" width="40%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Advertiser</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="8%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
			<b>&nbsp;</b></font></td>
		</TR> 
end_of_html

# read info about the lists

$sql = "select link_id,refurl from links where refurl like '%$link_id%' order by link_id desc";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($link_id,$refurl) = $sth->fetchrow_array())
{
    $sql="select advertiser_name from advertiser_info,advertiser_tracking where advertiser_info.advertiser_id=advertiser_tracking.advertiser_id and link_id=?";
    $sth1=$dbhq->prepare($sql);
    $sth1->execute($link_id);
    ($aname)=$sth1->fetchrow_array();
    $sth1->finish();
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
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$refurl</font></TD> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2">http://{{SUB_DOMAIN}}/cgi-bin/redir1.cgi?cid={{CID}}&eid={{EMAIL_USER_ID}}&id=$link_id</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$aname</font></TD> 
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
