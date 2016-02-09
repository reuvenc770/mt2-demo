#!/usr/bin/perl
#===============================================================================
#--Change Control---------------------------------------------------------------
# 05/02/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $notes;
my $url;
my $code;
my $hitpath_id;
my $thirdparty_hitpath_id;
my $mid;
my $client_id;
my $rows;
my $aid = $query->param('aid');
my $ctype = $query->param('ctype');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$util->genLinks($dbhu,$aid,0);
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Tracking</title>
</head>

<body>
<table width=80%><tr><th align=center>Client</th><th align=center>URL</th><th align=center>Code</th></tr>
end_of_html
$sql="select url,code,company from advertiser_tracking,user where advertiser_tracking.client_id=user.user_id and advertiser_id=$aid and daily_deal='$ctype'";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($url,$code,$company) = $sth->fetchrow_array())
{
	print "<tr><td>$company</td><td>$url</td><td>$code</td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
<table>
<br><br>
<a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid"><img src="/images/cancel.gif" border=0></a></p> </body>
</html>
end_of_html
