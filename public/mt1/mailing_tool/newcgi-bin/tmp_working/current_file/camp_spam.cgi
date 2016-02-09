#!/usr/bin/perl

# *****************************************************************************************
# camp_spam.cgi
#
# this page is for sending a spamassasin report of a campaign
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
my $userid;
my $email_addr;
my $rows;
my $errmsg;
my $campaign_id = $query->param('campaign_id');
my $aid;
my $id;
my $user_id;
my $campaign_name;
my $created_datetime;
my $subject;
my $image_url;
my $title;
my $subtitle;
my $greeting;
my $introduction;
my $list_name;
my $status;
my $status_name;
my $scheduled_date;
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

# read the campaigns info to fill in the form

$sql = "select creative_name,default_subject,status,creative_date,advertiser_id
	from creative where creative_id = $campaign_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($campaign_name,$subject,$status,$created_datetime,$aid) = $sth->fetchrow_array();
$sth->finish();

# make pretty status for the screen

if ($status eq "D") 
{
	$status_name = "Draft";
}
elsif ($status eq "S")
{
	$status_name = "Scheduled";
}
elsif ($status eq "P")
{
	$status_name = "Pending";
}
elsif ($status eq "C")
{
	$status_name = "Complete";
}

# print out the html page

util::header("Send Spamassassin Report for Campaign");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Send Spam Assassin Report</B> 
            </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
    	<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
    		size=2>These are the current settings for this Campaign.
    		<BR></FONT></TD>
		</TR>
		<TR>
    	<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
    	<TD>

    		<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
    		<TBODY>
    		<TR>
        	<TD align=middle>

        		<TABLE cellSpacing=0 cellPadding=0 width=450 bgColor=#E3FAD1 border=0>
        		<TBODY>
        		<TR align=top bgColor=#509C10 height=18>
        		<TD vAlign=top align=left height=15><IMG 
            		src="$images/blue_tl.gif" border=0 width="7" height="7"></TD>
            	<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
            	<TD align=middle height=15>

		            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
       			    <TBODY>
            		<TR bgColor=#509C10 height=15>
                	<TD height=15></TD></TR>
					</TBODY>
					</TABLE>

				</TD>
	            <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
            	<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
            		src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
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
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
                    <TR>
                    <TD vAlign=center noWrap align=right width="35%"><FONT 
                        face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>Name:&nbsp;&nbsp;&nbsp; 
                        </FONT></TD>
                    <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>$campaign_name</FONT> </TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
                    <TR>
                    <TD vAlign=top noWrap align=right>
						<FONT face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>Who:&nbsp;&nbsp;&nbsp; 
                        </FONT></TD>
					<TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Opt-In subscribers who have joined one or more of the following 
						Interest Groups:
						<ul>
end_of_html

$sql = "select list_name from campaign_list, list where campaign_list.list_id = list.list_id and campaign_list.campaign_id = $campaign_id order by list_name";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_name) = $sth->fetchrow_array())
{
	print "<li>$list_name\n";
}
$sth->finish();

print "</ul>\n";

print << "end_of_html";
                    </TD>
					</TR>
                    <TR>
					<TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
                    <TR>
                    <TD vAlign=center noWrap align=right><FONT 
                        face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>When:&nbsp;&nbsp;&nbsp; 
                        </FONT></TD>
                    <TD vAlign=center align=left><FONT 
                        face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>$created_datetime</FONT> 
                    </TD>
					</TR>
                    <TR><TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
                    <TR> <TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
                    <TR>
                    <TD vAlign=center noWrap align=right><FONT 
                        face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>Status:&nbsp;&nbsp;&nbsp; 
                        </FONT></TD>
                    <TD vAlign=center align=left><FONT 
                        face="verdana,arial,helvetica,sans serif" 
                        color=#509C10 size=2>$status_name</FONT> 
                        </TD>
					</TR>
                    <TR> <TD><IMG height=7 src="$images/spacer.gif"></TD></TR> 
					<TR> <TD><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
					<tr>
					<td colspan=2 align=center><form method="post" action="send_spam.cgi">
					<input type=hidden name=cid value=$campaign_id>
					<input type=hidden name=aid value=$aid>
					Email Addr: <input type=text maxlength=255 size=80 name=cemail value="mdimaio\@spirevision.com,eneuner\@spirevision.com,david\@spirevision.com"><br>
					<input type=submit value="Send Report">
					</form>
					</td></tr>
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
                    width=1 border=0><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=#509C10 size=2> </FONT><IMG height=3 src="$images/spacer.gif" 
                    width=1 border=0></TD>
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
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<td width="25%" align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></td>
			<td width="25%" align="center">
				<a href="camp_step5.cgi?campaign_id=$campaign_id">
				<img src="$images/wizard.gif" border=0></a></td>
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
