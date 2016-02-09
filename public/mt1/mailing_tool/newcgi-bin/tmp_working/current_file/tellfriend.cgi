#!/usr/bin/perl
# *****************************************************************************************
# tellfriend.cgi
#
# sends friend email
#
# History
# Grady Nash, 10/23/2001, Creation
# *****************************************************************************************

use strict;
use CGI;
use util;
use util_mail;

my $util = util->new;
my $query = CGI->new;
my $sth;
my $dbh;
my $sql;
my $action;
my $rows;
my $errmsg;
my $program = "tellfriend.cgi";
my $email_user_id = $query->param('id');
my $campaign_id = $query->param('cid');
my $friend_email_addr = $query->param('femail');
my $format = "H"; # hardwire html format to the friend for now

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# send the friend an email for this campaign, just like a test email.

&util_mail::mail_sendtest ($dbh, $campaign_id, $friend_email_addr, $format, $email_user_id);

# print html page

print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<title>Tell A Friend</title>
</head>
<body>

<table cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<tr>
<td valign="top" align="left">

    <table border="0" cellpadding="0" cellspacing="0" width="660">
    <tr>
    <td><img border="0" src="/images/header.gif"></td>
    <td><b><font face="Arial" size="2">Tell A Friend</font></b></td>
    </tr>
    </table>

</td>
</tr>
<tr>
<td valign="top" align="left">

    <TABLE cellSpacing=0 cellPadding=10 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left>
			<B><FONT face="Arial" color=#509C10 size=2> Thank You.
			An email has been sent to $friend_email_addr</FONT></B></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="/images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=center height=17><br>
    <img border="0" src="/images/footer.gif"></TD>
</TR>
</TABLE>
</body>
</html>
end_of_html

$util->clean_up();
exit;
