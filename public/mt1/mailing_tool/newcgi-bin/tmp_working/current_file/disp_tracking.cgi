#!/usr/bin/perl
#===============================================================================
# Purpose: Display tracking info before deleting 
# Name   : disp_tracking.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/04/05  Jim Sobeck  Creation
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
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $url;
my $code;
my $aid;
my $tid = $query->param('tid');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;
$sql="select advertiser_id,url,code,company from advertiser_tracking,user where tracking_id=$tid and client_id=user.user_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($aid,$url,$code,$company) = $sth->fetchrow_array();
$sth->finish();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Delete Tracking</title>
</head>

<body>
<b>Delete Tracking: </b><br>
<form method=post action="/cgi-bin/del_tracking.cgi">
<input type=hidden name=tid value=$tid>
<input type=hidden name=aid value=$aid>
<br><b>List Name:</b>&nbsp;&nbsp;$company<br>
<br><b>URL:</b>&nbsp;&nbsp;$url<br>
<b>Code: (to tie it back to the network - stat purposes)</b>&nbsp;&nbsp;$code<br>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">&nbsp;&nbsp;<a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid"><img src="/images/cancel_blkline.gif" border=0></a></p>
</form>
</body>
</html>
end_of_html
