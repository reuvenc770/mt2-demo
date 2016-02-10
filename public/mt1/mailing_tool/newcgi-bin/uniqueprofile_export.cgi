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
my $sth;
my $sth1;
my $sql;
my $dbh;
my $filename;
my $errmsg;
my $images = $util->get_images_url;

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $cid= $query->param('cid');
$filename=$user_id."_".$cid.".csv";
open(LOG,">/data3/3rdparty/$filename");
print LOG "ISP,Username,Client ID,Records\n";
$sql="select u.user_id,u.username,ed.class_id,class_name,reccnt from user u,email_class ed, UniqueCheckIsp where ed.class_id=UniqueCheckIsp.class_id and UniqueCheckIsp.check_id=$cid and UniqueCheckIsp.client_id=u.user_id and ed.status='Active' order by class_name,u.username";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $class_id;
my $cname;
my $reccnt;
my $fname;
my $client_id;
while (($client_id,$fname,$class_id,$cname,$reccnt)=$sth->fetchrow_array())
{
	print LOG "$cname,$fname,$client_id,$reccnt\n";
}
$sth->finish();
close(LOG);
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<body>
<center>
<h4><a href="/downloads/$filename">Click here</a> to download file</h4>
<br>
</center>
<br>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
