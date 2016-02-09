#!/usr/bin/perl

# *****************************************************************************************
# del_mon_tag.cgi 
#
# *****************************************************************************************

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
my $template_name;
my $subject= $query->param('subject');
my $aid= $query->param('aid');
my $cid= $query->param('cid');
my $ctype= $query->param('ctype');
my $add_adv= $query->param('add_adv');
my $images = $util->get_images_url;

my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Check to see if campaign specified
#
# print html page out

util::header("Enter Delivery Monitor Tag");

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
        <TD>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Enter Delivery Monitor Tag that will be added to the mail message.
        	<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="emailreach_creative.cgi" method=post>
		<INPUT type=hidden name="subject" value="$subject"> 
		<INPUT type=hidden name="aid" value="$aid"> 
		<INPUT type=hidden name="cid" value="$cid"> 
		<INPUT type=hidden name="ctype" value="$ctype"> 
		<INPUT type=hidden name="add_adv" value="$add_adv"> 

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
					<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
					<B>Delivery Monitor Tag</B></FONT></TD>
                <TD height=15>
					<IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG height=7 src="$images/blue_tr.gif" width=7 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0>
				</TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD>
					<IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>
					<IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
					<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
                    <TR>
                    <TD vAlign=center align=left><FONT 
						face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT maxLength=50 size=50 name=header_tag value="">
						</FONT></TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" 
                    	width=3></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
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
        	<TR>
        	<TD>

				<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=right><a href="javascript:self.close();">
					<IMG hspace=7 src="$images/exit_wizard.gif" border=0 width="90" height="22"></a>
					<IMG height=1 src="$images/spacer.gif" width=340 border=0> 
					<INPUT type=image src="$images/next_arrow.gif" border=0></TD>
				</TR>
				</TBODY>
				</TABLE>

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
