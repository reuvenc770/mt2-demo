#!/usr/bin/perl
#===============================================================================
# Purpose: Displays the 'Current File Layout' for a specific user.
# File   : file_layout_instructions.cgi
#
# Input  :
#
# Output :
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 9/06/01  Created.
#===============================================================================

#----- include Perl Modules ------
use strict;
use CGI;
use util;

#---- get some objects to use later -----
my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my ($cat_id, $category, $description, $bgcolor) ;
my ($sql2, $sth2, $values_reccnt, $db_value, $value_descr) ;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my ($first_name, $last_name, $company, $nbr_fields, $name_and_company);

#----  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}


#----- Get User Info and Number of Fields in File Layout table ----------
$sql = qq{select first_name, last_name, company, count(*) 
	from   user u, file_layout fl
	where  u.user_id = $user_id
	and    u.user_id = fl.user_id
	group  by first_name, last_name, company };

$sth = $dbhq->prepare($sql) ;
$sth->execute();
($first_name, $last_name, $company, $nbr_fields) = $sth->fetchrow_array();
$sth->finish();
$name_and_company = qq{<font color="black"><b>$first_name $last_name</b></font> };
if ( $company ne "" )
{
	$name_and_company = qq{$name_and_company of <font color="black"><b>$company</b></font>} ;
}

util::header("CURRENT FILE LAYOUT");    #--- Print HTML Header ---

print << "end_of_html";
<!-- <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>  -->
<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#E3FAD1 border=0>
<TBODY>
<TR align=top bgColor=#509C10 height=18>
	<TD colspan=4 align=middle width="100%" height=15>
		<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
		<a name="DocTop"><B>Your Current File Layout</B></a>
		</FONT>
	</TD>
</TR>

<TR> 
	<TD colspan=4 align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<br>This layout allows you to <b>Batch Upload Email Data</b> to the system from an
		file.<br>
		</font>
	</TD>
</TR>

<TR> <!-- Begin File Layout Notes ------------------------------ -->
	<TD colspan=4 width="100%" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Notes:</b>
		</font>
	</TD>
</TR>

<TR>
	<TD colspan=1 width="02%" valign="top" align="center">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>1. </b>
		</font>
	</TD>
	<TD colspan=3 width="95%" valign="top" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Pos.</b> Denotes the fields position within the file, 1 is first field, 2 is second etc.
		</font>
	</TD>
</TR>
<TR>
	<TD colspan=1 width="02%" valign="top" align="center">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>2. </b>
		</font>
	</TD>
	<TD colspan=3 width="95%" valign="top" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Field Name</b> Name decribing the demographic data you are uploading.
		</font>
	</TD>
</TR>
<TR>
	<TD colspan=1 width="02%" valign="top" align="center">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>3. </b>
		</font>
	</TD>
	<TD colspan=3 width="95%" valign="top" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Value</b> Valid values for the field being uploaded.  If the value column
		is blank then you may enter any desired data.  If there are values you may
		only enter the values bolded to the left of the equals sign.
</TR>
<TR>
	<TD colspan=1 width="02%" valign="top" align="center">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>4. </b>
		</font>
	</TD>
	<TD colspan=3 width="95%" valign="top" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Description</b> A brief description of the field being uploaded.
	</TD>
</TR>
<TR>
	<TD colspan=1 width="02%" valign="top" align="center">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>5. </b>
		</font>
	</TD>
	<TD colspan=3 width="95%" valign="top" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		The <b>Email Address</b> is a mandatory field and MUST always be the first field.
	</TD>
</TR>
<TR>
	<TD colspan=1 width="02%" valign="top" align="center">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>6. </b>
		</font>
	</TD>
	<TD colspan=3 width="95%" valign="top" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		If the <b>Email Type</b> is NOT specified it defaults to '<b>H</b>' for HTML.
	</TD>
</TR>
<TR>
	<TD colspan=4 width="100%">
	<br>
	<A HREF="#FileExamples">Click here for Example Layouts</a><br><br>
	</td>
</TR>  <!-- End File Layout Notes ------------------------------ -->

<TR> 
	<TD colspan=4 align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Current 'File Layout' for $name_and_company.
		<br>
		This file contains <b>$nbr_fields</b> fields separated via the <b>comma</b>, <b>tab</b>,
		or the <b>pipe</b> character.  Listed below is the detailed file specification.
		<br><br>
		</font>
	</TD>
</TR>

<TR> <!--  Begin Headings for Detailed Specifications ----------- -->
	<TD colspan=1 width="05%" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Pos.</b>
		</font>
	</TD>
	<TD colspan=1 width="20%" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Field Name</b>
		</font>
	</TD>
	<TD colspan=1 width="40%" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Value</b>
		</font>
	</TD>
	<TD colspan=1 width="30%" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<b>Description</b>
		</font>
	</TD>
</TR> <!--  End Headings for Detailed Specifications ----------- -->

end_of_html

	#---------------------------------------------------------------------------
	# 1. Outer loop joins FILE_LAYOUT and DEMO_CAT tables, prints Pos., 
	#    Field Name and Description.
	# 2. Inner loop gets ALL DEMO_REF recs to print the VALUE col info.
	#---------------------------------------------------------------------------
	#----- Get ALL recs w/in FILE_LAYOUT for specific user --------------------
	$sql = qq{select dc.cat_id, category, dc.description from demo_cat dc, file_layout fl 
		where user_id = $user_id and dc.cat_id = fl.cat_id } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	
	#--- Begin Outer Loop ---
	while ( ($cat_id, $category, $description) = $sth->fetchrow_array() )
	{
		$reccnt++;
		if ( ($reccnt % 2) == 1 ) 
		{
			$bgcolor = $alt_light_table_bg ;
		}
		else
		{
			$bgcolor = $light_table_bg ;
		}

		print qq{
			<TR bgcolor="$bgcolor"> 
				<TD valign=top colspan=1 width="05%" align="left">
					<FONT face="verdana,arial,helvetica,sans serif" color="black" size=2>
					<b>$reccnt.</b>
					</font>
				</TD>
				<TD valign=top colspan=1 width="20%" align="left">
					<FONT face="verdana,arial,helvetica,sans serif" color="black" size=2>
					$category
					</font>
				</TD>

				<TD valign=top colspan=1 width="40%" align="left">
					<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			} ; # end print qq statement 
		

		#------- Get ALL Values from DEMO_REF (if they exist) ---------------
		$sql2 = qq{ select db_value, description from demo_ref 
			where cat_id = $cat_id order by disp_order, db_value } ;

		$sth2 = $dbhq->prepare($sql2) ;
		$sth2->execute();
		$values_reccnt = 0 ;
		
		#--- Begin Inner Loop ----------------
		while ( ($db_value, $value_descr) = $sth2->fetchrow_array() )
		{
			$values_reccnt++;
			print qq{ <font color="black"><b>$db_value</b></font> = $value_descr <br> \n } ;
		}
		$sth2->finish();
		print qq{</font> </TD> \n } ;

		print qq{
				<TD valign=top colspan=1 width="35%" align="left">
					<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
					<font color="black">$description</font> 
					</font>
				</TD>
			</TR>
			} ; # end print qq statement 

	}  # end Outer while loop

	$sth->finish();

	print << "end_of_html";

<TR>
	<TD colspan=4 align=left width="100%" height=15>
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2>
	<A NAME="FileExamples"><br><br><b>File Examples:</b>&nbsp;&nbsp;<a href="#DocTop"> Top </a></font>
	</TD>
</TR> 

<TR>
	<TD colspan=1 valign=top align=left width="15%">
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2>
	<b>Example 1:</b></font>
	</TD>
	<TD colspan=3 align=left width="90%">
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2>
	This file is an ascii <b>comma</b> delimited file containing the following fields:<br>
	&nbsp;&nbsp;1. Email Address,  2. Email Type, 3. Gender, 4. Income
	</TD>
</TR> 
<TR>
	<TD colspan=1 align=left width="15%"> &nbsp; </TD>
	<TD colspan=3 align=left width="90%">
	<FONT face=Verdana,Arial,Helvetica,sans-serif color="black" size=2>
	msmyth\@nownet.edu, H, M, 13<br>
	aharper\@hotmail.com, , , 9<br>
	btaylor\@mymail.net,T,F,<br>
	ttucker\@aolme.com, A, F, 4<br>
	</TD>
</TR> 

<TR>
	<TD colspan=4>
	<br><br>
	</TD>
</TR>

<TR>
	<TD colspan=1 valign=top align=left width="15%">
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2>
	<b>Example 2:</b></font>
	</TD>
	<TD colspan=3 align=left width="90%">
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2>
	This file is an ascii <b>pipe</b> delimited file containing the following fields:<br>
	&nbsp;&nbsp;1. Email Address,  2. Gender, 3. Marital Status, 4. Education, 5. Country
	</TD>
</TR> 
<TR>
	<TD colspan=1 align=left width="15%"> &nbsp; </TD>
	<TD colspan=3 align=left width="90%">
	<FONT face=Verdana,Arial,Helvetica,sans-serif color="black" size=2>
	msmyth\@nownet.com|m|widow|5|usa<br>
	bbaker\@doomnet.com|F|Married|2|CAN<br>
	aharper\@doomnet.net|||4|<br>
	xman\@nowcom.net||||USA<br>
	htyme\@mymail.com|m|SINGLE|3|usa<br>
	</TD>
</TR
</TBODY>
</TABLE>

end_of_html

$util->footer();
$util->clean_up();
exit(0);
