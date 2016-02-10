#!/usr/bin/perl
#===============================================================================
# Purpose: Logical Delete/Remove of 'list_member' recs.
# File   : sub_del.cgi
#
#--Change Control---------------------------------------------------------------
#  Aug 2, 2001  Mike Baker  Created.
#  Feb 8, 2002  Jim Sobeck  Add logging of emails not removed
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my (@list_array, $upload_file, $email_list_text_area) ;
my ($list_id, $list_name, $email_addr, @email_array);
my (%hash_good_cnt, %hash_bad_cnt, %hash_prev_rem_cnt) ;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $email_user_id;


# ------- Get fields from html Form post -----------------

@list_array = $query->param('list_chkbox');
$upload_file = $query->param('upload_file');
$email_list_text_area = $query->param('email_list_text_area');

# ----- Set Hash Counts to Zero Values  -> Used to Track Adds to 'list_member' table -----
foreach $list_id (@list_array)
{
	$hash_good_cnt{$list_id} = 0 ; 
	$hash_bad_cnt{$list_id} = 0 ; 
	$hash_prev_rem_cnt{$list_id} = 0 ; 
}

# ----- connect to the util database -------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	&process_list();
}

#---- Print Delete Statistics -----------------
&print_del_summary();


$util->clean_up();
exit(0) ;



#===============================================================================
# Sub: process_list
#  1. Split html TEXTAREA field into separate fields - One for each Email Addr
#  2. Loop for ALL 'Lists' selected
#  3. Pass control to add 'list_member' recs
#===============================================================================
sub process_list 
{
	my ($add_good_cnt, $add_bad_cnt, $add_dup_cnt, $tot_email_cnt ) ;
	my ($list_id, $email_addr, @email_array) ;
	my ($email_exists, $sth1, $sth2 ) ;

	#-----------------------------------------------------------------------------
	#   1. Remove Space, NewLine, CR, FF, Tab from text string 
	#   2. if Mult Pipes Exist together change to Single Pipe char (eg from 2-999)
	#   3. Split text line via Pipe char into Array to get individual Email Addrs
	#-----------------------------------------------------------------------------
	$email_list_text_area =~ s/[ \n\r\f\t]/\|/g ;    
	$email_list_text_area =~ s/\|{2,999}/\|/g ;           
	@email_array = split '\|', $email_list_text_area ;

	foreach $list_id (@list_array) 
	{
		if ( $list_id ne "dummy" )
		{
			foreach $email_addr (@email_array)
			{
				&del_upd_list_member($list_id, $email_addr);
				&del_upd_email_user($email_addr);
			}
		}
	}

} # end of sub


#===============================================================================
# Sub: process_file
#  1. Open file
#  2. Loop - Read File til EOF 
#  3. Update 'list_member' for Logical Delete/Remove (eg set status = R )
#      - set proper counts (eg good, bad, total)
#===============================================================================
sub process_file 
{
	my ($list_id, $email_addr, @email_array) ;
	my ($status, $email_exists, $sth1, $sth2 ) ;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($ary_len, $i);
	my $upload_dir_unix;

	# get upload subdir

	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script

	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}sub_del.cgi.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	open (LOG, ">> /tmp/sub_del.log");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_addr, @rest_of_line) = split('\|', $line) ;
		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
		$email_addr =~ s/\s//g ;     # remove all white space global
		
		#---- Loop thru list_array and Add a 'list_member' foreach list_id  -----
		$ary_len = @list_array -1 ;
		for($i = 0; $i <= $ary_len; $i++)
		{
			if ( $list_array[$i] ne "dummy" )   # Skip list_id(s) eq "dummy" 
			{
				&del_upd_list_member($list_array[$i], $email_addr);
			} 

			if ( $i == $ary_len )
			{
				&del_upd_email_user($email_addr);
			}
		} 
	} 

	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in

} # end of sub


#===============================================================================
# Sub: del_upd_list_member
#===============================================================================
sub del_upd_list_member
{
	my ($list_id, $email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i) ; 
	
	# ---- See if Email Addr Already Exists for specified List -----

	$email_exists = 0 ;
	$sql = "select count(*) from list_member, email_user
		where list_member.list_id = $list_id and 
		list_member.email_user_id = email_user.email_user_id and
		email_user.email_addr = '$email_addr'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($email_exists) = $sth1->fetchrow_array();
	$sth1->finish();

	# ------- If Email Addr Does NOT Exist for the specified List then Add it ------ 

	$email_addr =~ s/\s//g ;   # remove ALL white space from EmailAddr
	if ( $email_addr eq "" )
	{
		$hash_bad_cnt{$list_id} = $hash_bad_cnt{$list_id} + 1 ;
	}
	elsif ( $email_exists > 0  )
	{
		# email exists in the list - find out the status

		$sql = "select list_member.status, email_user.email_user_id 
			from list_member, email_user
		 	where list_member.list_id = $list_id and 
			list_member.email_user_id = email_user.email_user_id and
			email_user.email_addr = '$email_addr'";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($status, $email_user_id) = $sth1->fetchrow_array();
		$sth1->finish();

		if ( $status eq 'R' ) 
		{	
			# Record was Previously Removed 

			$hash_prev_rem_cnt{$list_id} = $hash_prev_rem_cnt{$list_id} + 1 ;
		}
		else
		{	
			# Update Existing list_member rec -- Reset Status, Subscribe and UnSubscribe fields

			$sql = "update list_member set status = 'R', unsubscribe_datetime = now()
				where list_id = $list_id and email_user_id = $email_user_id";
			$rows = $dbhu->do($sql) ;
			if ($dbhu->err() == 0 ) 
			{
				$hash_good_cnt{$list_id} = $hash_good_cnt{$list_id} + 1 ;
			}
			else 
			{
				$hash_bad_cnt{$list_id} = $hash_bad_cnt{$list_id} + 1 ;
				print LOG "$email_addr\n";
			}
		}
		
	}
	else
	{
		$hash_bad_cnt{$list_id} = $hash_bad_cnt{$list_id} + 1 ;
		print LOG "$email_addr\n";
	}

} # end of sub


#===============================================================================
# Sub: print_del_summary
#===============================================================================
sub print_del_summary
{

	my ($list_cnt, $color, $tot_good, $tot_dup, $tot_bad);
	my ($sth1, $list_name) ;

	$tot_good = 0 ;
	$tot_bad  = 0 ;
	$tot_dup  = 0 ;

util::header("REMOVE SUBSCRIBERS SUMMARY");    # Print HTML Header
#-----------------------------------------------------------
#  BEGIN HEADER FIX 
#-----------------------------------------------------------
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
<TBODY>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
<TBODY>
<TR>
<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>
<B>Confirmation</B></FONT></TD></TR>
</TBODY>
</TABLE>
end_of_html
#-----------------------------------------------------------
#  END HEADER FIX 
#-----------------------------------------------------------


	print << "end_of_html" ;
	
	<!--  BEGIN My Tbl Definition -------------------------------------------- -->
	<!--  Form Buttons - Images ---------------------------------------- -->
	<FORM name="sub_del_mesg_form" action=mainmenu.cgi method=post>

	<TABLE align="left" cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD colspan="6" align=middle width="100%" height=15>
	<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
	<b>Summary of Subscriber Emails Removed from Lists</b></font></TD>
	</TR>
	<TR bgColor=#509C10 height=15>
	<td colspan="6" >&nbsp;</td>
	</tr>

	<!-- bgColor = "#509C10" ;     # Dark Green     -->
	<!-- bgcolor = "#EBFAD1" ;     # Light Green    -->
	<!-- bgcolor = "$alt_light_table_bg" ;     # Light Yellow   -->

	<!-- Table Headings ----------------------------------------------------- -->
	<TR> 
		<TD bgcolor="#509C10" align="left" width="05%">&nbsp;</td>
		<TD bgcolor="#509C10" align="center" width="25%">	 
		<FONT face="verdana,arial,helvetica,sans serif" color=white size=2><b>List Name</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Number<br>Removed</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Number<br>Previously<br>Removed</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Invalid<br>Removes</b></font></td>
		<TD bgcolor="#509C10" align="left" width="05%">&nbsp;</td>
	</TR> 
end_of_html

	$list_cnt = 0 ;
	foreach $list_id (@list_array)
	{
		if ( $list_id ne "dummy" )
		{ 
			$list_cnt++;
			if ( ( $list_cnt % 2 ) == 0 ) 
			{
				$color = "#EBFAD1" ;   # Light Green 
			}
			else
			{
				$color = "$alt_light_table_bg" ;   # Light Yellow
			}
			
			$sql = qq{ select list_name from list where list_id = $list_id } ;
			$sth1 = $dbhq->prepare($sql) ;
			$sth1->execute();
			($list_name) = $sth1->fetchrow_array();
			$sth1->finish();

			$tot_good = $tot_good + $hash_good_cnt{$list_id} ;
			$tot_dup  = $tot_dup  + $hash_prev_rem_cnt{$list_id} ;
			$tot_bad  = $tot_bad  + $hash_bad_cnt{$list_id} ;

			print qq{ <!-- Table Summary Data ------------------------------------------------ --> \n } ;
			print qq{ <TD bgcolor="$color" align="left" width="5%">&nbsp;</td> \n  } ;
			print qq{ <TD bgcolor="$color" align="left" width="25%"> \n   } ;
			print qq{ <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>$list_name</FONT></TD> \n } ;
			print qq{ <TD bgcolor="$color" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>$hash_good_cnt{$list_id}</td> \n } ;
			print qq{ <TD bgcolor="$color" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>$hash_prev_rem_cnt{$list_id}</td> \n } ;
			print qq{ <TD bgcolor="$color" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>$hash_bad_cnt{$list_id}</td> \n } ;
			print qq{ <TD bgcolor="$color" align="left" width="5%">&nbsp;</td> \n } ;
			print qq{ </TR> \n  } ;
		} 
	}
	
	print << "end_of_html" ;

	<!-- Table TOTAL Line(s) -------------------------------------------------- -->
	<TR> 
		<TD bgcolor="#EBFAD1" align="left"  width="5%" >&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left"  width="25%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left"  width="5%" >&nbsp;</td>
	</TR> 
	<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="5%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="25%">	 
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>Totals</b></FONT></TD> 
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_good</b></td>
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_dup</b></td>
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_bad</b></td>
		<TD bgcolor="#EBFAD1" align="left" width="5%">&nbsp;</td>
	</TR> 

	<tr>
	<td colspan="6" bgcolor="#EBFAD1" align="center" width="100%"><br>
	<INPUT type="image" src="$images/home_blkline.gif" hspace=7  width="72"  height="23" border=0>
	</td>
	</tr>

	</tbody>
	</table>
	</FORM> 

</td>
</tr>
<tr>
<td>
end_of_html

	util::footer();
}  

#===============================================================================
# Sub: del_upd_email_user
#   If user is NOT active in any 'list_member' rec then set 'email_user' status
#   to logical delete.
#===============================================================================

sub del_upd_email_user
{
	my ($email_addr) = @_ ;

	$sql = "update email_user set status = 'D' where email_addr = '$email_addr'";
	$rows = $dbhu->do($sql);
} 
