#!/usr/bin/perl
#===============================================================================
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;

my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my $username;
my $dataExportTool;
my $ctime;
my $cleanseData;
my $exportType=$query->param('exportType');
my $sql = "select username, dataExportTool,now(),cleanseData from UserAccounts where user_id = ?";
my $sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $dataExportTool,$ctime,$cleanseData) = $sth->fetchrow_array();
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
if (($cleanseData eq "N") and ($exportType eq "Cleanse"))
{
	open(LOG2,">>/tmp/export.log");
	print LOG2 "$ctime - $username - Cleanse\n";
	close(LOG2);
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Export Error</title></head>
<body>
<center><h3>You do not have permission to Cleanse Data.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
		exit();
}
my $pid=$query->param('pid');
#------  connect to the util database -----------
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/dataexport_edit.cgi?pid=$pid&exportType=$exportType" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
