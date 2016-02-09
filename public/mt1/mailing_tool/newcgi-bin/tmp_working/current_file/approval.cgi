#!/usr/bin/perl
#===============================================================================
# Purpose: Send Approval Email 
# Name   : approval.cgi 
#
#--Change Control---------------------------------------------------------------
# 02/28/04  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $cemail;
my $aid = $query->param('aid');
my $tid;
my $url;

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
    srand(rand time());
    my @c=split(/ */, "bcdfghjklmnprstvwxyz");
    my @v=split(/ */, "aeiou");
    my $sname;
    $sname = $c[int(rand(20))];
    $sname = $sname . $v[int(rand(5))];
    $sname = $sname . $c[int(rand(20))];
    $sname = $sname . $v[int(rand(5))];
    $sname = $sname . $c[int(rand(20))];
    $sname = $sname . int(rand(999999));
    $sql = "insert into approval_list(advertiser_id,uid,date_added) values($aid,'$sname',now())";
    my $rows=$dbh->do($sql);
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>E-mail</title>
</head>

<body>
<form action="send_approval.cgi" method="post">
<input type=hidden name=aid value=$aid>
<input type=hidden name=uid value=$sname>
<b>To: </b><br>
<select name="cemail">
<option value="ALL">ALL </option>
end_of_html
$sql="select email_addr from advertiser_approval where advertiser_id=$aid order by email_addr"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($cemail) = $sth->fetchrow_array())
{
	print "<option value=$cemail>$cemail</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><br><b><br>
</select><input type="submit" value="Send" name="B21"><br></p>

<p><b>*</b>When individual creatives are selected, it will send the same test as the 
current system with the default from and subject (selected in the creative) + the following text @ 
the top and 
bottom similar to the mockup above:</p>
<hr>

<p class="MsoNormal" align="left">
This approval e-mail has been sent to you from Spire Vision.  Please visit our <a target="_blank" href="http://www.aspiremail.com/cgi-bin/advapproval.cgi?aid=$aid&uid=$sname">advertiser approval page</a> to view and approve the variations of your campaign (subject lines, from lines, tracking URLs).  You may also email Approval at <a href="mailto:approval\@spirevision.com">approval\@spirevision.com</a> or call 212.242.2451 with questions/concerns</p>
</form>
</body>

</html>
end_of_html
