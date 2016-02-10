#!/usr/bin/perl

# *****************************************************************************
# add_partner_record.cgi
#
# this page allows adding a partner record 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $count;
my $sth;
my $sql;
my $dbh;
my $pid;
my $pname;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Partner Record</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<h4>This screen allows you to add a record to one of the Partners.</h4>
<p>
<FORM name="campform" action="add_partner_record_save.cgi" method=post>
<TABLE cellSpacing=0 cellPadding=2 bgColor=#ffffff border=0>
<TR>
<TD><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Partner</B></FONT></TD>
<td><select name=pid>
end_of_html
$sql="select partner_id,partner_name from PartnerInfo order by partner_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($pid,$pname)=$sth->fetchrow_array())
{
	print "<option value=$pid>$pname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
<TR>
<TD><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Client</B></FONT></TD>
<td><select name=clientid>
end_of_html
$sql="select distinct client_id,first_name from PartnerClientInfo pci, user u where pci.client_id=u.user_id and u.status='A' order by first_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($pid,$pname)=$sth->fetchrow_array())
{
	print "<option value=$pid>$pname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
<TR>
<TD><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Email Addr</B></FONT></TD>
<TD><INPUT maxLength=40 size=40 name=email_addr value=""></FONT></TD>
</TR>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>URL: </B></FONT></TD>
<TD><INPUT maxLength=255 size=40 name=url value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>IP: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=ip value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>First Name: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=fname value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Last Name: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=lname value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Address: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=addr value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Address 2: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=addr2 value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>City: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=city value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>State: </B></FONT></TD>
<TD><INPUT maxLength=80 size=40 name=state value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Zip: </B></FONT></TD>
<TD><INPUT maxLength=80 size=10 name=zip value=""></FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Gender: </B></FONT></TD>
<TD><INPUT type=radio name=gender value="M">Male&nbsp;&nbsp:<INPUT type=radio name=gender value="F">Female</FONT></TD>
</tr>
<tr><td><FONT face=Verdana,Arial,Helvetica,sans-serif size=2><B>Phone: </B></FONT></TD>
<TD><INPUT maxLength=80 size=12 name=phone value=""></FONT></TD>
</tr>
<tr><td colspan=2 align=middle><input type=submit name=submit value="Add Record"></td></tr>
</TABLE>
</FORM>
<center>
<a href="mainmenu.cgi" target=_top>Home</a>
</body>
</html>
end_of_html

$util->clean_up();
exit(0);
