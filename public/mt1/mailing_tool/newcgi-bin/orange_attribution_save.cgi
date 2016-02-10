#!/usr/bin/perl

# *****************************************************************************************
# orange_attribution_save.cgi
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
my $username;
my $setAttribution;
my $ctime;
$sql = "select username, setAttribution,now() from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $setAttribution,$ctime) = $sth->fetchrow_array();
$sth->finish();
if ($setAttribution eq "N")
{
	open(LOG2,">>/tmp/attribution.log");
    print LOG2 "$ctime - $username\n";
    close(LOG2);
    print "Content-type: text/html\n\n";
    print<<"end_of_html";
<html><head><title>Attribution Error</title></head>
<body>
<center><h3>You do not have permission to set Attribution.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
	exit(0);
}
my $cid;
$sql="select user_id from user where status='A' and OrangeClient='Y'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($cid)=$sth->fetchrow_array())
{
	my $fld="cid_".$cid;
	my $val=$query->param($fld);
	$val=$val || 255;
	$sql="update user set AttributeLevel=$val where user_id=$cid";
	my $rows=$dbhu->do($sql);
}
$sth->finish();
print "Location: /cgi-bin/orange_attribution.cgi?pmesg='Attribution Levels Updated'\n\n";
