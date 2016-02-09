#!/usr/bin/perl
# *****************************************************************************************
# login_form.cgi
#
# this page displays the login form
#
# History
# Grady Nash, 11/21/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use util;
my $util = util->new;
my $dbh;
my $sth;
my $sql;
my $ctitle;

#------ connect to the util database -------------------
$util->db_connect();
$dbh = $util->get_dbh;

$sql = "select parmval from sysparm where parmkey = 'SITENAME'";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($ctitle) = $sth->fetchrow_array();
$sth->finish();
$util->clean_up();

print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<title>$ctitle.com Mail Log On</title>
</head>
<body>

<FORM METHOD="POST" ACTION="login.cgi">

<TABLE cellSpacing=0 cellPadding=0 border=0>
<TR>
<TD colspan="2"><IMG height=3 src="/images/spacer.gif"></TD>
</TR>
<TR>
<TD colspan="2"><FONT face="verdana,arial,helvetica,sans serif" color=#509c10 size=3><b>
	New Mailing Tool Log On</b></font><br></TD>
</TR>
<TR>
<TD colspan="2"><IMG height=6 src="/images/spacer.gif"></TD>
</TR>
<TR>
<TD vAlign=center noWrap align=right>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>
	Username&nbsp;&nbsp</FONT></TD>
<TD vAlign=center align=left>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>
    <INPUT name=username size="20"></FONT></TD>
</TR>
<TR>
<TD colspan="2"><IMG height=7 src="/images/spacer.gif"></TD>
</TR>
<TR>
<TD vAlign=center noWrap align=right>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>
	Password&nbsp;&nbsp; </FONT></TD>
<TD vAlign=center align=left>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>
    <INPUT type=password name=password size="20"> </FONT></TD>
</TR>
<TR>
<TD colspan="2"><IMG height=6 src="/images/spacer.gif"></TD>
</TR>
<TR>
<TD align=middle colspan="2"><INPUT type=submit value="Log On"></td>
</tr>
</table>

</FORM>

<br><a href="forgot.cgi">Forgot Your Password?</a><br>

</body>
</html>
end_of_html

exit(0);
