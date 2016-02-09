#!/usr/bin/perl
# *****************************************************************************************
# orange_attribution_add.cgi
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
my $level = $query->param('level');
$sql = "update user set AttributeLevel=$level where user_id=$client_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting Unique Attribution record $sql: $errmsg");
	exit(0);
}
$sql="update user set AttributeLevel=AttributeLevel+1 where AttributeLevel >= $level and OrangeClient='Y' and AttributeLevel != 255 and user_id != $client_id";
$rows = $dbhu->do($sql);
print "Location: orange_attribution.cgi\n\n";
$pms->clean_up();
exit(0);
