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
use Data::Dumper;
use Lib::Database::Perl::Interface::Unsubscribe;

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
$em=~tr/A-Z/a-z/;

# ----- connect to the util database -------
my ($dbhq,$dbhu)=$util->get_dbh();

# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$em=~s/ //g;
if ($em =~ /[^a-z0-9\@\_\.\-]/)
{
print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head><title>Confirmation</title></head>
<body>
<h3><$em> - Record has non A-Z, 0-9, @, _, . , or - - not added to global suppression
<br>
<center>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home.gif" border=0></a>
</body>
</html>
end_of_html
exit;
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
	my $eid;
	my $user_id;
	my $company;
	my $list_id;
	my $params;

	my $unsubscribeInterface=Lib::Database::Perl::Interface::Unsubscribe->new();
	my $errors=$unsubscribeInterface->unsubscribeAll( { 'emailAddress' => $em} );
	$errors=$unsubscribeInterface->unsubscribeUniques( { 'emailAddress' => $em} );
	util::addProadvertisers($em);
	util::addGlobal({ 'emailAddress' => $em});
} # end of sub

sub display
{
    my ($message, $displayValue)    = @_;

    print "\n" . '*' x 30 ."\n\n";
    print "$message: " . Dumper($displayValue) . "\n";
}
