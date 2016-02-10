#!/usr/bin/perl

# *****************************************************************************************
# add_brand_article_save.cgi
#
# this page updates information in the brand_article table
#
# History
# Jim Sobeck, 08/10/07, Creation
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
my $bid= $query->param('bid');
my @article_id = $query->param('articles');
foreach my $c1 (@article_id)
{
	if ($c1 > 0)
	{
		$sql = "insert into brand_article(brand_id,article_id) values($bid,$c1)";
		$sth = $dbhu->do($sql);
	}
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/add_brand_article.cgi?bid=$bid\n\n";
$util->clean_up();
exit(0);
