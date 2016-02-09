#!/usr/bin/perl
#===============================================================================
# Purpose: Insert record into draft_creative table
# Name   : insert_draft.cgi 
#
#--Change Control---------------------------------------------------------------
# 04/24/06  Jim Sobeck  Creation
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($pmesg, $old_email_addr) ;
my $images = $util->get_images_url;
my $creative_name ;
my $original_flag ;
my $img_cnt;
my $upload_dir;
my $cid;
my ($scheme, $auth, $path, $frag);
my $name;
my $new_name;
my $suffix;
my $img_added;
my $trigger_flag ;
my $approved_flag ;
my $creative_date;
my $inactive_date ;
my $unsub_image ;
my $default_subject ;
my $default_from ;
my $image_directory ;
my $thumbnail ;
my $html_code ;
my $puserid;
my $pmode;
my $BASE_DIR;
my $sth1;
my $global_text;
my $img_dir;
my $replace_flag;
$img_cnt = 0;
my @var=("{{NAME}}","{{LOC}}","{{EMAIL_USER_ID}}","{{EMAIL_ADDR}}","{{URL}}","{{IMG_DOMAIN}}","{{DOMAIN}}","{{CID}}","{{FID}}","{{CRID}}","{{FOOTER_SUBDOMAIN}}","{{FOOTER_DOMAIN}}","{{FROMADDR}}","{{MAILDATE}}","{{FOOTER_TEXT}}","{{DATE}}");

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
$creative_name = $query->param('creative_name');
my $due_date = $query->param('due_date');
my $aid = $query->param('aid');
my $designer = $query->param('designer');
my ($dbhq,$dbhu)=$util->get_dbh();
&insert_creative();
$util->clean_up();
print "Content-Type: text/plain\n\n";
print<<"end_of_html";
<html>
<head>
<body>
<script language="JavaScript">
document.location="/draft.html";
</script>
</body>
</html>
end_of_html
exit(0);

sub insert_creative
{
	my $rows;
	# add user to database

	$creative_name =~ s/'/''/g;
	$sql = "insert into draft_creative(advertiser_id,status,creative_name,due_date,designer_id,assigned_date,updated_date) values($aid,'A','$creative_name','$due_date',$designer,curdate(),curdate())";
	$sth = $dbhu->do($sql);
}  # end sub - insert_creative

