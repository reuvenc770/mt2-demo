#!/usr/bin/perl
# *****************************************************************************************
# camp_history.cgi
#
# this page displays the snapshot report 
#
# History
# Jim Sobeck, 8/03/01, Creation
# Jim Sobeck, 04/08/02, Modified to use info in campaign table
# Jim Sobeck, 11/11/02, Added full mailbox count
# Jim Sobeck, 02/02/2005, Modified to handle new table layout
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
my $ecpm;
my $offer_type;
my $campaign_name;
my $campaign_id = $query->param('campaign_id');
my $sent_datetime;
my $action;
my $cnt;
my $datestr;
my $this_month_cnt;
my $last_month_cnt;
my $total_subscribe_cnt;
my $total_sent_cnt;
my $click_cnt;
my $payment_per_conversion;
my $total_conversions;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $aid;
my ($sent_cnt, $opened_cnt, $clicked_cnt,$uns_cnt,$bounce_cnt,$full_cnt,$aol_cnt,$opened_percent);
my $click_percent;
my $sth1;
my $status;
my $user_type;

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
$sql = "select user_type from user where user_id = $user_id";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($user_type) = $sth->fetchrow_array();
$sth->finish();

# print out html page

util::header("CAMPAIGN HISTORY");

print <<"end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=white>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0>
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10><!-- doing ct-table-open -->

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Campaign History Report</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>This report includes the results for the selected Campaign.<BR></FONT></TD>
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

            <TABLE cellSpacing=0 cellPadding=0 width="660" border=0>
            <TBODY>
            <TR>
            <TD width=64></TD>
            <TD width="592">&nbsp;</TD>
			</TR>
            <TR>
            <TD align=middle colSpan=2 width="658"><FONT face=Verdana,Arial,Helvetica,sans-serif 
                color=#509C10 size=3><B>Campaign History</B></FONT></TD>
			</TR>
            <TR>
            <TD colSpan=2 width="658"><IMG height=7 src="$images/spacer.gif" border=0></TD>
			</TR>
            <TR>
            <TD align=middle colSpan=2 width="728">

                <TABLE cellSpacing=0 cellPadding=0 width=1050 bgColor=$alt_light_table_bg border=0>
                <TBODY>
                <TR bgColor=#509C10>
                <TD vAlign=top align=left><IMG src="$images/blue_tl.gif" border=0 width="7" 
					height="7"></TD>
                <TD align=left width=140 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Date Sent</B> </FONT></TD>
                <TD align=left width=230 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Title</B> </FONT></TD>
                <TD align=middle width=50 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B># Sent</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>&nbsp;&nbsp;&nbsp;Opened</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Open Rate</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Clicks</B> </FONT></TD>
            	<TD align=middle width=100px><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Clicks/<br>Opens (%)</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Unsubscribes</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Bounces</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif 
                    color=white size=1><B>Full MBX</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Payment Per<br>Conversion</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Total Conversions</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Conversion %</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>Total \$\$</B> </FONT></TD>
                <TD align=middle width=100 height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=1><B>eCPM</B> </FONT></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
                    src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
end_of_html

# Get the information about the campaign

$sql = "select advertiser_id,campaign_name,sent_datetime,status,unsubscribe_cnt,fullmbx_cnt,bounce_cnt from campaign where campaign_id = $campaign_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($aid,$campaign_name,$sent_datetime,$status,$uns_cnt,$full_cnt,$bounce_cnt) = $sth->fetchrow_array();
$sth->finish();
#
# Get information from logs
#
$sql = "select sum(sent_cnt),sum(open_cnt),sum(click_cnt),sum(action_cnt) from campaign_log where campaign_id=$campaign_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($sent_cnt,$opened_cnt,$click_cnt,$total_conversions) = $sth->fetchrow_array();
$sth->finish();
#
#	Get deal type and payout
#
$sql = "select offer_type,payout from advertiser_info where advertiser_id=$aid";
$sth = $dbh->prepare($sql);
$sth->execute();
($offer_type,$payment_per_conversion) = $sth->fetchrow_array();
$sth->finish();

$datestr = substr($sent_datetime,0,10);

print "<TR>\n";
print "<TD>&nbsp; </TD>\n";
print "<TD vAlign=center align=left> \n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$datestr</font></td>\n";
print "<TD align=left>";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$campaign_name</font></TD>\n";

print "<TD align=center>";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$sent_cnt</font></TD>\n";

print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$opened_cnt</font></TD>\n"; # Opened
if ($sent_cnt > 0)
{
	$opened_percent = ($opened_cnt/$sent_cnt) * 100;
}
else
{
	$opened_percent = 0;
}
print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
printf "%4.2f\%</font></TD>\n",$opened_percent; # Opened Percentage
print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$click_cnt</font></TD>\n"; # Click Cnt 
if ($opened_cnt > 0)
{
	$click_percent = ($click_cnt/$opened_cnt) * 100;
}
else
{
	$click_percent = 0;
}
print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
printf "%4.2f\%</font></TD>\n",$click_percent; # CLick Percentage
print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$uns_cnt</font></TD>\n"; # Unsubscribed 
print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$bounce_cnt</font></TD>\n"; # Bounced 
print "<TD align=center>\n";
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$full_cnt</font></TD>\n"; # Fullmx 

print "<TD align=center>\n"; # Payment Per Conversion 
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
printf "\$%4.2f</font></TD>\n",$payment_per_conversion; # 
print "<TD align=center>\n"; # Total Conversions 
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
print "$total_conversions</font></TD>\n"; # 
print "<TD align=center>\n"; # Conversion % 
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
my $conversion_percent;
if ($click_cnt > 0)
{
	$conversion_percent = ($total_conversions/$click_cnt) * 100;
}
else
{
	$conversion_percent = 0;
}
printf "%4.2f\%</font></TD>\n",$conversion_percent; 
print "<TD align=center>\n"; # Total $$ 
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
my $total_money = $payment_per_conversion * $total_conversions;
printf "\$%6.2f</font></TD>\n",$total_money; # 
print "<TD align=center>\n"; # eCPM 
print "<FONT face=\"verdana,arial,helvetica,sans serif\" color=#000000 size=2>\n";
if ($sent_cnt > 0)
{
	$ecpm = $total_money/($sent_cnt/1000);
}
else
{
	$ecpm = 0;
}
printf "\$%6.2f</font></TD>\n",$ecpm; 
print "<TD>&nbsp; </TD>\n";
print "</TR>\n";

print<<"end_of_html";
                <TR bgColor=$alt_light_table_bg>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/yellow_bl.gif" 
                    width=7 border=0></TD>
                <TD colSpan=5>&nbsp;</TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/yellow_br.gif" 
                    width=7 border=0></TD>
				</TR>
				</TBODY>
				</TABLE>

			</TD>
			</TR>
            <TR>
            <TD colSpan=2 width="658"><IMG height=7 src="$images/spacer.gif" border=0></TD>
			</TR>
end_of_html
if ($user_type ne "R")
{
print<<"end_of_html";
            <TR>
            <TD colSpan=2 align=middle>
				<a href="camp_clear.cgi?campaign_id=$campaign_id" onClick="return confirm('Are you sure you want to erase this campaigns history?\\nIt will be permanently erased and it cannot be undone.\\nClick OK to erase');">
				<FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2><b>
				Clear History For This Campaign</b></font></a></td>
			</TR>
end_of_html
}
print<<"end_of_html";
            <TR>
            <TD colSpan=2 width="658"><IMG height=7 src="$images/spacer.gif" border=0></TD>
			</TR>
            <TR>
            <TD colspan=2 align="left">
				<a href="mainmenu.cgi"><IMG src="$images/home_blkline.gif" border=0></a></TD>
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
$util->clean_up();
exit(0);
