#!/usr/bin/perl

# *****************************************************************************************
# 3rdparty_add.cgi
#
# this page adds records to third_party_defaults 
#
# History
# Jim Sobeck, 12/20/05, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $curl;
my @url_array;

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
my $id = $query->param('id');
my $mailer_name = $query->param('mailer_name');
my $num_subject = $query->param('num_subject');
my $num_from = $query->param('num_from');
my $num_creative = $query->param('num_creative');
my $rname=$query->param('rname');
my $rloc=$query->param('rloc');
my $rdate=$query->param('rdate');
my $remail=$query->param('remail');
my $rcdate=$query->param('rcdate');
my $rip=$query->param('rip');
my $rcid=$query->param('rcid');
my $remailid=$query->param('remailid');
my $unsub_flag=$query->param('unsub_flag');
if ($unsub_flag eq "")
{
	$unsub_flag="N";
}
my $include_images=$query->param('include_images');
if ($include_images eq "")
{
	$include_images="N";
}
my $ip_addr=$query->param('ip_addr');
my $ftp_username=$query->param('ftp_username');
my $ftp_password=$query->param('ftp_password');
my $record_path=$query->param('record_path');
my $suppression_path=$query->param('suppression_path');
my $creative_path=$query->param('creative_path');
my $list_suppression_path=$query->param('list_suppression_path');
my $aol_seed=$query->param('aol_seed');
my $yahoo_seed=$query->param('yahoo_seed');
my $hotmail_seed=$query->param('hotmail_seed');
my $other_seed=$query->param('other_seed');
my $seed_fname=$query->param('seed_fname');
my $seed_city=$query->param('seed_city');
my $seed_state=$query->param('seed_state');
my $seed_gender=$query->param('seed_gender');
my $seed_dob=$query->param('seed_dob');
my $seed_ip=$query->param('seed_ip');
my $seed_date=$query->param('seed_date');
my $email_seq=$query->param('email_seq');
my $fname_seq=$query->param('fname_seq');
my $city_seq=$query->param('city_seq');
my $state_seq=$query->param('state_seq');
my $gender_seq=$query->param('gender_seq');
my $dob_seq=$query->param('dob_seq');
my $eid_seq=$query->param('eid_seq');
my $network_seq=$query->param('network_seq');
my $ip_seq=$query->param('ip_seq');
my $date_seq=$query->param('date_seq');
my $source_seq=$query->param('source_seq');
my $source_val_seq=$query->param('source_val_seq');
my $export_format=$query->param('export_format');
my $filenaming_convention=$query->param('filenaming_convention');
my $export_freq=$query->param('export_freq');
my $build_zip=$query->param('build_zip');
my $send_data=$query->param('send_data');
my $contact=$query->param('contact');
$contact=$dbhq->quote($contact);
my $phone=$query->param('phone');
$phone=$dbhq->quote($phone);
my $email=$query->param('email');
$email=$dbhq->quote($email);
my $website=$query->param('website');
$website=$dbhq->quote($website);
my $username=$query->param('username');
$username=$dbhq->quote($username);
my $password=$query->param('password');
$password=$dbhq->quote($password);
my $notes=$query->param('notes');
$notes=$dbhq->quote($notes);
my $hitpath_id=$query->param('hitpath_id');
my $default_client=$query->param('default_client');
#
$sql = "update third_party_defaults set mailer_name='$mailer_name',num_subject='$num_subject',num_from='$num_from',num_creative='$num_creative',name_replace='$rname',loc_replace='$rloc',date_replace='$rdate',email_replace='$remail',cid_replace='$rcid',include_unsubscribe='$unsub_flag',emailid_replace='$remailid',mailer_ftp='$ip_addr',ftp_username='$ftp_username',ftp_password='$ftp_password',record_path='$record_path',suppression_path='$suppression_path',aol_seed='$aol_seed',yahoo_seed='$yahoo_seed',hotmail_seed='$hotmail_seed',other_seed='$other_seed',seed_first_name='$seed_fname',seed_city='$seed_city',seed_state='$seed_state',seed_gender='$seed_gender',seed_dob='$seed_dob',fname_seq=$fname_seq,city_seq=$city_seq,state_seq=$state_seq,dbo_seq=$dob_seq,eid_seq=$eid_seq,export_format='$export_format',filenaming_convention='$filenaming_convention',export_freq='$export_freq',gender_seq=$gender_seq,email_seq=$email_seq,contact=$contact,phone=$phone,email=$email,website=$website,username=$username,password=$password,notes=$notes,seed_ip='$seed_ip',seed_date='$seed_date',ip_seq=$ip_seq,date_seq=$date_seq,creative_path='$creative_path',list_suppression_path='$list_suppression_path',capture_replace='$rcdate',ip_replace='$rip',include_images='$include_images',build_zip='$build_zip',send_data='$send_data',source_seq='$source_seq',network_seq='$network_seq',source_val_seq='$source_val_seq',hitpath_id='$hitpath_id',default_client=$default_client where third_party_id=$id";
$sth = $dbhu->do($sql);
#
# Display the confirmation page
#
print "Location: /cgi-bin/3rdparty_list.cgi\n\n";
