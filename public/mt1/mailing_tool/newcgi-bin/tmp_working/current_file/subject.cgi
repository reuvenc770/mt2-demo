#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser subject data (eg 'advertiser_subject' table).
# Name   : subject.cgi (subject.cgi)
#
#--Change Control---------------------------------------------------------------
# 12/16/04  Jim Sobeck  Creation
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
my $aim;
my $website;
my $username;
my $password;
my $notes;
my $aid = $query->param('aid');
my $subject_str;
my $subject;

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
#
$sql = "select advertiser_subject from advertiser_subject where advertiser_id=$aid and status='A' order by advertiser_subject"; 
$sth = $dbh->prepare($sql);

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>From &amp; Subject Lines</title>
</head>
<body>
<p><b>Current Subjects: </b></br>
end_of_html
$sth->execute();
$subject_str = "";
while (($subject) = $sth->fetchrow_array())
{
	print "&nbsp;&nbsp;&nbsp;$subject<br>\n";
}
$sth->finish();
print<<end_of_html
<p><b>Subject: (Hit ENTER after each one) </b><br>
<form action="/cgi-bin/add_subject.cgi" method="post">
<input type=hidden name=aid value="$aid">
<textarea name="csubject" rows="7" cols="82"></textarea></p>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">&nbsp;&nbsp;
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
											Approved </b>
<input type="checkbox" name="aflag" size="40" maxlength="90" value="Y"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
											Original </b>
<input type="checkbox" name="oflag" size="40" maxlength="90" value="Y"></p>
</form>
</body>
</html>
end_of_html
