#!/usr/bin/perl
#===============================================================================
# Name   : delete_c3.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($pmesg, $old_email_addr) ;
my $images = $util->get_images_url;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

#---------------------------------------------------
# Get the information about the user from the form
#---------------------------------------------------
my $puserid = $query->param('aid');

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
&delete_c3();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/gen_tracking.cgi?aid=$puserid&ctype=N&rid=$rid\n\n";
exit(0);

sub delete_c3
{
	my $rows;
	my $i;

	$sql="update advertiser_tracking set url=replace(url,'&c3={{EMAIL_ADDR}}','') where advertiser_id=$puserid and daily_deal='N'";
	$rows= $dbhu->do($sql);
}
# end sub - delete_creative
