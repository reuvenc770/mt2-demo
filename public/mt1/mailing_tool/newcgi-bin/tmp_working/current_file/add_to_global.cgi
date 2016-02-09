#!/usr/bin/perl
#===============================================================================
# File   : add_to_global.cgi
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
my $tot_good;
my $tot_bad;
my $tot_dup;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $email_user_id;
$tot_good = 0;
$tot_bad = 0;
$tot_dup = 0;
my ($BytesRead, $Buffer, $Bytes ) ;
my (@temp_file, %confirm);
my $tmp_file;


# ------- Get fields from html Form post -----------------

my $em= $query->param('em');

# ----- connect to the util database -------
$util->db_connect();
$dbh = $util->get_dbh;

# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&uns_upd_list_member($em);

#---- Print Delete Statistics -----------------
print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head><title>Confirmation</title></head>
<body>
<h3>Email $em added to global suppression list</h3>
<br>
<center>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home.gif" border=0></a>
</body>
</html>
end_of_html
$util->clean_up();
exit(0) ;

#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($em) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i) ; 
	my $email_addr;
	my $tid;
	
	$sql = "insert into suppress_list values('$em')";
	$rows = $dbh->do($sql) ;

} # end of sub

