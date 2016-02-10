#!/usr/bin/perl

# *****************************************************************************************
# partner_status.cgi
#
# this page changes a status of PartnerInfo table 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $dbh;
my $partner_id = $query->param('partner_id');
my $tflag= $query->param('tflag');
my $new_id;
my $sql;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
if (($tflag eq "N") or ($tflag eq "Y"))
{
	$sql="update PartnerInfo set enable_flag='$tflag' where partner_id=$partner_id";
}
elsif ($tflag eq "U")
{
	$sql="update PartnerInfo set pause_flag='N' where partner_id=$partner_id";
}
elsif ($tflag eq "P")
{
	$sql="update PartnerInfo set pause_flag='Y' where partner_id=$partner_id";
}
my $rows=$dbhu->do($sql);

print "Location: partner_list.cgi\n\n";
