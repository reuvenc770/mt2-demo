#!/usr/bin/perl
#===============================================================================
# Purpose: Displays and Add/Updates the FILE_LAYOUT rec for a specific user.
# File   : file_layout_upd.cgi
#
# Input  :
#
# Output :
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 9/10/01  Created.
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
my ($sth, $sql, $dbh, $rows, $reccnt ) ;

my (@chkbox_array, $chkbox_field, $chkbox_value) ;
my (@fldpos_array, $fldpos_field, $fldpos_value) ;
my ($loop_cnt, $fld_name, $fld_pos);
my ($db_field, $cat_id);
my (@db_field_array, @cat_id_array, @fldpos_array);
my ($field, $i, $field_cnt);
my ($mesg, $go_back, $go_home, $go_url);

#----- connect to the util database -----
$util->db_connect();
$dbh = $util->get_dbh;

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

print qq{Content-Type: text/html\n\n};    # comment out
print qq{<html><body>};                   # comment out

#------ Get Values from Array of CHKBOX fields (eg db_field|cat_id) ---------
$reccnt = 0;
@chkbox_array = $query->param('chkbox') ;
foreach $chkbox_field (@chkbox_array) 
{
	($db_field, $cat_id) = split(/\|/,$chkbox_field);
	$db_field_array[$reccnt] = $db_field;
	$cat_id_array[$reccnt] = $cat_id;
	$reccnt++;
	print "DbField: $db_field &nbsp;nbsp; CatId: $cat_id <br> \n" ;           # comment out
	print "Field Name: chkbox &nbsp;nbsp; Value: $chkbox_field <br> \n" ;     # comment out
}

#------ Get Values from Array of FLDPOS fields ---------
$reccnt = 0;
@fldpos_array = $query->param('fldpos') ;
foreach $fldpos_field (@fldpos_array) 
{
	if ( $fldpos_field ne "" )
	{
		$fldpos_array[$reccnt] = $fldpos_field ;
		$reccnt++;
		print "Field Name: fldpos &nbsp;nbsp; Value: $fldpos_field <br> \n" ;   # comment out
	}
}

print qq{</body></html>};   # comment out
exit(99);                   # comment out


$rows = 0 ;
$sql = qq{delete from file_layout where user_id = $user_id};
$rows = $dbh->do($sql);

$rows = 0 ;
$field_cnt = @db_field_array;  # set to length of array
for($i=0; $i < $field_cnt; $i++)
{
	$sql = qq{insert into file_layout ( user_id, cat_id, field_position, create_date) values (
		$user_id,  $cat_id_array[$i], $fldpos_array[$i], curdate() ) } ;
	$rows = $dbh->do($sql);
}


$go_back = qq{&nbsp;&nbsp;<a href="file_layout_disp.cgi">Back</a>\n };
$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
$go_url = qq{&nbsp;&nbsp;<a href="sub_disp_add.cgi">Add Subscribers</a>\n };
$mesg = qq{<font color="black"><br><br><b>Successful</b> Add/Update of FILE LAYOUT data.<br><br></b></font>} ;
# $mesg = $mesg . $go_back . $go_home ;
$mesg = $mesg . $go_back . $go_url ;
# util::logerror($mesg) ;
util::confirmation_page('FILE LAYOUT',$mesg);
exit(0) ;

# print $query->redirect("file_layout_disp.cgi?mesg=$mesg") ;
# print $query->redirect("file_layout_disp.cgi") ;


