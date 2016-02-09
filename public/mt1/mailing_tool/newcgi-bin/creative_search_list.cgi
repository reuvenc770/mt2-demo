#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Creatives 
# File   : creative_search_list.cgi
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sth, $sth1,$reccnt, $sql, $dbh ) ;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $bgcolor;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
$user_id=1;
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $sql=bldQry($query);
&disp_header();
&disp_body($sql);
&disp_footer();
#------------------------
# End Main Logic
#------------------------



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

	print << "end_of_html" ;

	<TABLE cellSpacing=1 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=2 align=center><a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="13" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>Creatives</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the creative)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Creative</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Creative ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Advertiser</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>SID or cakeCreativeID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Status</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Approval Status</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Comment(s)</b></font></td>
	</TR> 

end_of_html
	my $STAT;
	$STAT->{'A'}="Active";
	$STAT->{'I'}="Inactive";
	$STAT->{'D'}="Deleted";
	my $reccnt=0;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	while (($cid,$cname,$aid,$aname,$sid,$comment,$cstatus,$astatus,$istatus,$cake_creativeID) = $sth->fetchrow_array())
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
		print "<tr bgcolor=$bgcolor><td><a href=\"/cgi-bin/edit_creative.cgi?aid=$aid&cid=$cid\" target=_blank>$cname</a></td><td>$cid</td><td><a href=\"/cgi-bin/advertiser_disp2.cgi?puserid=$aid&mode=U\" target=_blank>$aname</a></td><td><a href=\"/cgi-bin/hitpath_creative_deploy_it.cgi?aid=$aid\" target=_blank>$sid</a></td><td>$cstatus</td><td>$astatus</td><td>$comment</td>";

		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
	<A HREF="mainmenu.cgi" target="_top">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>

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

sub bldQry
{
	my ($query)=@_;
	my $sql;
	my $tables;

my $cname=$query->param('cname');
my $aname=$query->param('aname');
my $cstatus=$query->param('cstatus');
my $astatus=$query->param('astatus');
my $creatorStatus=$query->param('creatorStatus');
my $tstr=$query->param('tstr');
my $istartDate=$query->param('istartDate');
my $climit=$query->param('climit');
my $iendDate=$query->param('iendDate');
my $cstartDate=$query->param('cstartDate');
my $cendDate=$query->param('cendDate');
my $cids=$query->param('cids');
my $sord=$query->param('sord');
if ($sord eq "")
{
	$sord="advertiser_name";
}

	$tables="creative c, advertiser_info ai";
	$sql="select c.creative_id,creative_name,ai.advertiser_id,ai.advertiser_name,ai.sid,cc.comment,c.status,c.approved_flag,c.internal_approved_flag,ai.cake_creativeID from creative c join advertiser_info ai on c.advertiser_id=ai.advertiser_id left outer join creativeComment cc on c.creative_id=cc.creativeID  where 1=1 ";
	if ($cname ne "")
	{
		$sql.="and creative_name like '%".$cname."%' ";
	}
	if ($aname ne "")
	{
		$sql.="and advertiser_name like '%".$aname."%' ";
	}
	if ($cstatus ne "")
	{
		$sql.="and c.status='".$cstatus."' ";
	}
	if ($astatus ne "")
	{
		if ($astatus eq "N")
		{
			$sql.="and c.approved_flag='N' ";
		}
		elsif ($astatus eq "Y")
		{
			$sql.="and c.approved_flag='Y' ";
		}
		if ($astatus eq "I")
		{
			$sql.="and c.internal_approved_flag='Y' ";
		}
	}
	if ($creatorStatus ne "")
	{
		if ($creatorStatus eq "O")
		{
			$sql.="and c.original_flag='Y' ";
		}
		elsif ($creatorStatus eq "A")
		{
			$sql.="and c.original_flag!='Y' ";
		}
		if ($creatorStatus eq "C")
		{
			$sql.="and c.copywriter='Y' ";
		}
	}
	if ($tstr ne "")
	{
		$sql.="and c.html_code ".$climit." like '%".$tstr."%' ";
	}
	if ($istartDate ne "")
	{
		$sql.="and c.inactive_date >= '$istartDate' ";
	} 
	if ($iendDate ne "")
	{
		$sql.="and c.inactive_date <= '$iendDate' ";
	} 
	if ($cstartDate ne "")
	{
		$sql.="and c.creative_date >= '$cstartDate' ";
	} 
	if ($cendDate ne "")
	{
		$sql.="and c.creative_date <= '$cendDate' ";
	} 
	if ($cids ne "")
	{
        $cids =~ s/[ \n\r\f\t]/\|/g ;
        $cids =~ s/\|{2,999}/\|/g ;
        my @c= split '\|', $cids;
		my $cstr="";
		foreach my $c1 (@c)
		{
			$cstr.=$c1.",";
		}
		chop($cstr);
		$sql.="and c.creative_id in ($cstr) ";
	}
	$sql.=" order by ".$sord;
open(LOG,">/tmp/cq.q");
print LOG "$sql\n";
close(LOG);
	return($sql);
}
