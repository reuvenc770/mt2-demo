#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Templates 
# File   : template_list.cgi
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
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $sname;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $pmesg=$query->param('pmesg');

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

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        bt.userID = $user_id AND
    |;
}


my $filter=$query->param('filter');
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
	$heading_text = "Date: $curdate" ;

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

	<TABLE cellSpacing=2 cellPadding=0 width=900 bgColor=#ffffff border=0>
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
	my ($nl_id,$nl_name,$nl_status,$nl_confirm_cnt);
	my $nl_date;
	my $notes;
	my $typelabel;

	print << "end_of_html" ;
	<center><a href="template_disp.cgi?mode=A"><img src="/images/add.gif" border=0></a></center>
	<br>
end_of_html
if ($pmesg ne "")
{
print<<"end_of_html";
<script language="JavaScript">
	alert('$pmesg');
</script>
end_of_html
}
print<<"end_of_html";
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
    <tr><td><form method=POST action="upload_template.cgi" encType=multipart/form-data>Template File:  <INPUT type=file name="upload_file" size="65">&nbsp;&nbsp;<input type=submit value="Upload"></form></td></tr>
	</tbody></table>
	<form method=post action=template_list.cgi>
	<center>Filter: <input type=text name=filter size=50 value="$filter">&nbsp;&nbsp<input type=submit value="Filter">
	</form>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="7" align=center width="100%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Mail Templates</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click template to edit the template)</b></font></TD>
	</TR>
	<TR> 
<TD bgcolor="#EBFAD1" align="middle" width="05%"></td>
	<TD bgcolor="#EBFAD1" align="middle" width="05%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="25%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Type</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>MTAs</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Date Added</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Notes</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>&nbsp;</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get All Templates 
	#===========================================================================
	$sql = "select template_id,template_name,date_added,notes,mailingTemplateTypeLabel from brand_template bt, MailingTemplateType mtt where $userDataRestrictionWhereClause status='A' and bt.mailingTemplateTypeID=mtt.mailingTemplateTypeID ";
	if ($filter ne '')
	{
		my @f=split(",",$filter);
		my $i=0;
		while ($i <= $#f)
		{
			if ($i == 0)
			{
				$sql = $sql . " and (";
			}
			else
			{
				$sql = $sql . " or ";
			}
			$sql=$sql." (template_name like '%$f[$i]%')";
			$i++;
		}
		$sql=$sql.") ";
	}
	$sql = $sql . "order by template_name";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($nl_id,$nl_name,$nl_date,$notes,$typelabel) = $sth->fetchrow_array())
	{
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
		print qq{<TD bgcolor="#EBFAD1" align="middle" width="05%"></td>\n};
		print qq{<TD bgcolor="#EBFAD1" align="middle" wdith="05%">$nl_id</td>\n};
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="template_disp.cgi?pmode=U&nl_id=$nl_id">$nl_name</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		$typelabel</font></TD> \n } ;
		$sql="select distinct name from mta, mta_templates where mta.mta_id=mta_templates.mta_id and mta_templates.template_id=? and mta_templates.class_id=3";
		my $stht=$dbhu->prepare($sql);
		$stht->execute($nl_id);
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		my $mname;
		while (($mname)=$stht->fetchrow_array())
		{
			print "$mname, ";
		}
		$stht->finish();
		print "</td>\n";
		print qq{		<td>$nl_date</TD> \n } ;
		print qq{		<td>$notes</font></TD> \n } ;
		if ($nl_id > 1)
		{
			print qq{ <td><a href="template_delete.cgi?nl_id=$nl_id">Delete</a>&nbsp;&nbsp;<a href="template_copy.cgi?nl_id=$nl_id">Copy</a></td> };
		}
		else
		{
			print qq{ <td>&nbsp;&nbsp;<a href="template_copy.cgi?nl_id=$nl_id">Copy</a></td> };
		}
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
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


