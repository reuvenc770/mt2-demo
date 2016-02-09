#!/usr/bin/perl

# *****************************************************************************************
# sav_client_brand_info.cgi
#
# this page saves the client brand category selection
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use DBI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $sid;
my $list_id;
my $iopt;
my $rows;
my $errmsg;
my $campaign_id;
my $id;
my $campaign_name;
my $k;
my $cname;
my $status;
my $aid;
my $list_cnt;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $dbh=DBI->connect('DBI:mysql:new_mail:recordprocessing01.i.routename.com','db_user','sp1r3V') or die "Can't connect to DB: $!\n";
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $client_id = $query->param('clientid');
my $pid= $query->param('pid');
my $email_addr= $query->param('email_addr');
my $url = $query->param('url');
my $ip= $query->param('ip');
my $fname= $query->param('fname');
$fname=~s/'/''/g;
my $lname= $query->param('lname');
$lname=~s/'/''/g;
my $addr= $query->param('addr');
$addr=~s/'/''/g;
my $addr2= $query->param('addr2');
my $city= $query->param('city');
my $state= $query->param('state');
my $zip= $query->param('zip');
my $gender = $query->param('gender');
my $phone= $query->param('phone');

my $domain_id;
my ($rest,$domain)=split('@',$email_addr);
$sql="select domain_id from email_domains where domain_name=?";
my $sth=$dbhu->prepare($sql);
$sth->execute($domain);
if (($domain_id)=$sth->fetchrow_array())
{
}
else
{
	$domain_id=0;
}
$sth->finish();

$sql = "insert into PartnerData(partner_id,email_addr,client_id,send_date,send_time,first_name,last_name,address,address2,city,state,zip,gender,phone,member_source,source_url,capture_date,domain_id) values($pid,'$email_addr',$client_id,curdate(),curtime(),'$fname','$lname','$addr','$addr2','$city','$state','$zip','$gender','$phone','$ip','$url',now(),$domain_id)";
$rows = $dbh->do($sql);

print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head><title>Added Partner Record</title></head>
<body>
<center>
<h3>Record has been added to Partner Data Table</h3>
<br>
<a href="/newcgi-bin/mainmenu.cgi">Home</a>
</body>
</html>
end_of_html
