#!/usr/bin/perl

# *****************************************************************************************
# getSubIDInfo_upd.cgi
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
my $errmsg;
my $user_id;
my $pid=$query->param('pid');
my $chunkSize=$query->param('chunkSize');
if ($chunkSize eq "")
{
	$chunkSize=0;
}
if ($pid eq "")
{
	$pid=0;
}
my $pname=$query->param('pname');
my $outname=$query->param('outname');

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
my $dataExportTool;
my $ctime;
my $BusinessUnit;
$sql = "select username, dataExportTool,now(),BusinessUnit from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $dataExportTool,$ctime,$BusinessUnit) = $sth->fetchrow_array();
$sth->finish();
if ($dataExportTool eq "N")
{
	open(LOG2,">>/tmp/export.log");
	print LOG2 "$ctime - $username\n";
	close(LOG2);
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Export Error</title></head>
<body>
<center><h3>You do not have permission to Export Data.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
		exit();
}
$sql="insert into GetSubIDInfo(fileName,output_fileName,status,lastUpdated,chunkSize) values('$pname','$outname','In Queue',curdate(),$chunkSize)";
my $rows=$dbhu->do($sql);
print "Location: getSubIDInfo_list.cgi\n\n";
