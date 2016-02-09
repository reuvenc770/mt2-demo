#!/usr/bin/perl

# *****************************************************************************************
# camp_test_gen.cgi
#
# this page sends one test email
#
# History
# Grady Nash, 8/15/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sql;
my $campaign_id = $query->param('campaign_id');
my $cemail = $query->param('cemail');
my $format = $query->param('format');
my $other_send = $query->param('other_send');

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id != 0)
{
#	if ($other_send eq "Y")
#	{
#	    $sql = "update campaign set other_addr='$cemail' where campaign_id=$campaign_id";
#	    my $rows = $dbh->do($sql);
#	}
	&util_mail::mail_sendtest($dbh,$campaign_id,$cemail,$format,0,$user_id);
}

print "Location:camp_preview.cgi?campaign_id=$campaign_id&format=$format&userid=$user_id\n\n";

$util->clean_up();
exit(0);
