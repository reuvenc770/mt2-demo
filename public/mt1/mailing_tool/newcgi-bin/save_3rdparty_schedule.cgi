#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of 3rdparty_view_schedule.cgi page 
# Name   : save_3rdparty_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 10/13/05  Jim Sobeck  Creation
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
my $rows;
my $camp_id;
my $dbh;
my $phone;
my $email;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $tables;
my $startdate = $query->param('startdate');
my $advertiser_name;
my $sdate;
my $sdate1;
my $cname;
my $edate;
my $cdate;
my $startdate1;
my $cday;
my $slot_id;
my $tday;
my $client_id;
my $stime;
my $profile_id;
my $brand_id;
my $creative1;
my $subject1;
my $from1;
my $send_email;
my $from_addr;
my $suppid;
my $supp_name;
my $last_updated;
my $filedate;
my $daycnt;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>View Schedule</title>
</head>
<body>
end_of_html
#
my @chkboxs= $query->param('chkbox');
foreach my $chkbox (@chkboxs) 
{
	$sql = "update camp_schedule_info set status='D' where campaign_id=$chkbox";
	$rows=$dbhu->do($sql);
	$sql = "update campaign set deleted_date=now() where campaign_id=$chkbox and status='C'"; 
	$rows=$dbhu->do($sql);
}
print<<"end_of_html";
<center>
<h2>Schedule successfully Updated</h2>
<br>
<a href="/cgi-bin/mainmenu.cgi" target=_top>Home</a>
</body>
</html>
end_of_html
