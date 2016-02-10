#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Clients
# File   : client_list.cgi
#
# Input  :
#   1. mesg - If present - display mesg from list_upd.cgi or list_add.cgi.
#
# Output :
#   1. Display 2 Forms - 1. Adds New List Names, 2. Update(s) List values.
#   2. Pass control to 'list_add.cgi' to - Add NEW rec to 'list' table  or 
#   3. Pass control to 'list_upd.cgi' to - Update (1:M) 'list' recs
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 02/07/07, Added logic for exclude_thirdparty
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
my $username = $query->param('username');
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $sname;
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
my $BusinessUnit;

$sql = "select BusinessUnit from UserAccounts where user_id=?";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute($user_id);
($BusinessUnit) = $sth1->fetchrow_array();
$sth1->finish();

my @raw_cookies;
my %cookies;
my $key;
my $val;
@raw_cookies = split (/; /,$ENV{'HTTP_COOKIE'});
foreach (@raw_cookies)
{
    ($key, $val) = split (/=/,$_);
    $cookies{$key} = $val;
}
my $brand_type = $cookies{'brand_type'};
if ($brand_type eq "")
{
    $brand_type="ALL";
}
my $old_tid = $cookies{'ctid'};
if ($old_tid eq "")
{
    $old_tid=0;
}
my $tag=$cookies{'tag'};
$tag||='0';
my $ex_subdomain=$cookies{'ex_subdomain'};
$ex_subdomain||='';

disp_header();
if ( $mesg ne "" ) {
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
	# print qq{ 	alert("The specified List Records have been SUCCESSFULLY updated!");  \n } ;
	print qq{ 	alert("$mesg");  \n } ;
	print qq{ </script>  \n } ;
}
&disp_body();
&write_java();
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
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

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
	my ($puserid, $username, $fname, $lname, $cl_type, $status);
	my ($listOwner, $countryName, $ownerType, $subAffiliateID);
	my ($bgcolor) ;

print << "end_of_html" ;

<form method=post action="/cgi-bin/client_list.cgi">
Please select a client: <select name=client_id>
end_of_html
	
	print "<option value='ALL' >Show All Clients</option>";
	
	my $clientSql = "select user_id, username from user where 1=1";
	if ($BusinessUnit eq "Orange")
	{
		$clientSql.=" and OrangeClient='Y' and status='A' ";
	}
	else
	{
		#$clientSql.=" and ((user_id < 2598) or (user_id >= 2598 and OrangeClient!='Y')) and status='A' ";
		$clientSql.=" and OrangeClient!='Y' and status='A' ";
	}
	$clientSql.=" order by username";
	$sth = $dbhq->prepare($clientSql);
	$sth->execute();
	while (my $userData = $sth->fetchrow_hashref()){
		
		print "<option value=$userData->{'user_id'} >$userData->{'username'}</option>";	
	}

print<<"end_of_html";	
</select><br /><input type=submit value="Go"></form>
end_of_html
	
print<<"end_of_html";
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="7" align=center width="100%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Clients</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click username to edit the client)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="middle" width="05%">ID</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Username</b></font></td>
<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Sub Aff. ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="25%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Name (Last, First)</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Client Type</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>List Owner</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Country</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Owner Type</b></font></td>
end_of_html
	if ($old_tid > 0)
	{
print<<"end_of_html";
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Exclude</b></font></td>
end_of_html
	}
print<<"end_of_html";
	<!-- <TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Brands/Hosts</b></font></td> -->
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>&nbsp;</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================
	
	my $userID =  $query->param('client_id');
	
	if($userID){
		
		my $whereClause = qq|user_id = $userID|;
		
		#show all clients
		if($userID eq 'ALL'){
			$whereClause = qq|1=1|;
		}
		if ($BusinessUnit eq "Orange")
		{
			$whereClause.=" and OrangeClient='Y'";
			$whereClause.=" and status='A'";
		}
		else
		{
			$whereClause.=" and ((user_id < 2598) or (user_id >= 2598 and OrangeClient!='Y'))";
		}
			
		$sql = qq|select user_id, username, last_name, first_name, client_type, status,clientStatsGroupingLabel, 
		c.countryName, ct.clientTypeName, u.cakeSubAffiliateID
		from 
			user u
			left outer join ClientStatsGrouping csg on csg.clientStatsGroupingID=u.clientStatsGroupingID 
			left outer join Country c on u.countryID = c.countryID
			left outer join ClientType ct on u.clientTypeId = ct.clientTypeId
		where 
			$whereClause order by u.user_id|;
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		$reccnt = 0 ;
		while (($puserid, $username, $lname, $fname, $cl_type, $status, $listOwner, $countryName, $ownerType, $subAffiliateID) = $sth->fetchrow_array())
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
	
			if ( $status eq "D" )
			{
				$status = "Inactive" ;
			}
			elsif ($status eq 'P')
			{
				$status = "Paused" ;
			}
			else
			{
				$status = "Active" ;
			}
	
			print qq{<TR bgColor=$bgcolor> \n} ;
			print qq{	<TD valign='top' align=middle>$puserid</td> \n} ;
	        print qq{	<TD valign='top' align=left><font color="#509C10" face="Arial" size="2"> \n } ;
			print qq{	<A HREF="client/client_disp.cgi?pmode=U&puserid=$puserid">$username</a></font></TD> \n } ;
			print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2">$subAffiliateID</font></TD> \n } ;
	        print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2">$lname, $fname</font></TD> \n } ;
	        print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2">$cl_type</font></TD> \n } ;
	        print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2">$listOwner</font></TD> \n } ;
	        print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2">$countryName</font></TD> \n } ;
	        print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2">$ownerType</font></TD> \n } ;
	       	print qq{	<TD valign='top' align=left><font color="black" face="Arial" size="2"> \n } ;
#		my $sth1;
#		my $ex_sub="";
#		if ($ex_subdomain ne "") {
#			$ex_sub=$ex_subdomain eq 'Y' ? qq^AND exclude_subdomain='Y'^ : qq^AND exclude_subdomain='N'^;
#		}
#	
#			if ($brand_type eq "ALL")
#			{
#				if ($old_tid > 0)
#				{
#					my $tag_filter='';
#					if ($old_tid==10) 
#					{
#						if ($tag>0) 
#						{
#							$tag_filter=$tag==1 ? qq^AND tag='0'^ : qq^AND tag<>'0'^;
#						}
#					}
#					$sql = "select brand_id,brand_name,brand_type,third_party_id,tag,exclude_thirdparty,purpose from client_brand_info where client_id=$puserid and status='A' and third_party_id=$old_tid $tag_filter $ex_sub order by brand_name"; 
#				}
#				else
#				{
#					$sql = "select brand_id,brand_name,brand_type,third_party_id,tag,exclude_thirdparty,purpose from client_brand_info where client_id=$puserid and status='A' $ex_sub order by brand_name"; 
#				}
#			}
#			else
#			{
#				if ($old_tid > 0)
#				{
#					my $tag_filter='';
#					if ($old_tid==10) 
#					{
#						if ($tag>0) 
#						{
#							$tag_filter=$tag==1 ? qq^AND tag='0'^ : qq^AND tag<>'0'^;
#						}
#					}
#					$sql = "select brand_id,brand_name,brand_type,third_party_id,tag,exclude_thirdparty,purpose from client_brand_info where client_id=$puserid and status='A' and brand_type='$brand_type' and third_party_id = $old_tid $tag_filter $ex_sub order by brand_name"; 
#				}
#				else
#				{
#					$sql = "select brand_id,brand_name,brand_type,third_party_id,tag,exclude_thirdparty,purpose from client_brand_info where client_id=$puserid and status='A' and brand_type='$brand_type' $ex_sub order by brand_name"; 
#				}
#			}
#			$sth1 = $dbhq->prepare($sql) ;
#			$sth1->execute();
#			my $bname;
#			my $bid;
#			my $btype;
#			my $third_party_id;
#			my $third_party_str;
#			my $tag_val;
#			my $ex_flag;
#			my $purpose;
#			while (($bid,$bname,$btype,$third_party_id,$tag_val,$ex_flag,$purpose) = $sth1->fetchrow_array())
#			{
#				my $extra_str="";
#				if ($tag_val>0) {
#					$extra_str="- 2";
#				}
#				if ($old_tid > 0)
#				{
#	       			print qq{$ex_flag</td><TD align=left><font color="black" face="Arial" size="2"> \n } ;
#				}
#				print "<a href=\"/cgi-bin/edit_client_brand.cgi?bid=$bid&cid=$puserid&mode=U\"><b>$bname $extra_str ($bid)</b></a>";
#				if ($third_party_id > 0)
#				{
#					$sql = "select mailer_name from third_party_defaults where third_party_id=$third_party_id";
#					$sth1a = $dbhq->prepare($sql) ;
#					$sth1a->execute();
#					($third_party_str) = $sth1a->fetchrow_array();
#					$sth1a->finish();
#				}
#				else
#				{
#					$third_party_str="None";
#				}
#				my $dailydeal;
#				my $triggerdeal="";
#				if ($purpose eq "Daily")
#				{
#					$dailydeal= "- <font color=red>Daily Deals</font>"; 
#				}
#				elsif ($purpose eq "Trigger")
#				{
#					$triggerdeal= "- <font color=red>Trigger Deal</font>"; 
#				}
#				elsif ($purpose eq "Normal")
#				{
	#				my $qDaily=qq|SELECT distinct(server_name) FROM brand_host WHERE brand_id=$bid AND server_type='C' LIMIT 1|;
	#				my $sthDaily=$dbhq->prepare($qDaily);
	#				$sthDaily->execute;
	#				my $dd_server=$sthDaily->fetchrow;
	#				$sthDaily->finish;
	#				$dailydeal=$dd_server ? "- <font color=red>Daily Deals</font>" : "";
#				}
	
	#			$sql = "select server_name from brand_host where brand_id=$bid and server_type='O' order by server_name";
	#			$sth1a = $dbhq->prepare($sql) ;
	#			$sth1a->execute();
	#			print "&nbsp;(";
	#			my $temp_str="";
	#			while (($sname) = $sth1a->fetchrow_array())
	#			{
	#				$temp_str = $temp_str . $sname. ",";	
	#			}
	#			$_ = $temp_str;
	#			chop;
	#			$temp_str = $_;
	#			print "$temp_str";
	#			$sth1a->finish();
	#			$sql = "select server_name from brand_host where brand_id=$bid and server_type='Y' order by server_name";
	#			$sth1a = $dbhq->prepare($sql) ;
	#			$sth1a->execute();
	#			print "&nbsp; - ";
	#			$temp_str="";
	#			while (($sname) = $sth1a->fetchrow_array())
	#			{
	#				$temp_str = $temp_str . $sname. ",";	
	#			}
	#			$_ = $temp_str;
	#			chop;
	#			$temp_str = $_;
	#			print "$temp_str";
	#			$sql = "select server_name from brand_host where brand_id=$bid and server_type='H' order by server_name";
	#			$sth1a = $dbhq->prepare($sql) ;
	#			$sth1a->execute();
	#			print "&nbsp; - ";
	#			$temp_str="";
	#			while (($sname) = $sth1a->fetchrow_array())
	#			{
	#				$temp_str = $temp_str . $sname. ",";	
	#			}
	#			$_ = $temp_str;
	#			chop;
	#			$temp_str = $_;
	#			print "$temp_str";
	#			$sql = "select server_name from brand_host where brand_id=$bid and server_type='A' order by server_name";
	#			$sth1a = $dbhq->prepare($sql) ;
	#			$sth1a->execute();
	#			print "&nbsp; - ";
	#			$temp_str="";
	#			while (($sname) = $sth1a->fetchrow_array())
	#			{
	#				$temp_str = $temp_str . $sname. ",";	
	#			}
	#			$_ = $temp_str;
	#			chop;
	#			$temp_str = $_; 
	#			print "$temp_str";
#				print " - $btype - $third_party_str $dailydeal $triggerdeal<br>";
#			}
#			$sth1->finish();
#	        print qq{	</font></TD> \n } ;
	        print qq{	<TD align=center><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/client/client_disp.cgi?pmode=C&puserid=$puserid">Copy</a>&nbsp;&nbsp;<a href="/cgi-bin/client_brand_list.cgi?cid=$puserid">Brands</a></font></TD> \n } ;
			print qq{</TR> \n} ;
	
		}  # end while statement
	
		$sth->finish();
		
	} #end if
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



#===============================================================================
# Sub: write_java
#===============================================================================
sub write_java
{
	print << "end_of_html" ;

	<!-- ------------------- JAVA SCRIPT ----------------------------------- -->
    <script language="JavaScript">
	//------------------------------------------------------
	// Update the list table
	//------------------------------------------------------
    function Save()
    {
		confirm("Are you sure you want to Update the Lists?");
        document.list_upd_form.submit();
        return true;
    }

	//--------------------------------------------------------------------------
	// Check that 'list_name' is NOT Null if Add List being done.
	//--------------------------------------------------------------------------
    function ValidateAdd()
    {
    	if ( document.list_add_form.list_name.value == "" ) 
		{
    		alert("You MUST enter a List Name to add a New List!");
			document.list_add_form.list_name.focus();
    		return false ;
		}
		else
		{
			document.list_add_form.submit();
    		return true ;
		}
    }

	//--------------------------------------------------------------------------
	// Set the 'list_upd_form' field - chg_ind_X (where X = List ID) value to
	// list_id variable passed in.  This is done whenever a CHANGE is made to
	// the list_name or status fields (eg done to ID change fields for Update).
	//--------------------------------------------------------------------------
    function ChgInd(FieldName, ListId)
    {
    	var ObjName;
    	// ---Worked --> document.list_upd_form(FieldName).checked = true ;
    	document.list_upd_form(FieldName).value = ListId ;
    	// alert(document.list_upd_form(FieldName).value + " = " + ListId );
    	return true ;
    }

    </script>

end_of_html

} # end sub write_java
