#!/usr/bin/perl
# *****************************************************************************************
# unsubscribe_save.cgi
#
# this page saves the users "unsubscribe" requests
#
# History
# Grady Nash, 8/17/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $email_user_id = $query->param('email_user_id');
my $campaign_id = $query->param('campaign_id');
my $unsub_option = $query->param('unsub_option');
my $list_id;
my $iopt;
my $rows;
my $images = $util->get_images_url;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check unsubscribe option 

if ($unsub_option eq "ALL LISTS")
{
	# unsubscribe to All Lists - look at each list and see if the checkbox is checked

	$sql = "select list_member.list_id from list,list_member
    	where list_member.email_user_id = $email_user_id and
    	list_member.status='A' and list.status='A' and
    	list_member.list_id = list.list_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($list_id) = $sth->fetchrow_array())
	{
		$iopt = $query->param("list_$list_id");
		if (!$iopt)
		{
			$sql = "update list_member set status = 'U', unsubscribe_datetime = now()
				where email_user_id = $email_user_id and list_id = $list_id";
			$rows = $dbh->do($sql);
		}
	}
	$sth->finish();
}
else
{
	# unsubscribe to just one list - actually unsubscribe to any lists
	# assigned to the campaign id passed in 

	$sql = "select list_id from campaign_list where campaign_id = $campaign_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($list_id) = $sth->fetchrow_array())
	{
		$sql = "update list_member set status = 'U', unsubscribe_datetime = now()
			where email_user_id = $email_user_id and list_id = $list_id";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
}


# print out the html page

print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unsubscribe</title>
</head>
<body>
<table cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<tr>
<td valign="top" align="left">
    <table border="0" cellpadding="0" cellspacing="0" width="660">
    <tr>
    <td><img border="0" src="/images/header.gif"></td>
    <td><b><font face="Arial" size="2">Unsubscribe</font></b></td>
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
        <TD><IMG height=3 src="/images/spacer.gif"></TD>
        </TR>
        <TR>
        <TD vAlign=center align=left>
            <B><FONT face="Arial" color=#509C10 size=2>
            Thank You!  You are now Unsubscribed</FONT></B></TD>
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
exit(0);
