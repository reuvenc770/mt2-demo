#!/usr/bin/perl

# *****************************************************************************************
# camp_step1.cgi
#
# this page is the first step in the email campaign creation process
# select a template step
#
# History
# Grady Nash, 7/30/01, Creation
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
my $template_name;
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

# print out form

util::header("CREATE EMAIL");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left>&nbsp;</TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Please select a template.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD></TR>
		</TBODY>
		</TABLE>

		<FORM name="campform" action="camp_step2.cgi" method=post>

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
                        color=white size=2><B>Template Name</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG height=7 
                    src="$images/blue_tr.gif" width=7 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
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
						<select name="template_id">
end_of_html

$sql = "select template_id,template_name from template where status='A' order by template_name";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($template_id,$template_name) = $sth->fetchrow_array())
{
	print "<option value=\"$template_id\">$template_name</option>\n";
}
$sth->finish();

print << "end_of_html";
						</select></FONT></TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
					<script language="Javascript">
					function OpenPreview()
					{
						var field = document.campform.template_id;
						var template_id = field.options[field.options.selectedIndex].value;
						var newwin = window.open("template_preview.cgi?template_id=" + template_id, "Preview", "toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=800,height=500,left=50,top=50");
    					newwin.focus();
					}
					</script>
                    <TR>
                    <TD vAlign=center align=middle>
						<a href="JavaScript:OpenPreview();">
                        <img src="$images/preview_rev.gif" border=0></a></TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD></TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
                    width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
                    width=1 border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" width=7 
                    border=0></TD>
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
		<TD>&nbsp;</TD>
		</TR>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=right>
				<a href="mainmenu.cgi"><IMG hspace=7 src="$images/exit_wizard.gif" 
				border=0 width="90" height="22"> </a>
				<IMG height=1 src="$images/spacer.gif" width=340 border=0> 
				<a href="mainmenu.cgi"><img src="$images/previous_arrow.gif" border=0></a>
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
