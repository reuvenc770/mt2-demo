#!/usr/bin/perl
#===============================================================================
# Purpose: Adds URLs for a Brand 
# Name   : add_url.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/07 Raymond Li
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use util;
require "/usr/local/share/modules/DB.pm";
require "/usr/local/share/modules/Common.pm";

my $args=Common::get_args();
my $dbh=DB::connect_db('db_user');
my $btype;
my $nl_id;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) {
    print "Location: notloggedin.cgi\n\n";
}
else {
	my $sql="select brand_type,nl_id from client_brand_info where brand_id=$args->{bid}";
	my $sth2=$dbh->prepare($sql);
	$sth2->execute();
	($btype,$nl_id)=$sth2->fetchrow_array();
	$sth2->finish();

	if ($args->{url}) {
		my @urls=split('\n', $args->{url});
		my $rank=$args->{next_rank};
		foreach my $url (@urls) {
			$url=~s/\n|\r|\s//g;
			$url=lc $url;
			my $qIn=qq|INSERT INTO brand_available_domains (brandID,domain,type,rank) VALUES ($args->{bid},'$url',|
				   .qq|'$args->{type}',$rank)|;
			$dbh->do($qIn);
			if (($btype eq "Newsletter") and ($args->{upd} eq "Y") and ($nl_id > 0))
			{
				my $qIn=qq|INSERT INTO brand_available_domains (brandID,domain,type,rank) select brand_id,'$url',|
				   .qq|'$args->{type}',$rank from client_brand_info where nl_id=$nl_id and status='A' and brand_id != $args->{bid}|;
				open(LOG,">/tmp/j.j");
				print LOG "<$qIn>\n";
				close(LOG);
				$dbh->do($qIn);
			}
			$rank++;
		}
		print "Location: add_brand_url.cgi?type=$args->{type}&bid=$args->{bid}&upd=$args->{upd}\n\n";
	}
	elsif ($args->{edit_domain} || $args->{submit} eq 'Edit') {
		$args->{edit_domain}=lc $args->{edit_domain};			
		my $qUpdate=qq|UPDATE brand_available_domains SET domain='$args->{edit_domain}' WHERE id=$args->{edit_id}|;
		$dbh->do($qUpdate);
		print "Location: add_brand_url.cgi?type=$args->{type}&bid=$args->{bid}&upd=$args->{upd}\n\n";
	}
	elsif ($args->{del_domain} || $args->{submit} eq 'Delete') 
	{
		my $cdomain;
		if ($args->{upd} eq "Y")
		{
			my $sql="select domain from brand_available_domains where id=$args->{del_id}";
			my $sth=$dbh->prepare($sql);
			$sth->execute();
			($cdomain)=$sth->fetchrow_array();
			$sth->finish();
		}	
		my $qDel=qq|DELETE FROM brand_available_domains WHERE id=$args->{del_id}|;
		$dbh->do($qDel);
		if (($cdomain ne "") and ($args->{upd} eq "Y"))
		{
			$qDel=qq|DELETE FROM brand_available_domains WHERE domain='$cdomain' and brandID in (select brand_id from client_brand_info where nl_id=$nl_id and status='A')|;
			$dbh->do($qDel);
		}
		print "Location: add_brand_url.cgi?type=$args->{type}&bid=$args->{bid}&upd=$args->{upd}\n\n";
	}
	elsif ($args->{act}) {
		my $domain=pull_domains($dbh,$args);
		my $sub_name=$args->{act} eq 'e' ? "edit_domain" : "del_domain";
		my $sub_id=$args->{act} eq 'e' ? "edit_id" : "del_id";
		my $sub_but=$args->{act} eq 'e' ? "Edit" : "Delete";
		print "Content-type: text/html\n\n";
		print qq^
        <html>
          <head>
            <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
            <title>Available domains for Brand</title>
          </head>
          <body>
        <form action="/cgi-bin/add_brand_url.cgi" method="post">
		<p><b>URL: </b><br>
		<input type=text name="$sub_name" value="$domain">
        <input type=hidden name="$sub_id" value="$args->{act_url}">
		<input type=hidden name=bid value="$args->{bid}">
		<input type=hidden name=type value="$args->{type}">
		<input type=hidden name=upd value="$args->{upd}">
		<input type=submit name="submit" value="$sub_but">
		</form>
		</body></html>^;
	}
	else {
		my ($hr,$next_rank)=pull_available_domains($dbh,$args);
		my $url_str;
		foreach my $rank (sort {$a<=>$b} keys %$hr) {
			$url_str.=qq^<a href="http://$hr->{$rank}" target=_new>$hr->{$rank}</a><br>^;
		}
		if (!$url_str) { $url_str="None<br>"; }
		print "Content-Type: text/html\n\n";
		print qq^
		<html>
		  <head>
			<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
			<title>Add available domains for Brand</title>
		  </head>
		  <body>
			<p><b>Current Inserted URL(s): </b></br>
			$url_str
		^;

		print qq^
		<p><b>URLs: (Hit ENTER after each one) </b><br>
		<form action="/cgi-bin/add_brand_url.cgi" method="post">
		<input type=hidden name=bid value="$args->{bid}">
		<input type=hidden name=type value="$args->{type}">
		<input type=hidden name=next_rank value="$next_rank">
		<input type=hidden name=upd value="$args->{upd}">
		<textarea name="url" rows="7" cols="82"></textarea></p>
		<p>
		<input type=image name="add_url" height="22" src="/images/save_rev.gif" width="81" border="0">
		</form>
		</body>
		</html>
		^;
	}
}

sub pull_available_domains {
	my ($dbh,$args)=@_;

	my $filter=$args->{act_url} ? qq^AND id=$args->{act_url}^ : "";
	my $hr={}; my $count=1;
	my $qURL=qq|SELECT domain,rank FROM brand_available_domains WHERE brandID=$args->{bid} AND type='$args->{type}' $filter|;
	my $sth=$dbh->prepare($qURL);
	$sth->execute;
	while (my ($domain,$rank)=$sth->fetchrow) {
		$hr->{$rank}=$domain;
		$count++;
	}
	$sth->finish;
	if ($args->{act_url}) {
		return $hr->{$args->{act_url}};
	}
	else {
		return ($hr,$count);
	}
}

sub pull_domains {
	my ($dbh,$args)=@_;

	my $filter=$args->{act_url} ? qq^AND id=$args->{act_url}^ : "";
	my $hr={}; my $count=1;
	my $qURL=qq|SELECT domain,rank FROM brand_available_domains WHERE brandID=$args->{bid} AND type='$args->{type}' $filter|;
	my $sth=$dbh->prepare($qURL);
	$sth->execute;
	my ($domain,$rank)=$sth->fetchrow(); 
	$sth->finish;
	return $domain;
}

$dbh->disconnect;
exit;
