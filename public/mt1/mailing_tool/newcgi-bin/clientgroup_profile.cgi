#!/usr/bin/perl

# *****************************************************************************************
# clientgroup_profile.cgi
#
# this page displays the list of clients and profiles in a category group 
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
my $user_id;
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $status_name;
my $status;
my $cat_id;
my $category_name;
my $domain_name;
my $group_id = $query->param('group_id');

my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# print out the html page

util::header("Client Group Client's Profiles");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<form method=post action=clientgroup_profile_save.cgi>
	<input type=hidden name=group_id value=$group_id>
<center>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=760 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
<td align="center" valign="top"><a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			For each client specify a Profile</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="50%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD align=center height=15><font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3"><b>Client</b></font></TD>
		<TD align=center height=15><font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3"><b>Profile</b></font></TD>
		</TR>
end_of_html

# read info about the lists
my $cid;
my $name;
my $pid;
$sql = "select user_id, username, cgc.profile_id from user,ClientGroupClients cgc where cgc.client_id=user.user_id and cgc.client_group_id=$group_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$name,$pid) = $sth->fetchrow_array())
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

	print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$name&nbsp&nbsp;</font></TD><TD><select name=pid_$cid>}; 
	$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	my $tid;
	my $tname;
	while (($tid,$tname)=$sth1->fetchrow_array())
	{
		if ($tid == $pid)
		{
			print "<option value=$tid selected>$tname</option>";
		}
		else
		{
			print "<option value=$tid>$tname</option>";
		}
	}
	$sth1->finish();
 	print "</select></td></TR>";;
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<tr><td colspan=2><input type=submit value="Save"></td></tr>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
