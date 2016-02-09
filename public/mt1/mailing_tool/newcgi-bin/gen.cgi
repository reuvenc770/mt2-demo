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
my $temp_url;
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
my $mid;
my $client_id;
my $input_client_id=$query->param('cid');
my $rows;
my $aid;
my $hitpath_id;
my $thitpath_id;
my $thirdparty_hitpath_id;
my @type = ( "N");

#------  connect to the util database -----------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Generate Tracking URLs</title>
</head>

<body>
end_of_html
$util->genLinks($dbhu,0,$input_client_id,1);
print<<"end_of_html";
<br>
<br>
<center>
<a href=/newcgi-bin/mainmenu.cgi>MainMenu</a>
</body>
</html>
end_of_html
