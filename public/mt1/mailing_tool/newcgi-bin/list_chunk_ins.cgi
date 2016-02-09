#!/usr/bin/perl
# *****************************************************************************************
# list_chunk_ins.cgi
#
# this page inserts new chunk lists 
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
my $list_name;
my $rows;
my $errmsg;
my $client_id = $query->param('company_id');

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

# get fields from the form

my $server_id = $query->param('server_id');
my @ip_addr = $query->param('ip_addr');
foreach my $c1 (@ip_addr)
{
	# Add the list record
	$sql="select rdns from server_ip_config where id=? and ip=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($server_id,$c1);
	($list_name)=$sth->fetchrow_array();
	$sth->finish();
#
	$sql = "insert into list (list_name, user_id, status, server_id,ip_addr,list_type) values ('$list_name', $client_id, 'A',$server_id,'$c1','CHUNK')";
open(LOG,">>/tmp/j.j");
print LOG "$sql\n";
close LOG;
	$rows = $dbhu->do($sql);
}

print "Location: list_chunk_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
