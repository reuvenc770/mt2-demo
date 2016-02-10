#!/usr/bin/perl

# *****************************************************************************************
# add_article_save.cgi
#
# this page updates information in the nl_article table
#
# History
# Jim Sobeck, 11/01/06, Creation
# Jim Sobeck, 12/20/06,	Changed to newsletter from advertiser
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
my $cfrom;
my @from_array;

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
#
my $nl_id = $query->param('nl_id');
my @article_id = $query->param('articles');
foreach my $c1 (@article_id)
{
	if ($c1 > 0)
	{
		$sql = "insert into nl_article(nl_id,article_id) values($nl_id,$c1)";
		$sth = $dbhu->do($sql);
	}
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/newsletter_disp.cgi?pmode=U&nl_id=$nl_id\n\n";
$util->clean_up();
exit(0);
