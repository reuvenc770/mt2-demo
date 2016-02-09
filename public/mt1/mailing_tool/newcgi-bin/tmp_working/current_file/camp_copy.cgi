#!/usr/bin/perl

# *****************************************************************************************
# camp_copy.cgi
#
# this page is to copy a campaign
#
# History
# Grady Nash, 8/24/01, Creation
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
my $template_id;
my $campaign_id = $query->param('campaign_id');
my $campaign_name;
my $images = $util->get_images_url;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# get information for the campaign to be copied

$sql = "select campaign_name from campaign where campaign_id=$campaign_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($campaign_name) = $sth->fetchrow_array();
$sth->finish();

# print html page out

util::header("Copy a Campaign");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			You have selected the $campaign_name campaign to copy.  You need to give this 
			campaign a unique name.  This name is not displayed in 
        	your emails; it is for you to identify each unique mailing.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="camp_save.cgi" method=post>
		<INPUT type=hidden name="old_campaign_id" value="$campaign_id"> 
		<INPUT type=hidden name="template_id" value="$template_id"> 

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD>
			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>
                <TABLE cellSpacing=0 cellPadding=0 width=200 bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR align=top bgColor=#509C10 height=18>
                <TD vAlign=top align=left height=15>
					<IMG height=7 src="$images/blue_tl.gif" width=7 border=0></TD>
                <TD height=15>
					<IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15>
						<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Give the New Copy a Name</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD height=15>
					<IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                    <TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG height=7 src="$images/blue_tr.gif" width=7 border=0>
				</TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0>
				</TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD>
					<IMG height=3 src="$images/spacer.gif" width=3>
				</TD>
                <TD align=middle>
					<IMG height=3 src="$images/spacer.gif" width=3>
				</TD>
                <TD align=middle>
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR><TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
                    <TR>
                    <TD vAlign=center align=left>
					<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
					<INPUT maxLength=40 size=40 name=campaign_name value="$campaign_name"></FONT>
					</TD>
					</TR>
                    <TR>
                    <TR>
                    <TD vAlign=center align=left>
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Schedule Date: <select name=sdate><option selected value="">None</option><option value="0">Today</option><option value="1">Today+1</option><option value="2">Today+2</option><option value="3">Today+3</option><option value="4">Today+4</option><option value="5">Today+5</option><option value="6">Today+6</option></select></FONT>
					</TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
                    <TR>
                    <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
                    color=#ff0000 size=2>&nbsp;</FONT></TD></TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" 
                    width=3></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
                <TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
                    width=1 border=0><IMG height=3 src="$images/spacer.gif" 
                    width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" 
                    width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
                    width=7 border=0>
				</TD>
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
        <TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=right>
				<a href="mainmenu.cgi">
				<IMG hspace=7 src="$images/exit_wizard.gif" border=0 width="90" height="22"></a>
				<IMG height=1 src="$images/spacer.gif" width=340 border=0> 
				<INPUT type=image src="$images/next_arrow.gif" border=0> 
			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

	</TD></TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();
$util->clean_up();
exit(0);
