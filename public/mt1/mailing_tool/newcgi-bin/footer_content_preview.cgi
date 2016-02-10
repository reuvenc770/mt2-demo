#!/usr/bin/perl

# *****************************************************************************************
# footer_content_preview.cgi
#
# this page presents the user with a preview of the footer content 
#
# History
# *****************************************************************************************

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
my $cid = $query->param('cid');
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
my $html_code;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# read the email address for this user
$sql = "select content_html from footer_content where content_id=$cid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($html_code) = $sth->fetchrow_array();
$sth->finish();
	$first_part = "<html><head><title>Footer Content Preview</title></head><body>";
	print "Content-type: text/html\n\n";
	print "$first_part\n";	
	print "$html_code\n";
print<<"end_of_html";
</body>
</html> 
end_of_html

exit(0);

