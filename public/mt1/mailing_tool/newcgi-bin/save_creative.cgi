#!/usr/bin/perl

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my $pmesg="";
my @subject_array;

# connect to the util database
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
# Remove old mediactive information
#
my $aid = $query->param('aid');
$sql = "update creative set mediactivate_flag='N' where advertiser_id=$aid and mediactivate_flag='Y'";
my $rows=$dbhu->do($sql);

my @mflag = $query->param('mflag');
foreach my $cid (@mflag)
{
	$sql="update creative set mediactivate_flag='Y' where creative_id=$cid and advertiser_id=$aid";
	my $rows=$dbhu->do($sql);
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
