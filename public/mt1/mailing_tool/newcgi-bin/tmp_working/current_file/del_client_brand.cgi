#!/usr/bin/perl
#===============================================================================
# Purpose: Allows editing of client brand 
# File   : edit_client_brand.cgi
#
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $bid = $query->param('bid');
my $bname;
my ($sth, $reccnt, $sql, $dbh ) ;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $sth1;
my $uid;
my $url;
my $vid;
my $vname;
my $old_vid;
my $font_id;
my $color_id;
my $bg_color_id;
my $tid;
my $fid;
my $fname;
my $color_name;
my ($bname,$ourl,$yurl,$o_imageurl,$y_imageurl,$ons1,$ons2,$yns1,$yns2,$oip,$yip,$addr1,$addr2,$whois_email,$abuse_email,$personal_email,$others_host,$yahoo_host,$cns1,$cns2);
my $header_text;
my $footer_text;
my $notes;

# ------- connect to the util database ---------
$util->db_connect();
$dbh = $util->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql = "select brand_name from client_brand_info where brand_id=$bid";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($bname) = $sth->fetchrow_array();
$sth->finish();
#
$sql="update client_brand_info set status='D' where brand_id=$bid";
my $rows=$dbh->do($sql);
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Delete Brand </title>
</head>

<body>
<center>
<h3>Brand <b>$bname</b> has been deleted.</h3>
<p>
<a href="/cgi-bin/mainmenu.cgi"><img src="/mail-images/home_blkline.gif" border="0"></a>
</center>
</body>
</html>
end_of_html
