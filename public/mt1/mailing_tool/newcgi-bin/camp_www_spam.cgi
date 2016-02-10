#!/usr/bin/perl

# *****************************************************************************************
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $html_template;
my $sth1;
my $BASE_DIR;

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

# get the fields from the form 

my $campaign_id = $query->param('cid');
my $format = $query->param('format');
my $cdraft = $query->param('cdraft');
my $aid= $query->param('aid');
my $subject=$query->param('subject');
my $from=$query->param('from');

if ($cdraft eq "")
{
	$cdraft="N";
}
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
my @args = ("${BASE_DIR}newcgi-bin/test_www_spam.sh","$campaign_id","$aid","$subject","$from","$cdraft");
system(@args) == 0 or die "system @args failed: $?";
util::header("Spam Report");
open(TEMPLATE,"</var/www/util/logs/spam_www_results.txt");
print <<end_of_html;
<p>
<p>
<table>
end_of_html
while (<TEMPLATE>)
{
	print "<tr><td>";
	print $_;
	print "</td></tr>";
}
close(TEMPLATE);
print <<end_of_html;
</table>
</body>
</html>
end_of_html
exit(0);
