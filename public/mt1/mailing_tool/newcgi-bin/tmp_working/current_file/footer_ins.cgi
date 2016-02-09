#!/usr/bin/perl

# *****************************************************************************************
# footer_ins.cgi
#
# this page updates information in the footer_variation table
#
# History
# Jim Sobeck, 06/14/05, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my @subject_array;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $vname= $query->param('vname');
my $privacy_text= $query->param('privacy_text');
my $unsub_text= $query->param('unsub_text');
#
# Insert record into footer_variation 
#
$sql = "insert into footer_variation(name,privacy_text,unsub_text,status) values('$vname','$privacy_text','$unsub_text','A')"; 
$sth = $dbh->do($sql);
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/footer_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
