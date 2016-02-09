#!/usr/bin/perl
# *****************************************************************************************
# schedule_report.cgi
#
# this page displays the schedule report 
#
# History
# Jim Sobeck, 4/29/03, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $first_cid;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $campaign_name;
my $campaign_id;
my $sent_datetime;
my $action;
my $cnt;
my $cid;
my $cname;
my $datestr;
my $sth1;
my $sth2;
my $reccnt;
my $bgcolor;
my $email_user_id;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;
my $count;
my $bdate;
my $edate;
my $list_name;
my $member_cnt;
my $aol_cnt;
my $hotmail_cnt;
my $msn_cnt;
my $include_flag;
my $nonaol;
my $total_cnt;
my $allcnt;

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

$bdate = $query->param('bdate');
$edate = $query->param('edate');
if ($bdate eq "")
{
	$sql = "select date_add(curdate(),interval 1 day)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($bdate) = $sth->fetchrow_array();
	$sth->finish();
	$edate = $bdate;
}

# print out html page

util::header("Schedule Report");

print <<"end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left>
	<center>
<script language="JavaScript">
var cntarr=new Array(200)
var cidarr=new Array(200)

for (x=0; x < 200; x++)
{
    cntarr[x] = 0;
	cidarr[x] = 0;
}
function setcid(h,cid)
{
    cidarr[x] = cid;
}
function setlistcnt(h,nonaol)
{
    cntarr[h] = nonaol;
}
function selectall(cid)
{
	var y = parseInt(cid) + 1;
    for (var x=0; x < document.forms[y].length; x++)
    {
        if (document.forms[y].elements[x].type=="checkbox")
        {
            document.forms[y].elements[x].checked = true;
        }
    }
    document.forms[y].totalcnt1.value = document.forms[y].htotal.value;
}
function unselectall(cid)
{
	var y = parseInt(cid) + 1;
    for (var x=0; x < document.forms[y].length; x++)
    {
        if (document.forms[y].elements[x].type=="checkbox")
        {
            document.forms[y].elements[x].checked = false;
        }
    }
	document.forms[y].totalcnt1.value = 0;
}
function listselected(cid,h)
{
    var cnt1 = 0;
    var cnt2 = 0;
	var y;
	y = parseInt(cid)+1;
   	cnt1 = parseInt(document.forms[y].totalcnt1.value);
    for (var x=0; x < document.forms[y].length; x++)
    {
        if (document.forms[y].elements[x].type=="checkbox")
        {
            if (document.forms[y].elements[x].name=="list_"+h)
            {
                if (document.forms[y].elements[x].checked == false)
                {
                    cnt1 -= parseInt(cntarr[h]);
                }
                else
                {
                    cnt1 += parseInt(cntarr[h]);
                }
                document.forms[y].totalcnt1.value = cnt1;
            }
        }
    }
}
</script>
    <TABLE cellSpacing=0 cellPadding=10 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><font face="verdana,arial,helvetica,sans serif" 
			color="#509C10" size="3"><b>Schedule Report</b></font></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		</TD>
		</TR>
        <TR>
        <TD align=middle><font face="Verdana,Arial,Helvetica,sans-serif" 
			color="#509C10" size="3"><b>Campaigns</b></font></TD>
		</TR>
<!--        <TR>
        <TD><IMG height=7 src="$images/spacer.gif" border=0></TD>
		</TR> -->
        <TR>
        <TD align=middle>
		
			<form method="post" action="schedule_report.cgi">
			Begin Date: <input type=text name=bdate value="$bdate" size=10 maxlength=10>&nbsp;&nbsp;&nbsp;End Date: <input type=text name=edate value="$edate" size=10 maxlength=10>&nbsp;&nbsp;&nbsp;<input type=submit value="Refresh">
			</form>
            <TABLE cellSpacing=0 cellPadding=0 width=900 border=1>
            <TBODY>
            <TR bgColor="$table_header_bg">
<!--            <TD vAlign=top align=left><IMG 
                src="$images/blue_tl.gif" border=0 width="7" height="7"></TD> -->
            <TD align=left width=20% height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Scheduled<br>Date</B> </FONT></TD>
            <TD align=left width=55% height=15><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Title</B> </FONT></TD>
             <TD align=middle height=15 width=25%><FONT 
                face=Verdana,Arial,Helvetica,sans-serif 
                color=white size=1><B>Criteria</B> </FONT></TD> 
			</TR>
end_of_html
# Get number of messages sent, opened, and clicked-throughed
my $month_str;
my $year_str;
my $cdate;
my $list_id;
my $cstatus;
my $aol_flag;
my $hotmail_flag;
my $yahoo_flag;
my $other_flag;
my $open_flag;
my $last60_flag;
my $max_emails;
my $criteria_str;

$sql = "select month(curdate()),year(curdate())";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$cdate = $year_str . "-" . $month_str . "-01";

$sql = "select campaign_id,campaign_name,date_format(scheduled_date,'%m/%d/%Y'),status,aol_flag,open_flag,last60_flag,max_emails,hotmail_flag,yahoo_flag,other_flag from campaign where scheduled_date >= '$bdate' and scheduled_date <= '$edate' and deleted_date is null order by scheduled_date,campaign_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
$first_cid = 0;
while (($cid,$cname,$sent_datetime,$cstatus,$aol_flag,$open_flag,$last60_flag,$max_emails,$hotmail_flag,$yahoo_flag,$other_flag) = $sth->fetchrow_array())
{
	$criteria_str = "";
	if ($max_emails == -1)
	{
		$criteria_str = "Send All, ";
	}
	else
	{
		$criteria_str = "Send: " . $max_emails . ", ";
	}
	if ($aol_flag eq "Y")
	{
		$criteria_str = $criteria_str . "AOL,";
	}
	if ($hotmail_flag eq "Y")
	{
		$criteria_str = $criteria_str . "Hotmail/Msn,";
	}
	if ($yahoo_flag eq "Y")
	{
		$criteria_str = $criteria_str . "Yahoo,";
	}
	if ($other_flag eq "Y")
	{
		$criteria_str = $criteria_str . "Other,";
	}
	if ($open_flag eq "Y")
	{
		$criteria_str = $criteria_str . "Opens Only,";
	}
	if ($last60_flag eq "Y")
	{
		$criteria_str = $criteria_str . "Last 60 Days,";
	}
	if ($last60_flag eq "7")
	{
		$criteria_str = $criteria_str . "Last 7 Days,";
	}
	if ($last60_flag eq "2")
	{
		$criteria_str = $criteria_str . "60-120 Days,";
	}
	if ($last60_flag eq "3")
	{
		$criteria_str = $criteria_str . "120-180 Days,";
	}
	if ($last60_flag eq "O")
	{
		$criteria_str = $criteria_str . "180 Days and older,";
	}
	$_ = $criteria_str;
	chop;
	$criteria_str = $_;
print <<end_of_html;
	<tr><td>$sent_datetime</td><td>$cname</td><td>$criteria_str</td></tr>
	<tr><td colspan=3><table width=100%><tr>
    <FORM action="camp_edit_lists_save.cgi" method="post" name="adform$first_cid" target="hidden">
	<input type=hidden name=returnto value="schedule">
end_of_html
if ($cstatus ne "C")
{
print <<end_of_html;
<input type=button name="SelectAll" value="Select All" onclick="selectall($first_cid)">&nbsp;&nbsp;&nbsp;<input type=button name="UnSelectAll" value="UnSelect All" onclick="unselectall($first_cid)">
end_of_html
}
print <<end_of_html;
    <INPUT type=hidden name="campaign_id" value="$cid">
end_of_html
	$sql = "select list_id,list_name,member_cnt-aol_cnt-hotmail_cnt-msn_cnt from list where status='A' order by list_name";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	$cnt = 0;
	$total_cnt = 0;
	$allcnt = 0;
	while (($list_id,$list_name,$nonaol) = $sth1->fetchrow_array())
	{
		if ($cnt == 3)
		{
			print "</tr><tr>\n";
			$cnt = 1;
		}
		else
		{
			$cnt++;
		}
    	$sql = "select count(*) from campaign_list where campaign_id=$cid and list_id=$list_id";
    	my $sth2 = $dbh->prepare($sql);
    	$sth2->execute();
    	($include_flag) = $sth2->fetchrow_array();
    	if ($include_flag > 0)
    	{
        	print "<td><INPUT CHECKED type=checkbox name=list_$list_id onClick=\"listselected($first_cid,$list_id);\">&nbsp;&nbsp;$list_name&nbsp;&nbsp;$nonaol</TD>\n";
			$total_cnt = $total_cnt + $nonaol;
    	}
    	else
    	{
        	print "<td><INPUT type=checkbox name=list_$list_id onClick=\"listselected($first_cid,$list_id);\">&nbsp;&nbsp;$list_name&nbsp;&nbsp;$nonaol</TD>\n";
    	}
		$allcnt = $allcnt + $nonaol;
		if ($first_cid == 0)
		{
        	print "<script language=JavaScript>setlistcnt($list_id,$nonaol);</script>\n";
		}
    	$sth2->finish();
        print "<script language=JavaScript>setcid($first_cid,$cid);</script>\n";
	}
	if ($cstatus ne "C")
	{
		print "</tr><tr><td colspan=3 align=left><b>Count: <input type=text name=totalcnt1 value=$total_cnt maxlength=10 size=10><input type=hidden name=htotal value=$allcnt></b></td></tr><tr><td align=center><input type=reset value=Reset>&nbsp;&nbsp;<input type=submit value=\"Save\"></td></tr></table></form></td></tr>\n";
	}
	else
	{
		print "</tr><tr><td colspan=3 align=left><b>Count: <input type=text name=totalcnt1 value=$total_cnt maxlength=10 size=10></b></td></tr><tr><td align=center></td></tr></table></form></td></tr>\n";
	}
	$sth1->finish();
	$first_cid++;
}
$sth->finish();
print<<"end_of_html";
			</TBODY>
			</TABLE>

		</TD>
		</TR>
        <TR>
        <TD><IMG height="20" src="$images/spacer.gif" border=0></TD>
		</TR>
        <TR>
        <TD align="center">
			<a href="mainmenu.cgi" target="_top">
			<IMG src="$images/home_blkline.gif" border=0></a></TD>
		</TD>
		</TR>
        <TD><IMG height="20" src="$images/spacer.gif" border=0></TD>
		</TR>
        <TR>
        <TD>
	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html
$sth->finish();

$util->footer();

$util->clean_up();
exit(0);
