#!/usr/bin/perl
#===============================================================================
# Name   : footer.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/14/05  Jim Sobeck  Creation
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
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Footer Variation</title>
</head>
<body>
<form action="/cgi-bin/footer_ins.cgi" method="post">
<table width=50%>
<tr><td width=20%>Name:</td><td><input type=text name=vname maxlength=50></td></tr>
<tr><td>Unsub:</td><td><textarea name=unsub_text cols=82 rows=5></textarea></td></tr>
<tr><td>Privacy:</td><td><textarea name=privacy_text cols=82 rows=5></textarea></td></tr>
</table>
<p>
<center>
<a href="/cgi-bin/footer_list.cgi"><img src="/images/cancel.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
