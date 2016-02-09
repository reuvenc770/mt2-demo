#!/usr/bin/perl
#===============================================================================
# Purpose: Displays and Add/Updates the USER_FILE_LAYOUT rec for a specific user.
# File   : fl_upd.cgi
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 9/10/01  Created.
# Jim Sobeck, 05/20/02 Added date_capture_pos and member_source_pos
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
my ($sql, $dbh, $rows) ;

my (@ary_chkbox_fields) ;
my (@ary_fldpos_fields, $str_fldpos_field, @ary_fldpos_nonblank_fields) ;
my ($i);
my ($mesg, $go_back, $go_home, $go_url);
my (@fl_db_name, $fl_pos, $ary_len );

# my ($db_field, $cat_id);   # mebtest
# my ($field, $i, $field_cnt);
# my (@db_field_array, @cat_id_array, @ary_str_fldpos_fields);

#---- Vars used for insert of tbl: user_file_layout ------------
my ($email_addr_pos, $email_type_pos,     $gender_pos);
my ($first_name_pos, $middle_name_pos,    $last_name_pos);
my ($birth_date_pos,  $address_pos,        $address2_pos);
my ($city_pos,       $state_pos,          $zip_pos);
my ($country_pos,    $marital_status_pos, $occupation_pos);
my ($job_status_pos, $income_pos,         $education_pos);
my ($date_capture_pos, $member_source_pos, $phone_pos, $source_url_pos);

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

	#----- Set user_file_layout fields to Null ----------------
	$email_addr_pos = "null";
	$email_type_pos = "null";
	$gender_pos = "null";
	$first_name_pos = "null";
	$middle_name_pos = "null";
	$last_name_pos = "null";
	$birth_date_pos = "null";
	$address_pos = "null";
	$address2_pos = "null";
	$city_pos = "null";
	$state_pos = "null";
	$zip_pos = "null";
	$country_pos = "null";
	$marital_status_pos = "null";
	$occupation_pos = "null";
	$job_status_pos = "null";
	$income_pos = "null";
	$education_pos = "null";
	$date_capture_pos = "null";
	$member_source_pos = "null";
	$phone_pos = "null";
	$source_url_pos = "null";

# print qq{Content-Type: text/html\n\n};    # comment out
# print qq{<html><body>};                   # comment out

#------ Get Values from Array of CHKBOX fields (eg db_field|cat_id) ---------
@ary_chkbox_fields = $query->param('chkbox') ;

#------ Put values ne "" on the New Array for processing (eg skip fields = "") -----
@ary_fldpos_fields = $query->param('fldpos') ;
foreach $str_fldpos_field (@ary_fldpos_fields) 
{
 	if ( $str_fldpos_field ne "" )
 	{
 		push @ary_fldpos_nonblank_fields, $str_fldpos_field ;
	}
}

$ary_len = (@ary_chkbox_fields);
$ary_len = $ary_len - 1;  
for($i = 0; $i <= 21; $i++)
{
	if ($ary_chkbox_fields[$i] eq "email_addr_pos")     { $email_addr_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "email_type_pos")     { $email_type_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "gender_pos")         { $gender_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "first_name_pos")     { $first_name_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "middle_name_pos")    { $middle_name_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "last_name_pos")      { $last_name_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "birth_date_pos")     { $birth_date_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "address_pos")        { $address_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "address2_pos")       { $address2_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "city_pos")           { $city_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "state_pos")          { $state_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "zip_pos")            { $zip_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "country_pos")        { $country_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "marital_status_pos") { $marital_status_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "occupation_pos")     { $occupation_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "job_status_pos")     { $job_status_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "income_pos")         { $income_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "education_pos")      { $education_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "date_capture_pos")     { $date_capture_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "member_source_pos")     { $member_source_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "phone_pos")     { $phone_pos = $ary_fldpos_nonblank_fields[$i]; }
	if ($ary_chkbox_fields[$i] eq "source_url_pos")     { $source_url_pos = $ary_fldpos_nonblank_fields[$i]; }
}

$rows = 0 ;
$sql = qq{delete from user_file_layout where user_id = $user_id};
$rows = $dbh->do($sql);

$rows = 0 ;
$sql = qq{insert into user_file_layout ( user_id, create_datetime,
	email_addr_pos,     email_type_pos,      gender_pos,
	first_name_pos,     middle_name_pos,     last_name_pos,
	birth_date_pos,     address_pos,         address2_pos,
	city_pos,           state_pos,           zip_pos,
	country_pos,        marital_status_pos,  occupation_pos,
	job_status_pos,     income_pos,          education_pos, 
	date_capture_pos, member_source_pos, phone_pos,source_url_pos )
	values ( $user_id, curdate(),
	$email_addr_pos,     $email_type_pos,      $gender_pos,
	$first_name_pos,     $middle_name_pos,     $last_name_pos,
	$birth_date_pos,     $address_pos,         $address2_pos,
	$city_pos,           $state_pos,           $zip_pos,
	$country_pos,        $marital_status_pos,  $occupation_pos,
	$job_status_pos,     $income_pos,          $education_pos,
	$date_capture_pos,   $member_source_pos, $phone_pos, $source_url_pos ) };
$rows = $dbh->do($sql);

#  print "sql = $sql <br> \n" ;   # comment out

$go_back = qq{&nbsp;&nbsp;<a href="fl_disp.cgi">Back</a>\n };
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

