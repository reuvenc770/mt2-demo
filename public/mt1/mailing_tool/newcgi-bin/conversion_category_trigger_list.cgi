#!/usr/bin/perl

# *****************************************************************************************
# conversion_category_trigger_list.cgi
#
# this page displays the list of conversion campaign categories and lets the user edit / add
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $catid;
my $errmsg;
my $user_id;
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $status_name;
my $status;
my $cat_id;
my $category_name;
my $domain_name;
my $trigger1;
my $tclient;
my $trigger2;
my $trigger1_cid;
my $trigger2_cid;
my $trigger1_str;
my $trigger2_str;
my $alt_trigger_str;
my $alt_trigger;
my $userid = $query->param('userid');
if ($userid eq "")
{
	$userid = 0;
}

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# print out the html page

util::header("Trigger Defaults");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<form method=get action=conversion_category_trigger_list.cgi>
		<TABLE cellSpacing=0 cellPadding=0 width=600 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td>Client: </td>
		<td align=left><select name=userid>
end_of_html
$sql = "select user_id,company from user where status='A' order by company"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $tid;
my $company;
if ($userid == 0)
{
	print "<option selected value=0>ALL</option>\n";
}
else
{
	print "<option value=0>ALL</option>\n";
}
while (($tid,$company) = $sth->fetchrow_array())
{
	if ($tid == $userid)
	{
		print "<option selected value=$tid>$company</option>\n";
	}
	else
	{
		print "<option value=$tid>$company</option>\n";
	}
}
$sth->finish;
print<<"end_of_html";
	</select>&nbsp;&nbsp;<input type=submit value="Go">
		</td>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
		<TABLE cellSpacing=0 cellPadding=0 width=760 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a category to edit</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<table cellSpacing="0" cellPadding="3" width="100%" border="0" id="table7">
		<tr>
						<td align="left" width="30%" bgcolor="#008000">
						<b>
						<font color="#FFFFFF">Category</font></b></td>
		<td width="20%" bgcolor="#008000"><b><font color="#FFFFFF">&nbsp;Trigger 1</font></b></td>
		<td width="20%" bgcolor="#008000"><b><font color="#FFFFFF">Trigger 2</font></b></td>
					</tr>
end_of_html

# read info about the lists

$sql = "select category_info.category_id,category_name from category_info order by category_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $catcnt;
while (($cat_id,$category_name) = $sth->fetchrow_array())
{
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }

	$sql="select count(*) from advertiser_info where category_id=? and status='A' and test_flag='N'";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute($cat_id);
	($catcnt)=$sth1->fetchrow_array();
	$sth1->finish();

	if ($userid > 0)
	{
		$sql="select client_id,trigger1_aid,trigger2_aid from conversion_category_trigger where client_id in (0,$userid) and category_id=$cat_id order by client_id desc";
	}
	else
	{
		$sql="select client_id,trigger1_aid,trigger2_aid from conversion_category_trigger where client_id =$userid and category_id=$cat_id order by client_id desc";
	}
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	$trigger1_str="";	
	$trigger1_cid="";	
	$trigger2_str="";	
	$trigger2_cid="";	
	if (($tclient,$trigger1,$trigger2) = $sth1->fetchrow_array())
	{
		$sth1->finish();
		if ($trigger1 > 0)
		{
			$sql = "select advertiser_name from advertiser_info where advertiser_id = ?";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute($trigger1);
			($trigger1_str) = $sth1->fetchrow_array();
			$sth1->finish();
			my $temp_str=$trigger1_str." ".$cat_id;
    		$sql="select campaign_id from campaign where campaign_name='$temp_str' and status='T' and deleted_date is null";
    		$sth1 = $dbhq->prepare($sql);
    		$sth1->execute();
    		($trigger1_cid) = $sth1->fetchrow_array();
			$sth1->finish();
		}
		if ($trigger2 > 0)
		{
			$sql = "select advertiser_name from advertiser_info where advertiser_id = ?"; 
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute($trigger2);
			($trigger2_str) = $sth1->fetchrow_array();
			$sth1->finish();
			my $temp_str=$trigger2_str." ".$cat_id;
    		$sql="select campaign_id from campaign where campaign_name='$temp_str' and status='T' and deleted_date is null";
    		$sth1 = $dbhq->prepare($sql);
    		$sth1->execute();
    		($trigger2_cid) = $sth1->fetchrow_array();
			$sth1->finish();
		}
	}
	else
	{
		$sth1->finish();
	}
	if (($trigger1_cid ne "") and ($trigger1_str ne ""))
	{
		$trigger1_str=$trigger1_str."(".$trigger1_cid.")";
	}
	if (($trigger2_cid ne "") and ($trigger2_str ne ""))
	{
		$trigger2_str=$trigger2_str."(".$trigger2_cid.")";
	}
	if ($tclient > 0)
	{
		print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"> <A HREF="conversion_category_trigger.cgi?cid=$cat_id&userid=$userid">$category_name</a> (Advertisers - $catcnt) </font></TD> <TD><font color=red>$trigger1_str</font></TD> <TD><font color=red>$trigger2_str</font></TD></TR> \n };
	}
	else
	{
		print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"> <A HREF="conversion_category_trigger.cgi?cid=$cat_id&userid=$userid">$category_name</a> (Advertisers - $catcnt)</font></TD> <TD>$trigger1_str</TD> <TD>$trigger2_str</TD></TR> \n };
	}
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

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

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
