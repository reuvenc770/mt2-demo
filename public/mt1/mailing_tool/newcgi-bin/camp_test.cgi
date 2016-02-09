#!/usr/bin/perl

# *****************************************************************************************
# camp_test.cgi
#
# this page presents a page to allow the user to generate a test email for one campaign
#
# History
# Grady Nash, 8/15/01, Creation
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
my $campaign_id = $query->param('campaign_id');
my $images = $util->get_images_url;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# print out form

util::header("TEST EMAIL");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
	<TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left>&nbsp;</TD></TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD></TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Generate a Test Email.<BR></FONT>
		</TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD></TR>
		</TBODY>
		</TABLE>

		<FORM action="camp_test_gen.cgi" method=post>
		<INPUT type="hidden" name="campaign_id" value="$campaign_id">
		<INPUT type="hidden" name="cdeploy" value="N">

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
                <TD vAlign=top align=left height=15><IMG height=7 src="$images/blue_tl.gif" 
                    width=7 border=0></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15><FONT 
                        face=Verdana,Arial,Helvetica,sans-serif 
                        color=white size=2><B>To Email Address</B> 
                        </FONT></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG height=7 
                    src="$images/blue_tr.gif" width=7 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD></TR>
                <TR bgColor=#E3FAD1>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
                    <TR>
                    <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>
						<INPUT type="text" name="cemail" size="40">
						</FONT></TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
                    <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
                        color=#ff0000 size=2>&nbsp;</FONT></TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD></TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
                    width=1 border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
                    width=7 border=0></TD>
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
		<TD>&nbsp;</TD></TR>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=right>
			<INPUT type=submit value="Send Test Email"> 
			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

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
$util->clean_up();
exit(0);
