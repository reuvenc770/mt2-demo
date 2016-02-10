#!/usr/bin/perl
#===============================================================================
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

# declare variables

my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my ($go_back, $go_home, $mesg, $list_id, $list_name, $chkbox_name);
my $images = $util->get_images_url;
my ($cat_id, $category, $db_field, $html_disp_order) ;
my (%file_fields, $field_position, $key, $value, $checked, $nbr_cols) ;
my ($file_layout_reccnt, $file_layout_str, $category_name);
my ($first_name, $last_name, $company);
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $rows;
my $errmsg;
my $name;
my $tid;
my $unique_id;
my $file;

#----- connect to the util database -----
my ($dbhq,$dbhu)=$util->get_dbh();

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $aid = $query->param('aid');
my @chkbox=$query->param('chkbox');
my $i=0;
while ($i <= $#chkbox)
{
	$file="/home/adv/$aid/".$chkbox[$i];
	unlink($file);
	$i++
}
print "Location: advertiser_upload.cgi?aid=$aid\n\n";
