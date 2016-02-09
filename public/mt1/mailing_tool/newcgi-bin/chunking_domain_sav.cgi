#!/usr/bin/perl

# ******************************************************************************
# chunking_domain_sav.cgi
# ******************************************************************************
#
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
my $errmsg;
my $user_id;
my $list_id;
my $rows;
my $did;
my $cserver;
my $list_name;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;
my $pid=$query->param('pid');

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql="delete from profile_chunk_add where profile_id=$pid";
$rows=$dbhu->do($sql);
#
$sql="select domain_id from email_domains where chunked=1";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($did)=$sth->fetchrow_array())
{
	my $temp_str="add_".$did;
	my $add_amount=$query->param($temp_str);
	if ($add_amount eq "")
	{
		$add_amount=0;
	}
	my $temp_str="max_".$did;
	my $max_amount=$query->param($temp_str);
	if ($max_amount eq "")
	{
		$max_amount=-1;
	}
	$sql="insert into profile_chunk_add(profile_id,domain_id,add_amount,max_amount) values($pid,$did,$add_amount,$max_amount)";
	$rows=$dbhu->do($sql);
}
$sth->finish();
print "Location: chunking_domaina.cgi?pid=$pid\n\n";
