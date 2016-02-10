#!/usr/bin/perl

# ******************************************************************************
# validate.cgi
#
# this page presents the user with a preview of the campaign email that they canvalidate
#
# History
# Jim Sobeck, 3/13/08, Creation
# ******************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $rows;
my $errmsg;
my $email_addr;
my $email_user_id;
my $first_name;
my $last_name;
my $campaign_id = $query->param('campaign_id');
my $cdeploy = "V";
my $format= "H";
my $images = $util->get_images_url;
my $subject;
my $body_text;
my $first_part;
my $testvar;
my $pos;
my $pos2;
my $the_rest;
my $end_pos;
my $selected_bg_color = "#509C10";
my $not_selected_bg_color = "#E3FAD1";
my $selected_tl_gif = "$images/blue_tl.gif";
my $selected_tr_gif = "$images/blue_tr.gif";
my $not_selected_tl_gif = "$images/lt_purp_tl.gif";
my $not_selected_tr_gif = "$images/lt_purp_tr.gif";
my $selected_text_color = "#FFFFFF";
my $not_selected_text_color = "#509C10";
my @bg_color;
my @tl_gif;
my @tr_gif;
my @text_color;
my $k;
my $from_addr;
my $other_addr;
my $aid;
my $footer_color;
my $internal_flag;
my $unsub_url;
my $unsub_image;
my $cunsub_image;
my $csite;
my $content_id;
my $footer_content_id;

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

# read the email address for this user

$sql = "select email_addr,first_name,last_name,website_url from user where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($email_addr,$first_name,$last_name,$csite) = $sth->fetchrow_array();
$sth->finish();
$email_addr="email\@domain.com";

# read the subject for this newsletter

$sql = "select default_subject,default_from,advertiser_id,unsub_image,content_id from creative where creative_id = $campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject,$from_addr,$aid,$cunsub_image,$footer_content_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "select advertiser_subject from advertiser_subject where subject_id = $subject and status='A'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject) = $sth->fetchrow_array();
$sth->finish();
if ($subject eq "")
{
	$subject = "No subject selected";
}
$sql = "select advertiser_from from advertiser_from where from_id = $from_addr and status='A'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($from_addr) = $sth->fetchrow_array();
$sth->finish();
if ($from_addr eq "")
{
	$from_addr = "{{{{FOOTER_SUBDOMAIN}}";
}
#
# Get unsub information and internal_flag
#
my $unsub_use;
my $unsub_text;
$sql = "select track_internally,unsub_link,unsub_image,unsub_use,unsub_text from advertiser_info where advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($internal_flag,$unsub_url,$unsub_image,$unsub_use,$unsub_text) = $sth->fetchrow_array();
$sth->finish();

if ($cunsub_image eq "NONE")
{
	$unsub_image="";
}
$email_user_id = 0; 

# Get the email template and substitute all the field data

my $the_email = &util_mail::mail_preview($dbhq,$campaign_id,$format,$email_addr,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_image,$content_id,"",$cdeploy,$unsub_use,$unsub_text);
$the_email=~s/{{HEAD}}//g;

# this screen presents the email template inside of a table with some other html
# "stuff" on top.  So I must first break up the html template to remove the body
# and /body tags, and put everything else in the right place in the table tag

print "Content-type: text/html\n\n";
print "$the_email\n";

$util->clean_up();
exit(0);

