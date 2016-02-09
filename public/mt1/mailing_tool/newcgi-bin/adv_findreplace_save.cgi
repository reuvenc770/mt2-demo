#!/usr/bin/perl

# *****************************************************************************************
# adv_findreplace_save.cgi
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $cnt;
my $rows;
my $bgcolor;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $errmsg;
my $aname;
my $email_addr;
my $email_user_id;
my $campaign_id;
my $format="H";
my $fld;
my @aidarr= $query->param('aid');
my $s= $query->param('s');
my $search_for = $query->param('search_for');
my $search_str = $query->param('search_str');
my $search_chk= $query->param('search_chk');
my $suppURL= $query->param('suppURL');
my $unsub_use= $query->param('unsub_use');
my $suppFile= $query->param('suppFile');
my $md5suppFile= $query->param('md5suppFile');
my $md5_suppression= $query->param('md5_suppression');
my $sinactive_date = $query->param('inactive_date');
my $inactive_chk= $query->param('inactive_chk');
my $images = $util->get_images_url;
my $subject;
my $body_text;
my $first_part;
my $testvar;
my $pos;
my $pos2;
my $the_rest;
my $end_pos;
my $selected_bg_color = "#509C10";
my $not_selected_bg_color = "#E3FAD1";
my $selected_tl_gif = "$images/blue_tl.gif";
my $selected_tr_gif = "$images/blue_tr.gif";
my $not_selected_tl_gif = "$images/lt_purp_tl.gif";
my $not_selected_tr_gif = "$images/lt_purp_tr.gif";
my $selected_text_color = "#FFFFFF";
my $not_selected_text_color = "#509C10";
my @bg_color;
my @tl_gif;
my @tr_gif;
my @text_color;
my $k;
my $from_addr;
my $footer_color;
my $internal_flag;
my $unsub_url;
my $unsub_image;
my $cunsub_image;
my $content_id;
my $footer_content_id;
my $aidstr=$query->param('aidstr');
my $aid;

if ($aidstr eq "")
{
foreach my $a (@aidarr)
{
	$aidstr=$aidstr.$a.",";
}
chop($aidstr);
}

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $varFld=$query->param('varFld');;

$email_addr="email\@domain.com";
$email_user_id = 0; 
my $likestr="";

if ($s eq "")
{
	$sql="select advertiser_id,advertiser_name,status,test_flag,md5_suppression,auto_download,unsub_use,inactive_date,vendor_supp_list_id{{VARFLD}} from advertiser_info where 1=1"; 
	if ($search_chk eq "doesnotcontain")
	{
		$likestr="not";
	}
	
	if ($search_for eq "CakeCreativeID")
	{
		$varFld="auto_cake_creativeID";
		$sql.=" and (auto_cake_creativeID $likestr like '%".$search_str."%' or auto_cake_creativeID is null)";
	}
	elsif ($search_for eq "Campaign Notes")
	{
		$varFld="physical_addr";
		$sql.=" and physical_addr $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Advertiser URL")
	{
		$varFld="advertiser_url";
		$sql.=" and advertiser_url $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Landing Page")
	{
		$varFld="landing_domain";
		$sql.=" and landing_domain $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Advertiser Unsubscribe URL")
	{
		$varFld="unsub_link";
		$sql.=" and unsub_link $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Hitpath Tracking Pixel")
	{
		$varFld="hitpath_tracking_pixel";
		$sql.=" and hitpath_tracking_pixel $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Suppression URL")
	{
		$varFld="suppression_url";
		$sql.=" and suppression_url $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Direct Suppression URL")
	{
		$varFld="direct_suppression_url";
		$sql.=" and direct_suppression_url $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Unsubscribe Text")
	{
		$varFld="unsub_text";
		$sql.=" and unsub_text $likestr like '%".$search_str."%'";
	}
	elsif ($search_for eq "Friendly Advertiser Name")
	{
		$varFld="friendly_advertiser_name";
		$sql.=" and friendly_advertiser_name $likestr like '%".$search_str."%'";
	}
	if ($varFld ne "")
	{
		$sql=~s/{{VARFLD}}/,$varFld/;
	}
	if ($suppURL ne "")
	{
		$sql.=" and auto_download='".$suppURL."'";
	}	
	if ($unsub_use ne "")
	{
		$sql.=" and unsub_use='".$unsub_use."'";
	}	
	if ($md5_suppression ne "")
	{
		$sql.=" and md5_suppression='".$md5_suppression."'";
	}	
	if ($suppFile ne "")
	{
		$sql.=" and md5_suppression='N' and vendor_supp_list_id=".$suppFile;
	}
	if ($md5suppFile ne "")
	{
		$sql.=" and md5_suppression='Y' and ((advertiser_id=".$md5suppFile." and vendor_supp_list_id=0) or (vendor_supp_list_id=$md5suppFile))";
	}
	if ($aidstr ne "")
	{
		$sql.=" and advertiser_id in ($aidstr)";
	}
	if ($sinactive_date ne "")
	{
		if ($inactive_chk eq "doesnotcontain")
		{
			$sql.=" and inactive_date != '".$sinactive_date."'";
		}
		else
		{
			$sql.=" and inactive_date = '".$sinactive_date."'";
		}
	}
	$sql.=" order by advertiser_name";
}
else
{
	$sql=$s;
}
&disp_header();
&disp_body($sql);
&disp_footer();
exit(0);

#===============================================================================
# Sub: disp_header - Header for PMS System (close bogus tbls to disp correctly)
#===============================================================================
sub disp_header
{
	my ($heading_text, $username, $curdate) ;

	my $time=60*10;
	$curdate = $util->date(0,2) ;
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

    print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Mailing System EMail System</title>
<script language="JavaScript">
function selectall()
{
    refno=/chkbox/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/chkbox/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = false;
        }
    }
}
</script>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE width=100% cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
    <TD width=248 bgColor=#FFFFFF rowSpan=2>
        <img border="0" src="/mail-images/header.gif"></TD>
    <TD width=328 bgColor=#FFFFFF>&nbsp;</TD>
    </tr>
    <tr>
    <td width="468">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="left"><b><font face="Arial" size="2">&nbsp;$heading_text</FONT></b></td>
        </tr>
        <tr>
        <td align="right">
            <b><a style="TEXT-DECORATION: none" href="logout.cgi" target="_top">
            <font face=Arial size=2 color="#509C10">Logout</font></a>&nbsp;&nbsp;&nbsp;
        </td>
        </tr>
        </table>
    </td>
    </tr>
    </table>
end_of_html
	#-----------------------------------------------------------
	#  BEGIN HEADER FIX 
	#-----------------------------------------------------------
	print << "end_of_html";
	</TD>
	</TR>
	<tr>
	<td>
	<TABLE width=100% cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=12>

	<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD>&nbsp;</TD></TR>
	</TBODY>
	</TABLE>
end_of_html
	#-----------------------------------------------------------
	#  END HEADER FIX 
	#-----------------------------------------------------------

} # end sub disp_header



#===============================================================================
# Sub: disp_body
#===============================================================================
sub disp_body
{
	my ($sql)=@_;
	my $sth;
	my ($aid,$aname,$md5_suppression,$auto_download,$unsub_use,$inactive_date,$suppID);
	my $cstatus;
	my $suppName;
	my $varStr;

	print << "end_of_html" ;

	<form method=post name=campform id=campform action=advertiser_update.cgi>
	<input type=hidden name=varFld value="$varFld">
	<input type=hidden name=s value="$sql">
	<TABLE cellSpacing=1 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=9 align=center><a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan=9 align=center>Function: <select name=function><option value=FIND>Find and Replace $search_for</option>
<option value=Active>Make Active</option>
<option value=Inactive>Make Inactive</option>
<option value=Paused>Make Paused</option>
<option value=Replace>Find/Replace Advertiser Name</option>
<option value=Add>Add to Advertiser Name</option>
</select>&nbsp;&nbsp;Find/Add Str or Inactive Date(yyyy-mm-dd): <input type=text name=find_str size=20>&nbsp;&nbsp;Replace Str: <input type=text name=replace_str size=20>
</td></tr>
	<tr><td>&nbsp;</td></tr>
        <tr><td colspan=9 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="9" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b></font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the advertiser)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Advertiser</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Status</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Searched For<br>$search_for</b>:$search_str</font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>MD5</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Suppression<br>File</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Supp Auto Download</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Unsub Type</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Inactive Date</b></font></td>
	</TR> 

end_of_html
	my $test_flag;
	my $STAT;
	$STAT->{'A'}="Active";
	$STAT->{'B'}="Waiting For Pixel";
	$STAT->{'I'}="Inactive";
	$STAT->{'C'}="Pending";
	$STAT->{'R'}="Requested";
	$STAT->{'P'}="Paused";
	$STAT->{'T'}="Testing";
	$STAT->{'D'}="Deleted";
	$STAT->{'W'}="Waiting For Approval";
	my $reccnt=0;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	while (($aid,$aname,$cstatus,$test_flag,$md5_suppression,$auto_download,$unsub_use,$inactive_date,$suppID,$varStr) = $sth->fetchrow_array())
	{
		if ($auto_download eq "")
		{
			$auto_download="N";
		}
		if (($cstatus eq "A") and ($test_flag eq "Y"))
		{
			$cstatus="T";
		}
		if (($cstatus eq "I") and ($test_flag eq "P"))
		{
			$cstatus="P";
		}
		$cstatus=$STAT->{$cstatus};
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "#EBFAD1" ;     # Light Green
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;     # Light Yellow
		}
		if ($inactive_date eq "0000-00-00")
		{
			$inactive_date="";
		}
		if ($md5_suppression eq "Y")
		{
			if ($suppID == 0)
			{
				$suppName=$aname;
			}
			else
			{
				my $sql1="select advertiser_name from advertiser_info where advertiser_id=?";
				my $sth1=$dbhu->prepare($sql1);
				$sth1->execute($suppID);
				($suppName)=$sth1->fetchrow_array();
				$sth1->finish();
			}
		}
		else
		{
			my $sql1="select list_name from vendor_supp_list_info where list_id=?";
			my $sth1=$dbhu->prepare($sql1);
			$sth1->execute($suppID);
			($suppName)=$sth1->fetchrow_array();
			$sth1->finish();
		}
		print "<tr bgcolor=$bgcolor><td><input type=checkbox name=chkbox value=$aid></td><td><a href=\"/cgi-bin/advertiser_disp2.cgi?puserid=$aid&mode=U\" target=_blank>$aname</a></td><td>$cstatus</td><td>$varStr</td><td>$md5_suppression</td><td>$suppName</td></td><td align=middle>$auto_download</td><td>$unsub_use</td><td>$inactive_date</td>";

		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;
	<tr><td align=center colspan=9><input type=submit value=Update></td></tr>
	<TR><td align=center colspan=9><br>
	<A HREF="mainmenu.cgi" target="_top">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>
	</form>

end_of_html
} # end sub disp_body


#===============================================================================
# Sub: disp_footer
#===============================================================================
sub disp_footer
{
	print << "end_of_html";
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td>
end_of_html

	util::footer();
	$util->clean_up();
} # end sub disp_footer
