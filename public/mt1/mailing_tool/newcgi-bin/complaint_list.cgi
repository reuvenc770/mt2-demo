#!/usr/bin/perl
# *****************************************************************************************
# complaint_list.cgi
#
# this page displays the list of Complainers
#
# History
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
my $errmsg;
my $user_id;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $light_table_bg = $util->get_light_table_bg;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $parmkey;
my $parmval;
my $email;
my $rdate;
my $company;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# print out the html page

util::header("Complaint History");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
	<center>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF width="700">

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="4" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Complaint History</b></font></TD>
		</TR>
		<TR> 
		<TH bgcolor="#EBFAD1" align="left" width="40%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Email Addr</b></font></th>
		<TH bgcolor="#EBFAD1" align="left" width="30%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Removal Date</b></font></th>
		<TH bgcolor="#EBFAD1" align="left" width="30%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Network</b></font></th>
		</TR> 
end_of_html

# read info about the templates

$sql = "select manual_removal.email_addr,removal_date,company from manual_removal,user where client_id=user_id order by removal_date desc";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($email,$rdate,$company) = $sth->fetchrow_array())
{
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg" ;
    }
    else
    {
        $bgcolor = "$alt_light_table_bg" ;
    }

	$parmval =~ s\<\&lt;\g;
	$parmval =~ s\>\&gt;\g;

	print qq { 
		<TR bgColor=$bgcolor> 
		<TD align=left valign="top"><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$email</a></font></TD> 
		<TD align=left valign="top"><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$rdate</a></font></TD> 
		<TD align=left valign="top"><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$company</a></font></TD> 
		</tr>\n };
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=4><IMG height=10 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=4>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width="100%" align="center">
				<a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

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
<TD noWrap align=left height=17>
end_of_html

$util->footer();

# exit function

$util->clean_up();
exit(0);
