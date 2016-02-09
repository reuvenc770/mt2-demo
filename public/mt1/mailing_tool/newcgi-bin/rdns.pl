#!/usr/bin/perl
use strict;
use Net::DNS;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $dbh;
my $query;
my $new_name;
my $sql;
my $list_id;
my $ip_addr;
my $list_name;
my $rows;


# connect to the util database
$| = 1;
$util->db_connect();
$dbh = $util->get_dbh;

my $extra=shift;
my $debug=shift;

my $extra_sql=qq^AND list_name like '%$extra%'^ if $extra;
$sql = "select list_id,ip_addr,list_name from list where list_type='CHUNK' and status='A' $extra_sql";
$sth = $dbh->prepare($sql);
$sth->execute();
my $res   = Net::DNS::Resolver->new;
while (($list_id,$ip_addr,$list_name) = $sth->fetchrow_array())
{
	$query = $res->search($ip_addr);
	if ($query) 
	{
		foreach my $rr ($query->answer) 
		{
			next unless $rr->type eq "PTR";
			$new_name=$rr->rdatastr;			
			chop($new_name);
			print "NEW: $new_name - OLD: $list_name\n" if $debug;
			if ($list_name ne $new_name)
			{
				print "$ip_addr\t$list_name\t$new_name\n";
				$sql="update list set list_name='$new_name' where list_id=$list_id";
				print "$sql\n" if $debug;
				$rows=$dbh->do($sql);
			}
		}
	}
}
$sth->finish();
$util->clean_up();
exit(0);
  
