#!/usr/bin/perl

# ******************************************************************************
# 3rdparty_schedule_copy.cgi
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
my $dbh;
my $images = $util->get_images_url;
my $company;
my $network_id;
my $sdate;
my $edate;
my $tdate;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# get information for the campaign to be copied

$sql = "select date_sub(curdate(),interval dayofweek(curdate())-1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($sdate) = $sth->fetchrow_array();
$sth->finish();
$sql = "select date_add('$sdate',interval 6 day),date_add('$sdate',interval 7 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($edate,$tdate) = $sth->fetchrow_array();
$sth->finish();

# print html page out
print "Content-type: text/plain\n\n";
print << "end_of_html";
<html>
<head><title>Schedule Copy</title></head>
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
function selectall3()
{
    refno=/third_party/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall3()
{
    refno=/third_party/;
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
<h3>Copy A Schedule</h3>
<br>
<form method=post name=adform action="/cgi-bin/3rdparty_schedule_copy_save.cgi">
<table width=50%>
<tr><td><b>Start Date</b></td><td><input type=text name=sdate size=10 maxlength=10 value="$sdate"><td><b>End Date</b></td><td><input type=text name=edate size=10 maxlength=10 value="$edate"></td></tr>
<tr><td><b>To Date</b></td><td><input type=text name=tdate size=10 maxlength=10 value="$tdate"></td></tr>
<tr><td vAlign="center" align="middle" colSpan="2"><input onclick="selectall()" type="button" value="Select All" name="SelectAll2"><input onclick="unselectall()" type="button" value="UnSelect All" name="UnSelectAll0"></td>
<td vAlign="center" align="middle" colSpan="2"><input onclick="selectall3()" type="button" value="Select All" name="SelectAll2"><input onclick="unselectall3()" type="button" value="UnSelect All" name="UnSelectAll0"></td></tr>
<tr><td colspan=2 align=middle><b>Network</b></td><td colspan=2 align=middle><b>3rd Party</b></td></tr>
end_of_html
$sql = "select user_id,company from user where status='A' and user_id in (select client_id from network_schedule) order by company"; 
$sth = $dbh->prepare($sql);
$sth->execute();
my $company_str="";
while (($network_id,$company) = $sth->fetchrow_array())
{
	$company_str = $company_str . "<input type=checkbox name=network value=$network_id>$company<br>";
}
$sth->finish();
$sql = "select third_party_id,mailer_name from third_party_defaults order by mailer_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $party_str="";
while (($network_id,$company) = $sth->fetchrow_array())
{
	$party_str= $party_str . "<input type=checkbox name=third_party value=$network_id>$company<br>";
}
$sth->finish();
print<<"end_of_html";
<tr><td colspan=2 valign=top>$company_str</td><td colspan=2 valign=top>$party_str</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
<a href="/cgi-bin/mainmenu.cgi" target=_top><img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"><input type="image" src="/images/save.gif" border="0" name="I1">
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
