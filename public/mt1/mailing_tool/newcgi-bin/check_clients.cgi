#!/usr/bin/perl
#===============================================================================
# Purpose: Checks to see if any new clients added for client_type 
# File   : check_clients.cgi
#
# Jim Sobeck	03/04/08	Creation	
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $bid = $query->param('bid');
my $dtype= $query->param('dtype');
my $bname;
my $client_type;
my $nl_name;
my $nl_id;
my ($sth, $reccnt, $sql, $dbh ) ;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$sql="select client_type,nl_name,cbi.nl_id,cbi.brand_name from client_brand_info cbi, newsletter where cbi.nl_id=newsletter.nl_id and cbi.brand_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($bid);
($client_type,$nl_name,$nl_id,$bname)=$sth->fetchrow_array();
$sth->finish();
print "Content-type:text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Client Brands</title>

<style type="text/css">
ul {
	list-style: none;
	padding: .5em;
}
</style>
</head>
<body>
end_of_html
if ($dtype eq "Y")
{
	print "<p><b>Clients of Data Type $client_type that lack a newsletter brand for $nl_name:</b></p>\n";
}
else
{
	print "<p><b>Clients that lack a newsletter brand for $nl_name:</b></p>\n";
}
print<<"end_of_html";
<form method=post action="add_client_brand.cgi">
<input type=hidden name=bid value=$bid>
<ul>
end_of_html
if ($dtype eq "Y")
{
	if ($client_type eq "ALL")
	{
		$sql="select user_id,company from user where status='A' and user_id not in (select client_id from client_brand_info where nl_id=$nl_id and brand_name='$bname') order by company";
	}
	else
	{
		$sql="select user_id,company from user where client_type='$client_type' and status='A' and user_id not in (select client_id from client_brand_info where nl_id=$nl_id and brand_name='$bname') order by company";
	}
}
else
{
	$sql="select user_id,company from user where client_type!='$client_type' and status='A' and user_id not in (select client_id from client_brand_info where nl_id=$nl_id and brand_name='$bname') order by company";
}
$sth=$dbhq->prepare($sql);
$sth->execute();
my $uid;
my $company;
while (($uid,$company)=$sth->fetchrow_array())
{
	print "<li><input type=checkbox name=chkbox value=$uid /> $company</li>\n";
}
$sth->finish();
print<<"end_of_html";
</ul>

<p>
<input type="submit" value="Add newsletter brand for selected clients">
</p>
</form>
</body>
</html>
end_of_html
