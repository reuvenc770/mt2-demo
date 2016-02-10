#!/usr/bin/perl

# *****************************************************************************************
# footer_content_sav.cgi
#
# this page inserts/updates information in the footer_content and content_category tables
#
# History
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
my $year;
my $mon;
my $mday;

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
my $content_id = $query->param('content_id');
my $content_name = $query->param('content_name');
my $content_date= $query->param('content_date');
my $inactive_date = $query->param('inactive_date');
my $html_code = $query->param('html_code');
$html_code=~ s/'/''/g;
my @catid = $query->param('catid');
#
# Insert record into footer_content 
#
my $tdate;
($mon,$mday,$year) = split('\/',$content_date);
if (length($year) == 2)
{
	$year = "20" . $year;
}
$tdate = $year . "-" . $mon . "-" . $mday;
my $idate;
if ($idate eq "")
{
	$idate="0000-00-00";
}
else
{
	($mon,$mday,$year) = split('\/',$inactive_date);
	if (length($year) == 2)
	{
		$year = "20" . $year;
	}
	$idate = $year . "-" . $mon . "-" . $mday;
}
if ($content_id == 0)
{
	$sql = "insert into footer_content(content_name,content_date,inactive_date,modified_date,content_html) values('$content_name','$tdate','$idate',NOW(),'$html_code')";
	$sth = $dbhu->do($sql);
	$sql="select max(content_id) from footer_content where content_name='$content_name'";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($content_id) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	$sql = "update footer_content set content_name='$content_name',content_date='$tdate',inactive_date='$idate',content_html='$html_code', modified_date=NOW() where content_id=$content_id";
	$sth = $dbhu->do($sql);
}
$sql="delete from content_category where content_id=$content_id";
$sth = $dbhu->do($sql);
foreach my $cat_id (@catid)
{
	$sql="insert into content_category(content_id,category_id) values($content_id,$cat_id)";
	$sth = $dbhu->do($sql);
}
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/footer_content_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
