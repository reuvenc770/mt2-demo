#!/usr/bin/perl

# *****************************************************************************************
# view_advertiser_save.cgi
#
# this page presents the user with a preview of the campaign email
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
my $asset = $query->param('asset');
if ($asset eq "Creative")
{
	$fld="cid";
}
else
{
	$fld="sid";
}
my $tasset=$asset;
$tasset=~tr/A-Z/a-z/;
my $s= $query->param('s');
my $cids= $query->param('cids');
my $cakeids = $query->param('cakeids');
my $cake_offerID= $query->param('cake_offerID');
my $sid= $query->param('sid');
$cids=~s/ //g;
$cakeids=~s/ //g;
my $cname= $query->param('cname');
my $ctype= $query->param('ctype');
my $tstr=$query->param('tstr');
my $climit=$query->param('climit');
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

$email_addr="email\@domain.com";
$email_user_id = 0; 

if ($s eq "")
{
	if ($asset eq "Creative")
	{
		$sql = "select c.creative_id,creative_name,ai.advertiser_id,ai.advertiser_name,ai.sid,cc.comment,c.status,c.approved_flag,c.internal_approved_flag,ai.cake_creativeID,c.inactive_date from creative c join advertiser_info ai on c.advertiser_id=ai.advertiser_id left outer join creativeComment cc on c.creative_id=cc.creativeID  where 1=1 ";
	}
	elsif ($asset eq "From")
	{
		$sql = "select c.from_id,advertiser_from,ai.advertiser_id,ai.advertiser_name,ai.sid,'',c.status,c.approved_flag,c.internal_approved_flag,ai.cake_creativeID,c.inactive_date from advertiser_from c join advertiser_info ai on c.advertiser_id=ai.advertiser_id where 1=1 ";
	}
	elsif ($asset eq "Subject")
	{
		$sql = "select c.subject_id,advertiser_subject,ai.advertiser_id,ai.advertiser_name,ai.sid,'',c.status,c.approved_flag,c.internal_approved_flag,ai.cake_creativeID,c.inactive_date from advertiser_subject c join advertiser_info ai on c.advertiser_id=ai.advertiser_id where 1=1 ";
	}
	if ($tstr ne "")
	{
		if ($asset eq "Creative")
		{
			$sql.="and c.html_code ".$climit." like '%".$tstr."%' ";
		}
		elsif ($asset eq "Subject")
		{
			$sql.="and c.advertiser_subject ".$climit." like '%".$tstr."%' ";
		}
		elsif ($asset eq "From")
		{
			$sql.="and c.advertiser_from ".$climit." like '%".$tstr."%' ";
		}
	}
	if ($sid ne "")
	{
		$sql.=" and ai.sid=$sid ";
	}
	if ($cake_offerID ne "")
	{
		$sql.=" and ai.cake_creativeID in (select creativeID from CakeCreativeOfferJoin where offerID=$cake_offerID)  ";
	}
	my $cakestr="";
	if ($cakeids ne "")
	{
		$cakeids =~ s/[ \n\r\f\t]/\|/g ;
	    $cakeids =~ s/\|{2,999}/\|/g ;
	    my @c= split '\|', $cakeids;
	    my $cakestr="";
	    foreach my $c1 (@c)
	    {
			$c1=~s/ //g;
	    	$cakestr.=$c1.",";
	    }
	    chop($cakestr);
		$sql.=" and ai.cake_creativeID in ($cakestr) ";
	}
	if ($aidstr ne "")
	{
		$sql.=" and ((c.advertiser_id in ($aidstr) ";
		if ($ctype ne "")
		{
			$sql.=" and c.status='$ctype' ";
		}
		if ($cname ne "")
		{
			if ($asset eq "Creative")
			{
				$sql.=" and c.creative_name like '%".$cname."%' ";
			}
			elsif ($asset eq "Subject")
			{
				$sql.=" and c.advertiser_subject like '%".$cname."%' ";
			}
			elsif ($asset eq "From")
			{
				$sql.=" and c.advertiser_from like '%".$cname."%' ";
			}
		}
		$sql.=")";
		if ($cids eq "")
		{
			$sql.=")";
		}
	}
	if ($cids ne "")
	{
		$cids =~ s/[ \n\r\f\t]/\|/g ;
	    $cids =~ s/\|{2,999}/\|/g ;
	    my @c= split '\|', $cids;
	    my $cstr="";
	    foreach my $c1 (@c)
	    {
			$c1=~s/ //g;
	    	$cstr.=$c1.",";
	    }
	    chop($cstr);
		if ($aidstr ne "")
		{
			if ($asset eq "Creative")
			{
	    		$sql.="or c.creative_id in ($cstr)) ";
			}
			elsif ($asset eq "Subject")
			{
	    		$sql.="or c.subject_id in ($cstr)) ";
			}
			elsif ($asset eq "From")
			{
	    		$sql.="or c.from_id in ($cstr)) ";
			}
		}
		else
		{
			if ($asset eq "Creative")
			{
	    		$sql.="and c.creative_id in ($cstr) ";
			}
			elsif ($asset eq "Subject")
			{
	    		$sql.="and c.subject_id in ($cstr) ";
			}
			elsif ($asset eq "From")
			{
	    		$sql.="and c.from_id in ($cstr) ";
			}
		}
	}
	if ($asset eq "Creative")
	{
		$sql.=" order by c.creative_name";
	}
	elsif ($asset eq "Subject")
	{
		$sql.=" order by c.advertiser_subject";
	}
	elsif ($asset eq "From")
	{
		$sql.=" order by c.advertiser_from";
	}
}
else
{
	$sql=$s;
}
open(LOG,">>/tmp/d.log");
print LOG "<$sql>\n";
close(LOG);
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
	my ($cid,$cname,$aid,$aname,$sid);
	my $comment;
	my $cstatus;
	my $astatus;
	my $istatus;
	my $cake_creativeID;
	my $inactive_date;

	print << "end_of_html" ;

	<form method=post name=campform id=campform action=creative_update.cgi>
	<input type=hidden name=asset value="$asset">
	<input type=hidden name=s value="$sql">
	<TABLE cellSpacing=1 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=9 align=center><a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan=9 align=center>Function: <select name=function><option value=FIND>Find and Replace</option>
<option value=Active>Make Active</option>
<option value=COPY_ADV>Copy To Advertiser</option>
<option value=Delete>Delete</option>
<option value=Inactive>Make Inactive</option>
<option value=CHG_NAME>Find/Replace $asset name</option>
<option value=ADD_NAME>Add to $asset name</option>
</select>&nbsp;&nbsp;Find/Add Str or Inactive Date(yyyy-mm-dd): <input type=text name=find_str size=20>&nbsp;&nbsp;Replace Str: <input type=text name=replace_str size=20>&nbsp;&nbsp;&nbsp;Copy To Advertiser: <select name=cadv>
end_of_html
my $sql1="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
$sth=$dbhq->prepare($sql1);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
    print "<option value=$aid>$aname</option>\n";
}
print<<"end_of_html";
</select></td></tr>
	<tr><td>&nbsp;</td></tr>
        <tr><td colspan=9 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="9" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>${asset}s</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the creative)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>$asset</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>$asset ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Advertiser</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>SID or cakeCreativeID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Status</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Inactive Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Approval Status</b></font></td>
end_of_html
if ($asset eq "Creative")
{
print<<"end_of_html";
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Comment(s)</b></font></td>
end_of_html
}
print<<"end_of_html";
	</TR> 

end_of_html
	my $STAT;
	$STAT->{'A'}="Active";
	$STAT->{'I'}="Inactive";
	$STAT->{'D'}="Deleted";
	my $reccnt=0;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	while (($cid,$cname,$aid,$aname,$sid,$comment,$cstatus,$astatus,$istatus,$cake_creativeID,$inactive_date) = $sth->fetchrow_array())
	{
		if ($sid == 0)
		{
			$sid=$cake_creativeID;
		}
		$cstatus=$STAT->{$cstatus};
		if ($astatus eq 'N')
		{
			$astatus="Not Approved";
		}
		elsif ($istatus eq 'Y')
		{
			$astatus="Approved - Internally";
		}
		else
		{
			$astatus="Approved";
		}
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
		print "<tr bgcolor=$bgcolor><td><input type=checkbox name=chkbox value=$cid></td><td><a href=\"/cgi-bin/edit_$tasset.cgi?aid=$aid&$fld=$cid\" target=_blank>$cname</a></td><td>$cid</td><td><a href=\"/cgi-bin/advertiser_disp2.cgi?puserid=$aid&mode=U\" target=_blank>$aname</a></td><td><a href=\"/cgi-bin/hitpath_creative_deploy_it.cgi?aid=$aid\" target=_blank>$sid</a></td><td>$cstatus</td><td>$inactive_date</td><td>$astatus</td><td>$comment</td>";

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
