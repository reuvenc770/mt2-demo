#!/usr/bin/perl
# *****************************************************************************************
# sysparm_list.cgi
#
# this page displays the list of system parameters
#
# History
# Grady Nash, 11/09/01, Creation
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

util::header("System Parameters");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF width="660">

		<TABLE cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a parameter to edit</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="4" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>System Parameters</b></font></TD>
		</TR>
		<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="50%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
			<b>&nbsp;</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="30%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
			<b>&nbsp;</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="08%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
			<b>&nbsp;</b></font></td>
		</TR> 
end_of_html

# read info about the templates

$sql = "select parmkey, parmval from sysparm order by parmkey";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($parmkey,$parmval) = $sth->fetchrow_array())
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
		<td colspan="4" height="5"><img src="$images/spacer.gif" height="5"></td>
		</tr>
		<TR bgColor=$bgcolor> 
		<TD>&nbsp;</td> 
		<TD align=left valign="top">
			<font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"> 
 			<A HREF="sysparm_edit.cgi?parmkey=$parmkey">$parmkey</a></font></TD> 
		<TD align=left valign="top">
			<font color="#509C10" face="verdana,arial,helvetica,sans serif" size="1"> 
 			$parmval</font></TD> 
		<TD>&nbsp;</td> 
		<TR bgColor=$bgcolor> 
		<td colspan="4" height="5"><img src="$images/spacer.gif" height="5"></td>
		</tr>
 		</TR> \n };
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
