#!/usr/bin/perl
# *****************************************************************************************
# camp_send_test.cgi
#
# this page sends a test email 
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
my $count;
my $sth;
my $sql;
my $dbh;
my $template_name;
my $campaign_id = $query->param('campaign_id');
my $campaign_name;
my $brand_id;
my $profile_id;
my $ptype;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# get campaign name

$sql = "select campaign_name,profile_id,brand_id from campaign where campaign_id=$campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($campaign_name,$profile_id,$brand_id) = $sth->fetchrow_array();
$sth->finish();

# print html page out

util::header("Send Email Test");

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
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>Select a Host, IP, and enter an email address.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
<script language="JavaScript">
function addIP(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.ip_addr.add(newOpt);
}

function update_ahost()
{
    var selObj = document.getElementById('hostname');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.ip_addr.length;
    while (selLength>0)
    {
        campform.ip_addr.remove(selLength-1);
        selLength--;
    }
    campform.ip_addr.length=0;
    parent.frames[1].location="/newcgi-bin/upd_ip.cgi?ahost="+selObj.options[selIndex].value;
}
</script>
		<FORM name=campform action=camp_send_test_save.cgi method=post target=_top>
		<INPUT type=hidden value=$campaign_id name=campaign_id> 
		<INPUT type=hidden value=$profile_id name=profile_id> 

		<TABLE cellSpacing=0 cellPadding=0 width=800 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
    		<TBODY>
    		<TR>
    		<TD align=middle>
			<table width=100%>
			<tr>
			<TD>Email Address: </td><td><input type=text size=50 maxlength=50 name=email_addr></td></tr>
end_of_html
my $pname;
$sql="select profile_name,profile_type from list_profile where profile_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($profile_id);
($pname,$ptype) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
			<tr><td>Profile: </td><td>$pname</td></tr>
			<tr><TD>Host: </td><td><select name=hostname onChange="update_ahost();">
end_of_html
if ($ptype != 'CHUNK')
{
	$sql="select distinct server_name from brand_host where brand_host.brand_id=$brand_id and server_type='T' and ip_addr != '' order by server_name"; 
}
else
{
#	$sql="select distinct server from server_config,list where list.server_id=server_config.id and list_id in (select list_id from list_profile_list where profile_id=$profile_id) order by 1";
	$sql="select distinct server_name from brand_host where brand_host.brand_id=$brand_id and server_type='T' and ip_addr != '' order by server_name"; 
}
#$sql="SELECT server FROM server_config WHERE inService=1 AND type='mailer' ORDER BY server ASC";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $server_name;
while (($server_name) = $sth->fetchrow_array())
{
	print "<option value=\"$server_name\">$server_name</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
			<tr><TD>IP Address: </td><td><select size=5 name=ip_addr multiple></select></td></tr>
			</table>
			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD width="50%" align="center">
				<a href="mainmenu.cgi"><img src="$images/cancel.gif" border=0></a></td>
			<td width="50%" align="center">
				<INPUT type=image src="$images/next.gif" border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

	</TD>
	</TR>
	</TBODY>
	</TABLE>
<script language="JavaScript">
update_ahost();
</script>
</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();

$util->clean_up();
exit(0);
