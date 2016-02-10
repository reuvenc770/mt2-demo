#!/usr/bin/perl
# *****************************************************************************************
# unique_attribution_super_add.cgi
#
# this page adds a new super client UniqueAttribution
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
my $rows;
my $errmsg;
my $userid;
my $dname;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
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
<center><h3>You do not have permission to set Client Attribution.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
	exit(0);
}

# get fields from the form

my $client_id= $query->param('cid');
my $profile_id= $query->param('pid');
my @ESP= $query->param('esp');
$sql="update sysparm set parmval='$client_id' where parmkey='SUPER_CLIENT_GROUP'";
$rows = $dbhu->do($sql);
open(LOG,">>/tmp/superclient.log");
print LOG "<$username> <$sql> <$user_id>\n";
close(LOG);

$sql="update sysparm set parmval='$profile_id' where parmkey='SUPER_CLIENT_PROFILE'";
$rows = $dbhu->do($sql);
$sql="update sysparm set parmval=now() where parmkey='SUPER_CLIENT_LASTUPD'";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating sysparm record $sql: $errmsg");
	exit(0);
}
$sql="update ESP set espSuperAttributionActive='N'";
$rows = $dbhu->do($sql);
foreach my $e (@ESP)
{
	$sql="update ESP set espSuperAttributionActive='Y' where espID=$e";
	$rows = $dbhu->do($sql);
}

print "Location: unique_attribution.cgi\n\n";
$pms->clean_up();
exit(0);
