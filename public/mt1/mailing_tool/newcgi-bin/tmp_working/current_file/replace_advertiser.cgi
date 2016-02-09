#!/usr/bin/perl
#===============================================================================
# Name   : replace_advertiser.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $sth1;
my $aname;
my $taid;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $aid= $query->param('aid');
#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($aname) = $sth1->fetchrow_array();
$sth1->finish;

$util->clean_up();
print "Content-Type: text/plain\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>Replace Advertiser</h3>
<br>
<form method=get action="/cgi-bin/replace_advertiser_save.cgi">
<input type=hidden name=aid value=$aid>
<table border=0 width=60%>
<tr><td><b>Replace Advertiser</b></td><td>$aname</td></tr>
<tr><td align=right><b>With</b></td><td><select name=new_aid>
end_of_html
$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($taid,$aname) = $sth1->fetchrow_array())
{
	print "<option value=$taid>$aname</option>\n";
}
$sth1->finish;
print<<"end_of_html";
</select>
</td></tr>
<tr><td colspan=2 align=middle><input type=submit value="Replace"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);

