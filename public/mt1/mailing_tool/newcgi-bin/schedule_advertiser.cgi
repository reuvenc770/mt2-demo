#!/usr/bin/perl
#===============================================================================
# Name   : schedule_advertiser.cgi 
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
my $client_id;
my $slot_id;
my $cday;
my $third_id;
my $STRONGMAIL=10;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $stype= $query->param('stype');
my $xid= $query->param('xid');
my $startdate= $query->param('startdate');
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
($client_id,$slot_id,$cday)=split('_',$xid);
if ($stype eq "N")
{
	$third_id=$STRONGMAIL;
}
else
{
$sql = "select third_party_id from schedule_info where client_id=$client_id and slot_id=$slot_id and slot_type='$stype'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($third_id) = $sth->fetchrow_array();
$sth->finish();
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>Schedule Advertiser</h3>
<br>
<form method=get action="/cgi-bin/schedule_advertiser_save.cgi">
<input type=hidden name=xid value=$xid>
<input type=hidden name=stype value=$stype>
<input type=hidden name=startdate value=$startdate>
<table border=0 width=60%>
<tr><td align=right><b>Advertiser</b></td><td><select name=new_aid>
end_of_html
if ($third_id == $STRONGMAIL)
{ 
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' and allow_strongmail='Y' order by advertiser_name";
}
else
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' and allow_3rdparty='Y' order by advertiser_name";
}
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($taid,$aname) = $sth1->fetchrow_array())
{
	print "<option value=$taid>$aname</option>\n";
}
$sth1->finish;
print<<"end_of_html";
</select>
</td></tr>
<tr><td colspan=2 align=middle><input type=submit value="Schedule"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);
