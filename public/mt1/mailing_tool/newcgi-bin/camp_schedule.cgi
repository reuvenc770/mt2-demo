#!/usr/bin/perl

# *****************************************************************************************
# camp_schedule.cgi
#
# this page is for scheduling the email
#
# History
# Grady Nash, 7/30/01, Creation
# Jim Sobeck, 	08/07/01,	Added logic to allow editing of campaign
# Jim Sobeck, 04/30/02, Added Logic to specify number of emails to send
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $catid;
my $cname;
my $sth2;
my $sql;
my $dbh;
my $campaign_id = $query->param('campaign_id');
my $list_id;
my $status;
my $schedule_date;
my $max_emails;
my $clast60;
my $openflag;
my $aolflag;
my $yes_flag;
my $last90_flag;
my $month_flag;
my $no_flag;
my $seven_flag;
my $fifteen_flag;
my $two_flag;
my $three_flag;
my $old_flag;
my $yes_open_flag;
my $no_open_flag;
my $aol_yes_flag;
my $general_aol_flag;
my $opener_catid;
my $nonyahoo_flag;
my $aol_flag;
my $hotmail_flag;
my $yahoo_flag;
my $other_flag;
my $both_flag;
my $light_table_bg = $util->get_light_table_bg;
my $images = $util->get_images_url;
my $list_members = 1;
my $counter;

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

# make sure this campaign has some valid member list or lists assigned to it before 
# allowing the user to schedule it to be sent.

$sql = "select list_id from campaign_list where campaign_id = $campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($list_id) = $sth->fetchrow_array())
{
#	$sql = "select count(*) from list_member where list_id = $list_id and status = 'A'";
	$sql = "select member_cnt from list where list_id = $list_id and status = 'A'";
	$sth2 = $dbhq->prepare($sql);
	$sth2->execute();
	($counter) = $sth2->fetchrow_array();
	$sth2->finish();

	$list_members = $list_members + $counter;
}
$sth->finish();

if ($list_members == 0)
{
	util::logerror("Error, the campaign you selected does not have any email member lists <br>
					assigned to it.  You must assign at least one email list that contains <br>
					some active members to this campaign before it can be scheduled.");
	$util->clean_up();
	exit(0);
}	

# print out the html page

util::header("Campaign Schedule");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10><!-- doing ct-table-open -->

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
			<TD vAlign=center align=left><font face="verdana,arial,helvetica,sans serif" 
				color="#509C10" size="3"><b>Schedule Your Campaign</b></font></TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Set your Campaign status to either Draft or Scheduled. Your Campaign
			will remain in Draft mode until you move it to Scheduled. If you schedule your 
			Campaign, it will be sent on the date specified beginning around midnight.
			If you schedule your Campaign for today, it will begin going out
			in the next 5 minutes.<BR></FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="camp_schedule_save.cgi" method=get>
		<INPUT type=hidden name="campaign_id" value="$campaign_id">
		<INPUT type=hidden name="copen" value="N">

		<TABLE cellSpacing=0 cellPadding=0 width=760 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=middle>

				<TABLE cellSpacing=0 cellPadding=0 width=600 bgColor=$light_table_bg border=0>
				<TBODY>
				<TR align=top bgColor=#509C10 height=18>
				<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" border=0 
					width="7" height="7"></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD align=middle height=15>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR bgColor=#509C10 height=15>
					<TD align=middle width="100%" height=15>
						<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Campaign Status</B> </FONT> </TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
				<TR bgColor=$light_table_bg>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=$light_table_bg>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR>
					<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					<TR>
					<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
						color=#509C10 size=2>
end_of_html

# get schedule information for this campaign

$sql = "select status,scheduled_date,max_emails,last60_flag,aol_flag,open_flag,hotmail_flag,yahoo_flag,other_flag,open_category_id from campaign where campaign_id=$campaign_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($status,$schedule_date,$max_emails,$clast60,$aol_flag,$openflag,$hotmail_flag,$yahoo_flag,$other_flag,$opener_catid) = $sth->fetchrow_array();

if (($status eq "D") || ($status eq "A"))
{
	my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
	$year = $year + 1900;
	$mon = $mon + 1;
	if ($mon < 10)
	{
		$mon = "0$mon";
	}
	if ($mday < 10)
	{
		$mday = "0$mday";
	}
	$schedule_date = "$mon/$mday/$year";

	print qq {
		<INPUT style="BACKGROUND: $light_table_bg" type=radio CHECKED name=schedule 
		value=D>Draft<BR>
		<INPUT style="BACKGROUND: $light_table_bg" type=radio name=schedule 
		value=S>Scheduled for
		<INPUT type="text" name="sdate" size="10" value="$schedule_date">\n };
}
else
{
	$schedule_date = substr($schedule_date,5,2) . "/" . substr($schedule_date,8,2) . 
		"/" . substr($schedule_date,0,4);

	print qq { 
		<INPUT style="BACKGROUND: $light_table_bg" type=radio name=schedule value=D>Draft<BR>
	    <INPUT style="BACKGROUND: $light_table_bg" type=radio CHECKED name=schedule 
		value="$status">Scheduled for
		<INPUT type="text" name="sdate" size="10" value="$schedule_date"> \n };
}
$sth->finish();

if ($clast60 eq "Y")
{
	$yes_flag="checked";
	$month_flag = "";
	$no_flag = "";
	$seven_flag = "";
	$fifteen_flag = "";
	$two_flag = "";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "7")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "checked";
	$fifteen_flag="";
	$month_flag = "";
	$two_flag = "";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "F")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "";
	$fifteen_flag="checked";
	$month_flag = "";
	$two_flag = "";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "M")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "";
	$fifteen_flag="";
	$two_flag = "";
	$month_flag="checked";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "9")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "";
	$fifteen_flag="";
	$two_flag = "";
	$month_flag="";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "checked";
}
else
{
	$no_flag="checked";
	$yes_flag = "";
	$seven_flag = "";
	$fifteen_flag="";
}
if ($openflag eq "Y")
{
	$yes_open_flag="checked";
	$no_open_flag = "";
}
else
{
	$no_open_flag="checked";
	$yes_open_flag = "";
}
if ($aol_flag eq "N")
{
	$aol_flag="";
}
else
{
	$aol_flag="checked";
}
if ($hotmail_flag eq "N")
{
	$hotmail_flag="";
}
else
{
	$hotmail_flag="checked";
}
if ($yahoo_flag eq "N")
{
	$yahoo_flag="";
}
else
{
	$yahoo_flag="checked";
}
if ($other_flag eq "N")
{
	$other_flag="";
}
else
{
	$other_flag="checked";
}
print << "end_of_html";
						<BR></FONT></TD>
					</TR>
					<TR>
					<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Send To:
						<input type=radio name=clast60 value="N" $no_flag>All&nbsp;&nbsp;<input type=radio name=clast60 value="7" $seven_flag>Last 7 Days&nbsp;&nbsp;&nbsp;<input type=radio name=clast60 value="F" $fifteen_flag>Last 15 Days&nbsp;&nbsp;&nbsp;<input type=radio name=clast60 value="M" $month_flag>Last 30 Days&nbsp;&nbsp;&nbsp;<input type=radio name=clast60 value="Y" $yes_flag>Last 60 Days&nbsp;&nbsp;&nbsp;<input type=radio name=clast60 value="9" $last90_flag>Last 90 Days</font></td>
					</tr>	
					<TR>
					<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Send To:
						<input type=checkbox name=aolflag value="Y" $aol_flag>AOL &nbsp;&nbsp;<input type=checkbox name=hotmailflag value="Y" $hotmail_flag>Hotmail/MSN&nbsp;&nbsp;&nbsp;<input type=checkbox name=yahooflag value="Y" $yahoo_flag>Yahoo&nbsp;&nbsp;&nbsp;<input type=checkbox name=otherflag value="Y" $other_flag>Other Domains</font></td>
					</tr>	
					<tr>
					<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Openers Category:
					<select name="open_catid">
end_of_html
					if ($opener_catid == 0)
					{
						print "<option value=0 selected>All</option>\n";
					}
					else
					{
						print "<option value=0>All</option>\n";
					}
				
					$sql = "select category_id,category_name from category_info where category_id > 0 order by category_name";
					$sth1 = $dbhq->prepare($sql);
					$sth1->execute();
					while (($catid,$cname) = $sth1->fetchrow_array())
					{
						if ($opener_catid == $catid)
						{
							print "<option value=$catid selected>$cname</option>\n";
						}
						else
						{
							print "<option value=$catid>$cname</option>\n";
						}
					}
					$sth1->finish();
print<<"end_of_html";
					</select>
					</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Max E-mails To Send (-1 means all):
						<input type=text name=max_emails value=$max_emails></font></td>
					</tr>	
					<TR>
					<TD><IMG height=7 src="$images/spacer.gif"></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
				<TR bgColor=$light_table_bg>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=$light_table_bg height=10>
				<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD align=middle bgColor=$light_table_bg><IMG height=3 
					src="$images/spacer.gif" width=1 
					border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
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
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<td align="center" width="50%">
				<a href="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></td>
			<td align="center" width="50%">
				<INPUT type=image src="$images/save.gif" border=0></TD>
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
