#!/usr/bin/perl
#===============================================================================
# Purpose: Displays the HTML page to add 'list_member' recs to identified lists.
# File   : sub_disp_add.cgi
#
# Input  :
#   2. the 'list' table to display valid Lists to attach emails to.
#   3. List of Email Addrs or a FileName with ONE Email Address per line.
#
# Output :
#   1. Added recs to 'list_member' table.
#   2. Control passed to 'sub_add.cgi' to Add Members to Specified Lists.
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 8/01/01  Created.
# Jim Sobeck, 05/20/02 Added Date Captured and Member Source Fields
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

# declare variables

my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my ($go_back, $go_home, $mesg, $list_id, $list_name, $chkbox_name);
my $images = $util->get_images_url;
my ($cat_id, $category, $db_field, $html_disp_order) ;
my (%file_fields, $field_position, $key, $value, $checked, $nbr_cols) ;
my ($file_layout_reccnt, $file_layout_str, $category_name);
my ($first_name, $last_name, $company);
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $rows;
my $errmsg;

#----- connect to the util database -----
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

# check if any email lists

&do_lists_exist();

# get some information for use on the screen

&format_file_layout_example();

# print out html screen

util::header("ADD SUBSCRIBER");
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3> <B> Add New Subscribers</B> </FONT></TD>
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
			Enter a file with email addresses or enter email addresses manually.
			Only email addresses obtained with the addressee's permission
			should be used. <BR></FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0> 
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=middle>

				<FORM action="sub_add_file.cgi" method="post" encType="multipart/form-data">

				<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor="$light_table_bg" border=0>
				<TBODY>
				<TR align=top bgColor=#509C10 height=18>
				<TD vAlign=top align=left height=15><IMG height=7 src="$images/blue_tl.gif" 
					width=7 border=0></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD align=middle height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
					<B>File Upload</B> </FONT></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG height=7 src="$images/blue_tr.gif" width=7 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle>
			
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR> 
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Your file will be processed offline and you will receive an email 
						when it is finished<br><br></font></TD>
					</TR>
					<TR> 
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<b>Pick an Email List</b></font></TD>
					</TR>
					<tr>
					<td>

						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TBODY>
end_of_html

#===========================================================================
# Loop - Get ALL Lists that belong to the User
#===========================================================================

$reccnt = 0 ;
$nbr_cols = 3 ;
$sql = "select list_id, list_name, status from list 
	where user_id = $user_id and status = 'A' order by list_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($list_id, $list_name) = $sth->fetchrow_array())
{
	$reccnt++;
	if (($reccnt % $nbr_cols) == 1) 
	{
		print "<TR>\n";
	}

	print qq { <TD><INPUT type="radio" name="list_id_file" value="$list_id">
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				$list_name</FONT></TD> \n } ;

	if (($reccnt % $nbr_cols) == 0) 
	{
		print "</TR>\n";
	}
}
$sth->finish();

if (($reccnt % $nbr_cols) != 0) 
{
	print "</TR>\n";
}
	
	print << "end_of_html";
						<TR>
						<TD><IMG height=7 src="$images/spacer.gif"></TD>
						</TR>
						</TBODY>
						</TABLE>
	
					<TR> 
			  		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			    		Your File Layout is shown below.  The example shown 
						uses commas to separate fields.</font></TD> 
			  		</TR> 
			  		<TR>
			  		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<TEXTAREA name="example" align="left" readonly rows=1 wrap=off 
						cols=75>$file_layout_str</TEXTAREA></FONT></TD>
			  		</TR>
			  		<TR>
			  		<TD bgColor="$light_table_bg"><input type=button value="File Instructions" 
						onClick="location.href='fl_instructions.cgi';">&nbsp;&nbsp;
			    		<input type=button value="Update Layout"     
						onClick="location.href='fl_disp.cgi';"> <br> </td>
			  		</TR>
					<TR>
					<TD><IMG height=4 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Enter the File Name to Upload in the text box below.</FONT></TD>
					</TR>
					<TR>
					<TD><IMG height=4 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT type=file name="upfile" size="50">
						<INPUT type="submit" value="Upload File Now"></FONT></TD>
					</TR>
					<TR>
					<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1 height=10>
				<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
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
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=middle>

				<FORM action="sub_add_manual.cgi" method="post">

				<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
				<TBODY>
				<TR align=top bgColor=#509C10 height=18>
				<TD vAlign=top align=left height=15>
					<IMG height=7 src="$images/blue_tl.gif" width=7 border=0></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD align=middle height=15>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR bgColor=#509C10 height=15>
					<TD align=middle width="100%" height=15>
						<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Manually Add Email Addresses</B> </FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				
				</TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG height=7 src="$images/blue_tr.gif" width=7 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR>
					<TD align=middle height="5"><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					<TR> 
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<b>Pick an Email List</b></font></TD>
					</TR>
					<tr>
					<td>

						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TBODY>
end_of_html

#===========================================================================
# Loop - Get ALL Lists that belong to the User
#===========================================================================

$reccnt = 0 ;
$nbr_cols = 3 ;
$sql = "select list_id, list_name, status from list 
	where user_id = $user_id and status = 'A' order by list_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($list_id, $list_name) = $sth->fetchrow_array())
{
	$reccnt++;
	if (($reccnt % $nbr_cols) == 1) 
	{
		print "<TR>\n";
	}

	print qq { <TD><INPUT type="radio" name="list_id_man" value="$list_id">
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				$list_name</FONT></TD> \n } ;

	if (($reccnt % $nbr_cols) == 0) 
	{
		print "</TR>\n";
	}
}
$sth->finish();

if (($reccnt % $nbr_cols) != 0) 
{
	print "</TR>\n";
}
	
	print << "end_of_html";
						<TR>
						<TD><IMG height=7 src="$images/spacer.gif"></TD>
						</TR>
						</TBODY>
						</TABLE>
	
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Type email addresses in the box below.  Hit ENTER after each
						email address.  Each email address must be on a separate line.</FONT> </TD>
					</TR>
                   	<TR>
                    <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
                        <input type="radio" name="emailtype" value="H" checked>
                        HTML Emails   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="emailtype" value="T">
                        Text Emails   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </FONT> </TD>
                    </TR>
					<TR>
					<TD><IMG height=4 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
					<TD>&nbsp;&nbsp;&nbsp;
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<TEXTAREA name="email_list_text_area" rows=10 wrap=off cols=45></TEXTAREA>
	    				<INPUT type="submit" value="Add Addresses Now"></FONT></TD>
					</TR>
					<TR>
					<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1 height=10>
				<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
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
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD width=100% align=center>
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border="0"></A></td>
			</TR>
			</TBODY>
			</TABLE>

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

$util->footer();

$util->clean_up();
exit(0);

#-------- End of Main Logic -----------------------------------

#===============================================================================
# Sub: format_file_layout_example
#===============================================================================
sub format_file_layout_example
{
	my (@fl_fields, %hsh_fl_text, $key_sort);
	my $count;

	# see if this user has a user_file_layout record

	$sql = "select count(*) from user_file_layout where user_id = $user_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($count) = $sth->fetchrow_array();
	$sth->finish();

	# if this user does not have a record, then create a default with just the email address

	if ($count == 0)
	{
		$sql = "insert into user_file_layout (user_id, email_addr_pos, create_datetime)
			values ($user_id, 1, now())";
		$rows = $dbhu->do($sql);
		if ($dbhu->err() != 0)
    	{
        	$errmsg = $dbhu->errstr();
        	util::logerror("Inserting campaign record $sql : $errmsg");
        	exit(0);
    	}
	}

	#----- get ALL fields from USER_FILE_LAYOUT to build text area string -----------

	$sql = "select email_addr_pos, email_type_pos, gender_pos, first_name_pos, 
		middle_name_pos, last_name_pos, birth_date_pos, address_pos, address2_pos,
		city_pos, state_pos, zip_pos, country_pos, marital_status_pos, occupation_pos,
		job_status_pos, income_pos, education_pos, date_capture_pos, member_source_pos, phone_pos,source_url_pos
		from user_file_layout where user_id = $user_id";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	(@fl_fields) = $sth->fetchrow_array() ;
	$sth->finish();

	#--- Set html Text based on field order of above select statement ------------
	if ($fl_fields[0] ne "" )  { $hsh_fl_text{$fl_fields[0]}  = 'Email Address' ; }
	if ($fl_fields[1] ne "" )  { $hsh_fl_text{$fl_fields[1]}  = 'Email Type' ; }
	if ($fl_fields[2] ne "" )  { $hsh_fl_text{$fl_fields[2]}  = 'Gender' ; }
	if ($fl_fields[3] ne "" )  { $hsh_fl_text{$fl_fields[3]}  = 'First Name' ; }
	if ($fl_fields[4] ne "" )  { $hsh_fl_text{$fl_fields[4]}  = 'Middle Name' ; }
	if ($fl_fields[5] ne "" )  { $hsh_fl_text{$fl_fields[5]}  = 'Last Name' ; }
	if ($fl_fields[6] ne "" )  { $hsh_fl_text{$fl_fields[6]}  = 'Birth Date' ; }
	if ($fl_fields[7] ne "" )  { $hsh_fl_text{$fl_fields[7]}  = 'Address Line 1' ; }
	if ($fl_fields[8] ne "" )  { $hsh_fl_text{$fl_fields[8]}  = 'Address Line 2' ; }
	if ($fl_fields[9] ne "" )  { $hsh_fl_text{$fl_fields[9]}  = 'City' ; }
	if ($fl_fields[10] ne "" ) { $hsh_fl_text{$fl_fields[10]} = 'State' ; }
	if ($fl_fields[11] ne "" ) { $hsh_fl_text{$fl_fields[11]} = 'Zip' ; }
	if ($fl_fields[12] ne "" ) { $hsh_fl_text{$fl_fields[12]} = 'Country' ; }
	if ($fl_fields[13] ne "" ) { $hsh_fl_text{$fl_fields[13]} = 'Marital Status' ; }
	if ($fl_fields[14] ne "" ) { $hsh_fl_text{$fl_fields[14]} = 'Occupation' ; }
	if ($fl_fields[15] ne "" ) { $hsh_fl_text{$fl_fields[15]} = 'Job Status' ; }
	if ($fl_fields[16] ne "" ) { $hsh_fl_text{$fl_fields[16]} = 'Household Income' ; }
	if ($fl_fields[17] ne "" ) { $hsh_fl_text{$fl_fields[17]} = 'Education Level' ; }
	if ($fl_fields[18] ne "" ) { $hsh_fl_text{$fl_fields[18]} = 'Date Captured' ; }
	if ($fl_fields[19] ne "" ) { $hsh_fl_text{$fl_fields[19]} = 'Member Source' ; }
	if ($fl_fields[20] ne "" ) { $hsh_fl_text{$fl_fields[20]} = 'Phone' ; }
	if ($fl_fields[21] ne "" ) { $hsh_fl_text{$fl_fields[21]} = 'Source URL' ; }

	#--- Numerical Sort hash key to build user_file_layout_str ------
	foreach $key_sort (sort {$a <=> $b} keys %hsh_fl_text)
	{
		$file_layout_str = $file_layout_str . ", " . $hsh_fl_text{$key_sort};
	}
	$file_layout_str =~ s/^, // ;

} # end sub format_file_layout_example

#==============================================================================
# Sub: do_lists_exist 
#  - LIST records must exist for the specific user in order to add Email data
#  - If no LIST recs exist display mesg and stop processing
#==============================================================================

sub do_lists_exist
{
	#===========================================================================
	# See if any 'list' recs exist for the given user - If not disp mesg & stop
	#===========================================================================
	$sth = $dbhq->prepare("select count(*) from list where user_id = $user_id") ;
	$sth->execute();
	($reccnt) = $sth->fetchrow_array() ;
	$sth->finish();
	if ( $reccnt == 0 )
	{  
		$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
		$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi">Home</a>\n };
		$mesg = qq { <br><br><font color=#509C10>No Lists exist for this user <$user_id - $reccnt>.  You MUST have 
			one or more lists before adding subscribers.<br> 
			Use </font>'Add or Update Lists'<font color=#509C10> in the Main Menu 
			under </font>'Subscribers'<font color=#509C10> to create Lists; then you may<br>
			add subscribers.</font><br> } ;
		$mesg = $mesg . $go_back . $go_home ;
		util::logerror($mesg) ;
		exit(0);
	}

} # end sub do_lists_exist
 
