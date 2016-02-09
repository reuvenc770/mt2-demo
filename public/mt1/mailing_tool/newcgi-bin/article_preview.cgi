#!/usr/bin/perl
#===============================================================================
# Name   : article_preview.cgi 
#
#--Change Control---------------------------------------------------------------
# 10/31/06  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $sth1;
my $dbh;
my $cid;
my $cname;
my $content_name;
my $content_html;
my $inactive_date;
my $content_date;
my $author;
my $headline;
my $article_font;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $content_id=$query->param('cid');
$sql="select article_name,date_format(date_of_content,'%m/%d/%y'),date_format(inactive_date,'%m/%d/%y'),html_code,author,headline,article_font from article where article_id=$content_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($content_name,$content_date,$inactive_date,$content_html,$author,$headline,$article_font) = $sth->fetchrow_array();
$sth->finish();
if ($author ne "")
{
	$author="by " . $author;
}	
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$content_name</title>
</head>

<body>
end_of_html
print "<font face=\"$article_font\" size=4 color=\"#444444\">$headline<br>\n";
print "<font face=\"$article_font\" size=2 color=\"#444444\">
		$author \n";
print "$content_html\n";
print<<"end_of_html";
</body>
</html>
end_of_html
