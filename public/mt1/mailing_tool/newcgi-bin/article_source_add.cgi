#!/usr/bin/perl
#===============================================================================
# Purpose: Add an article source 
# File   : article_source_add.cgi
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
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sname;
my $surl;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Article Source</title>
</head>
<body>
<p><b>Current Sources: </b></br>
end_of_html
$sql="select source_name,image_url from article_source order by source_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($sname,$surl)=$sth->fetchrow_array())
{
	print "<a href=\"http://www.affiliateimages.com/$surl\">$sname</a><br />\n";
}
$sth->finish();
my $r1=util::get_name();
my $r2=util::get_name();
my $a=substr($r1,0,1);
my $b=substr($r1,1,1);
my $c=substr($r1,2,1);
my $upload_dir="/var/www/util/creative";
my $t4;
$t4=$upload_dir."/".$a;
mkdir $t4;
$t4=$t4."/".$b;
mkdir $t4;
$t4=$t4."/".$c;
mkdir $t4;
print<<"end_of_html";
<form action="article_source_save.php" method="post">
<input type=hidden name=sid value=0>
<input type=hidden name=r1 value="$r1">
<input type=hidden name=r2 value="$r2">
<input type=hidden name=a value="$a">
<input type=hidden name=b value="$b">
<input type=hidden name=c value="$c">
<p><b>Article Source Name: </b><input type=text value="" name="source_name" maxlength="80" size="50"><br />
<p>
<p>


	<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
