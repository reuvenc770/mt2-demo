#!/usr/bin/perl
# *****************************************************************************************
# deploypool_edit.cgi
#
# this page is to edit a DeployPool
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
my $pid= $query->param('pid');
my $user_id;
my $gname;
my $client_group_id;
my $profile_id;
my $recs;
my $images = $pms->get_images_url;
my $pname;

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

$sql = "select deployPoolName,client_group_id,profile_id,recsPerChunk from DeployPool where deployPoolID=?";
$sth = $dbhq->prepare($sql);
$sth->execute($pid);
($pname,$client_group_id,$profile_id,$recs) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("Edit Deploy Pool");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

		<FORM action="deploypool_upd.cgi" method="post">
		<input type="hidden" name="pid" value="$pid">
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Pool Name:</td><td><input type=text size=20 name=pname value="$pname"></td></tr>
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Client Group:</td><td><select name=gid>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cid;
my $cname;
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cid == $client_group_id)
	{
    	print "<option selected value=$cid>$cname</option>";
	}
	else
	{
    	print "<option value=$cid>$cname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Profile:</td><td><select name=profileid>
end_of_html
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $pid;
my $pname;
while (($pid,$pname)=$sth->fetchrow_array())
{
	if ($profile_id == $pid)
	{
    	print "<option selected value=$pid>$pname</option>";
	}
	else
	{
    	print "<option value=$pid>$pname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Chunk Size:</td><td><input type=text size=10 name=recs value="$recs"></td></tr>
		<TR><TD align=center><input type=submit value="Update Pool"></td><td><a href="mainmenu.cgi"> <img src="$images/home_blkline.gif" border=0></a></TD></tr>

		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>
</form>

</TD>
</TR>
<TR>
<TD noWrap align=left height=1>
end_of_html

$pms->footer();
exit(0);
