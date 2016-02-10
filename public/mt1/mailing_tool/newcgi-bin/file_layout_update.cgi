#!/usr/bin/perl
#===============================================================================
# Purpose: Displays and Add/Updates the USER_FILE_LAYOUT rec for a specific user.
# File   : fl_upd.cgi
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 9/10/01  Created.
# Jim Sobeck, 05/20/02 Added date_capture_pos and member_source_pos
#===============================================================================

#print "Content-type: text/html\n\n";
#print "<code>\n";

#foreach $key (sort keys(%ENV)) {
#  print "$key = $ENV{$key}<p>";
#}

#read(STDIN, $FormData, $ENV{'CONTENT_LENGTH'}); 

#print "\n\n\nFORM DATA: \n\n" . $FormData;

#exit;


#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use CGI::Carp qw(fatalsToBrowser);
use util;
use MIME::Base64;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sql, $dbh, $rows) ;

my ($dbhq,$dbhu)=$util->get_dbh();

my $hrCookie = retrieve_cookie();
my $user_id;

#$hrCookie->{'clientInfo'} =~ s/[^0-9a-z\=]//gi;

if (!$hrCookie->{'clientInfo'}) {
   print "Location: http://www.zetainteractive.com/login.html\n\n";
}

else {

    my $username = decode_base64($hrCookie->{'clientInfo'});
    $username =~ s/[^0-9a-z\-\_]//gi;

    my $qSel = qq|
    SELECT  
            user_id,
            username
    FROM
        user u
    WHERE
        username = "$username"
    |;

    my $sth = $dbhq->prepare($qSel);
    $sth->execute();

    my $hrInfo = $sth->fetchrow_hashref;

    $user_id = $hrInfo->{'user_id'};

}

sub retrieve_cookie {
use CGI::Cookie;
     my %hCookies=fetch CGI::Cookie;

     if ($hCookies{sv}) {
       my %hCookie=$hCookies{sv}->value;
       if ($hCookie{'clientInfo'}) {
           return wantarray() ? (\%hCookie, "COOKIE") : \%hCookie;
       }         else {
           return wantarray() ? ({}, "NO ID") : {};
       }     }     else {
       return wantarray() ? ({}, "NO COOKIE") : {};
   }  }





if($query->param('validate'))
{
  print "Content-type: text/html\n\n";
  use App::RecordProcessing::RegularRecordProcessing;
  my $recordProcessingObject = App::RecordProcessing::RegularRecordProcessing->new();
  my $validate_status = 'Validation Failed - Please return to the previous page and enter a valid record.';

  $recordProcessingObject->databaseWriteObject();

  $recordProcessingObject->ClientID($user_id);
  $recordProcessingObject->getFileLayout();
print "<table bgcolor='#D1E4F0' width='600' align='center' border=0><tr><td align='center'><font color='#ff0000'>";
  my ($record) = $recordProcessingObject->formatRecord($query->param('data_sample'));
  if(!$recordProcessingObject->recordInvalid())
  {
    if($recordProcessingObject->setFileLayoutAsValidated())
    {
      $validate_status = 'Validation Success -  You may now close this window.';
    }
    else
    {
      $validate_status = 'ERROR: An Error occurred.  Please contact the system administrator.';
    }
  }

my $mesg = qq|</font><td></tr><tr><td align='center'><font color="black"><br><br><b>$validate_status</b><br /><a href='javascript:history.go(-1);'>Back</a> <br /> &nbsp;</td></tr></table>|;

print $mesg;

}
else
{


my (@ary_chkbox_fields);
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
###$util->db_connect();

#my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

#----- check for login --------

#my $user_id = util::check_security();
#if ($user_id == 0)
#{
#    print "Location: notloggedin.cgi\n\n";
#    $util->clean_up();
#    exit(0);
#}

#pass in user id
#my $user_id = $query->param('user_id');

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
#	if ( ($str_fldpos_field ne "")  && ($str_fldpos_field =~ /^\d+$/))
        if ($str_fldpos_field !~ /^\d+$/ )
        {
 		$str_fldpos_field = 'NULL';
	}
	
	push @ary_fldpos_nonblank_fields, $str_fldpos_field;
}

$ary_len = (@ary_chkbox_fields) - 1 ;
#$ary_len = $ary_len - 1;  

for($i = 0; $i <= $ary_len; $i++)
{
	if ($ary_chkbox_fields[$i] eq "email_addr_pos")     { $email_addr_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "email_type_pos")     { $email_type_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "gender_pos")         { $gender_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "first_name_pos")     { $first_name_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "middle_name_pos")    { $middle_name_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "last_name_pos")      { $last_name_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "birth_date_pos")     { $birth_date_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "address_pos")        { $address_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "address2_pos")       { $address2_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "city_pos")           { $city_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "state_pos")          { $state_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "zip_pos")            { $zip_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "country_pos")        { $country_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "marital_status_pos") { $marital_status_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "occupation_pos")     { $occupation_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "job_status_pos")     { $job_status_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "income_pos")         { $income_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "education_pos")      { $education_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "date_capture_pos")     { $date_capture_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "member_source_pos")     { $member_source_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "phone_pos")     { $phone_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
	if ($ary_chkbox_fields[$i] eq "source_url_pos")     { $source_url_pos = $ary_fldpos_nonblank_fields[$i] || 'NULL'; }
}

$rows = 0 ;
$sql = qq{delete from user_file_layout where user_id = $user_id};
$rows = $dbhu->do($sql);

$rows = 0 ;
$sql = qq{insert into user_file_layout ( user_id, create_datetime,
	email_addr_pos,     email_type_pos,      gender_pos,
	first_name_pos,     middle_name_pos,     last_name_pos,
	birth_date_pos,     address_pos,         address2_pos,
	city_pos,           state_pos,           zip_pos,
	country_pos,        marital_status_pos,  occupation_pos,
	job_status_pos,     income_pos,          education_pos, 
	date_capture_pos, member_source_pos, phone_pos,source_url_pos,validated)
	values ( $user_id, curdate(),
	$email_addr_pos,     $email_type_pos,      $gender_pos,
	$first_name_pos,     $middle_name_pos,     $last_name_pos,
	$birth_date_pos,     $address_pos,         $address2_pos,
	$city_pos,           $state_pos,           $zip_pos,
	$country_pos,        $marital_status_pos,  $occupation_pos,
	$job_status_pos,     $income_pos,          $education_pos,
	$date_capture_pos,   $member_source_pos, $phone_pos, $source_url_pos,0) };
$rows = $dbhu->do($sql);

#print "Content-type: text/html\n\nsql = $sql <br> \n" ;   # comment out

$go_back = qq{&nbsp;&nbsp;<a href="javascript:history.go(-1)">Back</a>\n };
$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
$go_url = qq{&nbsp;&nbsp;<a href="sub_disp_add.cgi">Add Subscribers</a>\n };
$mesg = qq|
<font color="black"><br><br><b>Successful</b> Add/Update of File Layout. <br/><p><strong>NOTE: Your layout MUST be validated!</strong></p></font><br />
<form name='formValidate' action='file_layout_update.cgi' method='post'>
Input sample line of data:<br/><textarea name='data_sample' rows=5 cols=80></textarea><br />
<input type='hidden' name='validate' value='1'>
<input type='submit' value='Validate' />
</form>

| ;
# $mesg = $mesg . $go_back . $go_home ;
$mesg = $mesg . $go_back;
# util::logerror($mesg) ;
util::record_processing_confirmation_page('Validate File Layout',$mesg);
}

exit(0) ;


# print $query->redirect("file_layout_disp.cgi?mesg=$mesg") ;
# print $query->redirect("file_layout_disp.cgi") ;



