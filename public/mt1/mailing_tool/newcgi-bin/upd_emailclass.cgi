#!/usr/bin/perl

# *****************************************************************************************
# upd_emailclass.cgi
#
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $rows;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $class_id;

my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
#
my $emailclass = $query->param('emailclass');
#
$sql="insert into email_class(class_name,status) values('$emailclass','Active')";
my $rows=$dbhu->do($sql);
$sql="select LAST_INSERT_ID()";
$sth=$dbhu->prepare($sql);
$sth->execute();
($class_id)=$sth->fetchrow_array();
$sth->finish();

$sql="insert ignore itno advertiser_setup select advertiser_id,id, creative1_id, creative2_id, creative3_id, creative4_id, creative5_id, creative6_id, creative7_id, creative8_id, creative9_id, creative10_id, creative11_id, creative12_id, creative13_id, creative14_id, creative15_id, subject1, subject2, subject3, subject4, subject5, subject6, subject7, subject8, subject9, subject10, subject11, subject12, subject13, subject14, subject15, from1, from2, from3, from4, from5, from6, from7, from8, from9, from10, trigger_creative, trigger_creative2, date_modified, ec.class_id, last_check,subject16, subject17,subject18,subject19,subject20,subject21,subject22,subject23,subject24,subject25, subject26,subject27,subject28,subject29,subject30,from11,from12,from13,from14,from15,from16, from17,from18,from19,from20 from advertiser_setup ads, email_class ec where ads.class_id=3 and ec.class_id = $class_id";
$rows=$dbhu->do($sql);
#
$sql="insert into mta_detail select mta_id, ec.class_id, inj_qty, pause_time, max_records_per_ip, ip_rotation_type, ip_rotation_times, domain_type, domain_times, fromline_type, fromline_times, subjectline_type, subjectline_times, creative_type, creative_times, seed_times, seedlist, seed_type, wikiID, ramp_up, encrypt_link, use_random_batch, variance, oneliner_type, oneliner_times, action_type, action_times, mailingHeaderID from mta_detail m, email_class ec where m.class_id=4 and ec.class_id = $class_id"; 
$rows=$dbhu->do($sql);
#
$sql="insert into DailyDealSettingDetail select dd_id, ec.class_id, group_id, domain, template_id, header_id, footer_id, seedlist, wikiID, mailingHeaderID, article_id, mail_from, hotmailDomain, ramp_up_freq, ramp_up_email_cnt, curdate(), send_cnt, cap_volume, curdate(), return_path from DailyDealSettingDetail dd, email_class ec where dd.class_id=4 and ec.class_id = $class_id";
$rows=$dbhu->do($sql);
#
$sql="insert into UniqueProfileIsp select profile_id, ec.class_id from UniqueProfileIsp u, email_class ec where u.class_id=4 and ec.class_id = $class_id";
$rows=$dbhu->do($sql);
#
$sql="insert into list_profile_domain select distinct profile_id, ec.class_id from list_profile_domain lpd, email_class ec where ec.class_id = $class_id";
$rows=$dbhu->do($sql);

print "Location: /cgi-bin/emailclasses.cgi\n\n";
