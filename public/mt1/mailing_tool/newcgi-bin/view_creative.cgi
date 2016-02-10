#!/usr/bin/perl
#===============================================================================
#
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_type;
my $images = $util->get_images_url;
my $unsub_option;
my $name;
my $sid;
my $fid;
my $aflag;
my $temp_str;
my $content_id;
my $header_id;
my $body_id;
my $style_id;
my $oflag;
my $cdate;
my $cat_id;
my $puserid; 
my $cid;
my $pmode;
my $backto;
my $pmesg;
my $replace_flag;
my $original_html;
my $create_name;

my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

#--------------------------------
# get CGI Form fields
#--------------------------------
$cid = $query->param('cid');

#------  Get the information about the user for display  --------
$sql = "select creative_name,original_html from creative where creative_id=$cid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($create_name,$original_html) = $sth->fetchrow_array();
$sth->finish();
print "Content-type: text/html\n\n";
print "$original_html";
