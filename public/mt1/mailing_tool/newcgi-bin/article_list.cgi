#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Articles 
# File   : article_list.cgi
#
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
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql, $dbh ) ;
my ($content_id,$content_name,$content_date,$inactive_date);
my $type_str;
my $sth1;

my $old_catid=$query->param('category_id');
if ($old_catid eq "")
{
	$old_catid=0;
}
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&disp_header();
&disp_body();
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

	$curdate = $util->date(0,2) ;
	$heading_text = "&nbsp;&nbsp;&nbsp;Date: $curdate" ;

	util::header($heading_text);    # Print HTML Header
	#-----------------------------------------------------------
	#  BEGIN HEADER FIX 
	#-----------------------------------------------------------
	print << "end_of_html";
	</TD>
	</TR>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF>
	<TABLE cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

	<TABLE cellSpacing=0 cellPadding=0 width=900 bgColor=#ffffff border=0>
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
	my ($puserid, $username, $fname, $lname, $city, $state, $zip, $status);
	my ($bgcolor) ;
	my ($vid, $name, $privacy_text, $unsub_text);
	my $send_str;

	print << "end_of_html" ;
	<center><a href="/cgi-bin/article.cgi"><img src="/mail-images/add.gif" border=0></a><br><br>
	<form method=post action=article_list.cgi>
	Category: <select name=category_id>
<option selected value=0>ALL</option>
end_of_html
$sql="select datatype_id,type_str from datatypes order by type_str";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $type_id;
my $type_str;
while (($type_id,$type_str)=$sth1->fetchrow_array())
{
	if ($type_id == $old_catid)
	{
		print "<option value=$type_id selected>$type_str</option>\n";
	}
	else
	{
		print "<option value=$type_id>$type_str</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
	</select><input type=submit value="Filter">
	</form>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="6" align=center height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Newsletter Articles</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the article)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="30%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width=15%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Date Added</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width=15%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Inactive Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width=15%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Category</b></font></td>
	<TD bgcolor="#EBFAD1" align="center" width=10%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get Newsletter Articles 
	#===========================================================================
	if ($old_catid > 0)
	{
		$sql = "select article_id,article_name,date_of_content,inactive_date,type_str from article,datatypes where status='A' and article.datatype_id=datatypes.datatype_id and article.datatype_id=$old_catid"; 
	}
	else
	{
		$sql = "select article_id,article_name,date_of_content,inactive_date,type_str from article,datatypes where status='A' and article.datatype_id=datatypes.datatype_id"; 
	}
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($content_id,$content_name,$content_date,$inactive_date,$type_str) = $sth->fetchrow_array())
	{
		if ($inactive_date eq "0000-00-00")
		{
			$inactive_date="";
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

		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=left>&nbsp;</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="article.cgi?cid=$content_id">$content_name</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$content_date</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$inactive_date</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$type_str</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/article_del.cgi?cid=$content_id">Delete</a>&nbsp;&nbsp;<a href="/cgi-bin/article_preview.cgi?cid=$content_id" target=_blank>Preview</a></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=6><br>
	<A HREF="mainmenu.cgi">
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


