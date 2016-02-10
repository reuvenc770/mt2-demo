#!/usr/bin/perl

# ******************************************************************************
# master_schedule_copy.cgi
#
# this page is to copy a schedule
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $images = $util->get_images_url;
my $company;
my $network_id;

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

# print html page out
print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head><title>Master Schedule Client Selection</title></head>
<script language="JavaScript">
function selectall()
{
    refno=/network/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/network/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
</script>
<center>
<body>
<h3>Select Clients to Copy Daily</h3>
<br>
<form method=post name=adform action="/cgi-bin/master_schedule_copy_save.cgi">
<table width=50%>
<tr><td vAlign="center" align="middle" colSpan="2"><input onclick="selectall()" type="button" value="Select All" name="SelectAll2"><input onclick="unselectall()" type="button" value="UnSelect All" name="UnSelectAll0"></td>
<tr><td align=middle colspan=3><b>Network</b></td></tr>
end_of_html
$sql = "select user_id,company from user,network_schedule where user.status='A' and user.user_id=network_schedule.client_id order by company"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $company_str="";
my $reccnt=0;
my $cnt;
print "<tr>";
while (($network_id,$company) = $sth->fetchrow_array())
{
	$sql="select count(*) from CopyScheduleClient where client_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($network_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt == 1)
	{
		print "<td><input checked type=checkbox name=network value=$network_id>$company</td>";
	}
	else
	{
		print "<td><input type=checkbox name=network value=$network_id>$company</td>";
	}
	$reccnt++;
	if ($reccnt == 3)
	{
		print "</tr><tr>";	
		$reccnt=0;
	}
}
$sth->finish();
print<<"end_of_html";
<tr><td>&nbsp;</td></tr>
</table>
<a href="/cgi-bin/mainmenu.cgi" target=_top><img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"><input type="image" src="/images/save.gif" border="0" name="I1">
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
