#!/usr/bin/perl
#===============================================================================
# File   : default_uniqueprofile.cgi 
#
#--Change Control---------------------------------------------------------------
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
my $cnt;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;

# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $profile_id = $query->param('profile_id');
my ($dbhq,$dbhu)=$util->get_dbh();

$sql="update sysparm set parmval='$profile_id' where parmkey='DEFAULT_MAILING_PROFILE'";
$rows=$dbhu->do($sql);
print "Location: uniqueprofile_list.cgi\n\n";
exit(0) ;


