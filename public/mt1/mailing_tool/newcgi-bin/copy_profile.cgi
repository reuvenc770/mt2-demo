#!/usr/bin/perl

# *****************************************************************************************
# copy_profile.cgi
#
# this page is to copy a profile 
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
my $aid;
my $aname;
my $template_id;
my $images = $util->get_images_url;
my $profile_id=$query->param('pid');
my $mesg=$query->param('mesg');
my $pname;
my $tid;
my $client_id;
my $cname;
my $mname;
my $id;
my $temp_str;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}


# print html page out

util::header("Copy a Profile");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
end_of_html
if ($mesg ne "")
{
	print "<script language=javascript>alert('$mesg');</script>\n";
}
print<<"end_of_html";
		<FORM action="copy_profile_save.cgi" method=post>
		<input type=hidden name=pid value=$profile_id>
		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
end_of_html
$sql = "select profile_name,list_profile.third_party_id,client_id,company,mailer_name from list_profile,user,third_party_defaults where profile_id=$profile_id and user.user_id=list_profile.client_id and list_profile.third_party_id=third_party_defaults.third_party_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($pname,$tid,$client_id,$cname,$mname) = $sth->fetchrow_array();
$sth->finish();
#
print<<"end_of_html";
<tr>
<td><B>Profile Name</B></FONT></TD><td>$pname</td></TR>
<tr><td><B>Third Party Mailer</B></FONT></TD><td>$mname</td></TR>
<tr><td><B>Client</B></FONT></TD><td>$cname</td></TR>
<tr><td>&nbsp;</TD><td></td></TR>
<tr>
<td><B>New Profile Name</B></FONT></TD>
<td><input type=text name="profile_name" value="$pname" size=40 maxlength=40></td>
					</TR>
<tr><td><B>New Third Party Mailer</B></FONT></TD><td><select name=tid>
end_of_html
$sql = "select third_party_id,mailer_name from third_party_defaults order by mailer_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$temp_str) = $sth->fetchrow_array())
{
	if ($id == $tid)
	{
		print "<option value=$id selected>$temp_str</option>\n";
	}
	else
	{
		print "<option value=$id>$temp_str</option>\n";
	}
}
print<<"end_of_html";
</select></td></TR>
end_of_html
my $i=1;
while ($i <= 10)
{
print<<"end_of_html";
<tr><td><B>New Client $i</B></FONT></TD><td><select name=newcid$i><option selected value=0>None</option>
end_of_html
my $uid;
my $fname;
my $company;
$sql="select user_id,first_name,username from user where status='A' order by username";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($uid,$fname,$company) = $sth->fetchrow_array())
{
	print "<option value=$uid>$company ($fname)</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
end_of_html
$i++
}
print<<"end_of_html";
<tr>
                <TD align=middle><IMG height=5 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=5 src="$images/spacer.gif" width=3></TD></TR>
			<TR>
			<TD colspan=2 align=middle>
				<a href="/cgi-bin/listprofile_list.cgi?tflag=Y">
				<IMG src="$images/home.gif" border=0 ></a>
				<IMG height=1 src="$images/spacer.gif" width=340 border=0> 
				<INPUT type=image src="$images/copy.gif" border=0> 
			</TD>
			</TR>
			</TBODY>
			</TABLE>
		</FORM>

	</TD></TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();
$util->clean_up();
exit(0);
