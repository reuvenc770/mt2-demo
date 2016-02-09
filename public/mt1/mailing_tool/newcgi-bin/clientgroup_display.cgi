#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_display.cgi
#
# this page is to Display an ClientGroup
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
my $client;
my $gid= $query->param('gid');
my $user_id;
my $gname;
my $images = $pms->get_images_url;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$pms->getUserData({'userID' => $user_id});

if($pms->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

# read info for this ClientGroup 

$sql = "select group_name from ClientGroup where $userDataRestrictionWhereClause client_group_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($gid);
($gname) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("Display Client Group");

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
		<tr><td><a href="clientgroup_list.cgi"><img src="$images/home_blkline.gif" border=0></a></td></tr>
		<TR><TD><IMG height=3 src="$images/spacer.gif"></TD></TR>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3>Group: <b>$gname</b></font></td> 
		</TR>
		<TR><TD><IMG height=3 src="$images/spacer.gif"></TD></TR>
		</TBODY>
		</TABLE>
<table border="0" width=40%>
	<tr><th>Client ID</th><th>Name</th></tr>
end_of_html
my $fname;
$sql="select client_id,username from ClientGroupClients,user u where client_group_id=(select client_group_id from ClientGroup where $userDataRestrictionWhereClause client_group_id=?) and ClientGroupClients.client_id=u.user_id order by username";
$sth=$dbhu->prepare($sql);
$sth->execute($gid);
while (($client,$fname)=$sth->fetchrow_array())
{
	print "<tr><td>$client</td><td>$fname</td></tr>";
}
$sth->finish();
print<<"end_of_html";
</table>
	</TD>
	</TR>
<tr><td><a href="clientgroup_list.cgi"><img src="$images/home_blkline.gif" border=0></a></td></tr>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=1>
end_of_html

$pms->footer();
exit(0);
