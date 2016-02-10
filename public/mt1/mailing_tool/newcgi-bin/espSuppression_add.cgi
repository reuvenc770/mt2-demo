#!/usr/bin/perl
# *****************************************************************************************
# espSuppression_add.cgi
#
# this page adds advertisers to ExportSuppressionAdvertiser for an esp 
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
	my $exportData;
	my $ctime;
	$sql = "select username, exportData,now() from UserAccounts where user_id = ?";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute($user_id);
	($username, $exportData,$ctime) = $sth->fetchrow_array();
	$sth->finish();
	if ($exportData eq "N")
	{
		open(LOG2,">>/tmp/export.log");
		print LOG2 "$ctime - $username\n";
		close(LOG2);
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
<html><head><title>Export Error</title></head>
<body>
<center><h3>You do not have permission to setup Advertisers for Suppression.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
		exit();
	}

# get fields from the form

my $esp= $query->param('esp');
my @aid= $query->param('sel2');

$sql = "delete from ExportSuppressionAdvertiser where esp='$esp'"; 
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#aid)
{
	$sql = "insert ignore into ExportSuppressionAdvertiser(esp,advertiser_id) values ('$esp',$aid[$i])";
	$rows = $dbhu->do($sql);
	$i++;
}
print "Location: espSuppression.cgi?esp=$esp\n\n";
$pms->clean_up();
exit(0);
