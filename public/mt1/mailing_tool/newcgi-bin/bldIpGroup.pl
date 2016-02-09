#!/usr/bin/perl
#
use strict;
use DBI;
use MIME::Lite;
use Data::Dumper;
use Lib::Database::Perl::Interface::Server;
use Lib::Database::Perl::Interface::SeedMonitor;
use lib "/var/www/html/newcgi-bin";
use util;
my $util = util->new;

my $NODECNT;
my $CLASS;
my $MTA;
my $IP;
my $params = {};
my $mgmtIP;
my @iparry;
my $dbhq;
my $dbhu;
my $mgmtip;
my $sql;
my $sth;
my $i;
my $j;
my $k;
my @seeds;
my $mta;
my $GRP;
my $grpcnt;
my $pname;
my $tname;
my $grpSize;
my $lastmta;
my $writedb=$ARGV[0];
if ($writedb eq "")
{
	$writedb=0;
}
else
{
	$writedb=1;
}
#my $notify_email_addr="dpappas\@spirevision.com";
my $notify_email_addr="mailops\@spirevision.com";
#	my $notify_email_addr="jsobeck\@spirevision.com";
my $cc_email_addr="jsobeck\@spirevision.com";
my $mail_mgr_addr="info\@spirevision.com";
($dbhq,$dbhu)=$util->get_dbh();
$|=1;
my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );
$mon++;
my $dstr=$mon."_".$mday;
#
# Okay get list of IpGroupProfiles
#
my $profiles=getIpGroupProfiles();
my $pcnt=$#{$profiles};

my $serverInterface	= Lib::Database::Perl::Interface::Server->new();
my $tfile="/tmp/bldip_".$$.".csv";
open(OUT,">$tfile");
my $sendfile=0;
for ($i=0; $i<= $pcnt; $i++)
{
	$NODECNT={};
	$CLASS={};
	$MTA={};
	$IP={};
	print "Profile ID $profiles->[$i]->{'IpProfileID'}\n";
	#
	# Get nodes and seeds for the Profile
	#
	my $nodes=getNodes($profiles->[$i]->{'IpProfileID'},$serverInterface);
	@seeds=getSeeds($profiles->[$i]->{'IpProfileID'});
#	display("Seeds:",\@seeds);
	#
	# For each node get IPs for the nodes
	#
	my $ncnt=$#{$nodes};
	for ($j=0; $j <= $ncnt; $j++)
	{
		my $mgmtIP=$nodes->[$j]->{'ipNode'};
		my $servID=$nodes->[$j]->{'servID'};
		$NODECNT->{$mgmtIP}{servID}=$servID;
		$params={};
		$params->{'serverID'}=$servID;
		my ($errors, $results1) = $serverInterface->getNodeIpsInAllTables($params);
		my $icnt=$#{$results1};
		for ($k=0; $k <= $icnt; $k++)
    	{
			my $ip=$results1->[$k]->{'ip'};
			my $cclass=getCclass($ip);
			$IP->{$ip}{mgmtIP}=$mgmtIP;
			$IP->{$ip}{mta}=$results1->[$k]->{'mtaHostname'};
			$IP->{$ip}{cclass}=$cclass;
			$NODECNT->{$mgmtIP}{mta}=$IP->{$ip}{mta};
			$CLASS->{$cclass}{mta}=$IP->{$ip}{mta};
#			print "Adding $ip $cclass $IP->{$ip}{mta} <$mgmtIP>\n";
			push(@iparry,$ip);
		}
	}

	my $deliveryInterface    = Lib::Database::Perl::Interface::SeedMonitor->new();
	#
	# Get inboxed IPs first
	#
	$params={};
	my $cdate=getCurdate();
	$params->{'dateBegin'}=$cdate;
	$params->{'dateEnd'}=$cdate;
	$params->{'emailAddresses'}=\@seeds;
	$params->{'inbox'}=1;
	$params->{'ips'}=\@iparry;
	my ($errors,$results2) = $deliveryInterface->getEmailSeedMonitor($params);
	my $cnt=$#{$results2};
	for ($k=0; $k <= $cnt; $k++)
	{
		my $ip=$results2->[$k]->{'ip'};
		my $cclass=getCclass($ip);
		$mgmtIP=$IP->{$ip}{mgmtIP};
		$mta=$IP->{$ip}{mta};
#		print "INBOX: $ip $cclass $mta\n";

		$NODECNT->{$mgmtIP}{cnt}++;
		$CLASS->{$cclass}{cnt}++;
		$MTA->{$mta}{cnt}++;
		if ($IP->{$ip}{include})
		{
		}
		else
		{
			$NODECNT->{$mgmtIP}{unqcnt}++;
			$CLASS->{$cclass}{unqcnt}++;
			$MTA->{$mta}{unqcnt}++;
		}
		$IP->{$ip}{include}=1;
	}
	#
	# Check to see if need to build Bulk IP groups
	#
	if ($profiles->[$i]->{'useBulkIps'} eq "Y")
	{
		$params->{'inbox'}=0;
		my ($errors,$results2) = $deliveryInterface->getEmailSeedMonitor($params);
		my $cnt=$#{$results2};
		for ($k=0; $k <= $cnt; $k++)
		{
			my $ip=$results2->[$k]->{'ip'};
			my $cclass=getCclass($ip);
			$mgmtIP=$IP->{$ip}{mgmtIP};
			$mta=$IP->{$ip}{mta};
			print "SPAM $ip $cclass $mta\n";
			if ($IP->{$ip}{include})
			{
			}
			else
			{
				$NODECNT->{$mgmtIP}{unqcnt}++;
				$CLASS->{$cclass}{unqcnt}++;
				$MTA->{$mta}{unqcnt}++;
			}
			$IP->{$ip}{include}=1;
		}
	}
	#
	# Now build the IP Groups
	#
	$pname=$profiles->[$i]->{'profileName'};
	$grpSize=$profiles->[$i]->{'minIpGroupSize'};
	$grpcnt=1;
	my $ipcnt=0;
	print "PROFILE: $pname $grpSize\n";
	foreach $mta (sort {$MTA->{$b}{cnt} <=> $MTA->{$a}{cnt} } keys %$MTA)
	{
		if ($MTA->{$mta}{unqcnt} > 0)
		{
			$lastmta=$mta;
			print "$mta $MTA->{$mta}{cnt} <$MTA->{$mta}{unqcnt}>\n\n";
			if (($profiles->[$i]->{'pType'} eq "Use Same Node Only") or ($profiles->[$i]->{'pType'} eq "Use Same MTA Only"))
			{
				($grpcnt,$ipcnt)=bldIPGroup($profiles->[$i]->{'pType'},$pname,$mta,$MTA->{$mta}{unqcnt},$grpSize,$ipcnt,$grpcnt,$IP,0);
				if ($profiles->[$i]->{'pType'} eq "Use Same MTA Only")
				{
					print "IPCNT: $ipcnt Grpsize: $grpSize\n";
					if ($ipcnt < $grpSize)
					{
						($tname)=bldName($pname,$dstr,$mta,$grpcnt);
						print "Resetting ips for $tname\n";
						$GRP->{$tname}{ips}="";
					}
					else
					{
						$grpcnt++;
					}
					$ipcnt=0;
				}
			}
			else
			{
				foreach $mgmtip (sort {$CLASS->{$b}{cnt} <=> $CLASS->{$a}{cnt} } keys %$CLASS)
				{
					if ($CLASS->{$mgmtip}{mta} eq $mta)
					{
						if ($CLASS->{$mgmtip}{cnt} > 0)
						{
							print "$mgmtip $CLASS->{$mgmtip}{cnt} <$CLASS->{$mgmtip}{unqcnt}>\n";
							($grpcnt,$ipcnt)=bldIPGroup($profiles->[$i]->{'pType'},$pname,$mgmtip,$CLASS->{$mgmtip}{unqcnt},$grpSize,$ipcnt,$grpcnt,$IP,0);
						}
					}
				}
			}
		}
	}
	if ($ipcnt < $grpSize)
	{
		($tname)=bldName($pname,$dstr,$lastmta,$grpcnt);
		print "Resetting ips for $tname\n";
		$GRP->{$tname}{ips}="";
		$grpcnt--;
	}
	#
	# print out groups
	#
	my $numGroups=$profiles->[$i]->{'minNumGroups'};
	print "Group count: $numGroups $grpcnt\n";
	if ($grpcnt < $numGroups)
	{
    	open (MAIL,"| /usr/sbin/sendmail -t");
    	print MAIL "From: $mail_mgr_addr\n";
    	print MAIL "To: $notify_email_addr\n";
    	print MAIL "Cc: $cc_email_addr\n";
    	print MAIL "Subject: IpGroup Profile Creation Results\n";
		print MAIL "Profile Name: $pname Not enought groups - only created $grpcnt and needed $numGroups\n";
		close(MAIL);
		exit();
	}
	$grpSize--;
	my $gname;
	my $group_id;
	$sendfile=1;
	print OUT "\nProfile: $pname\n";
	print OUT "-----------------------------------------------------\n";
	foreach $gname (sort {$GRP->{$a} cmp $GRP->{$b} } keys %$GRP)
	{
		my $ipstr=$GRP->{$gname}{ips};
		if ($writedb)
		{
			$group_id=getIpGroup($gname);
		}
		my @IPS=split('\|',$ipstr);
		print "IPS: $#IPS <$grpSize>\n";
		if ($#IPS >= $grpSize)
		{
			my $i1;
			for ($i1=0; $i1 <= $#IPS; $i1++)
			{
				print OUT "$IP->{$IPS[$i1]}{mta},$gname,$IPS[$i1],0,\n";
				if ($writedb)
				{
					addIPdb($group_id,$IPS[$i1]);
				}
			}
		}
	}
}
close(OUT);
if ($sendfile)
{
	my $subject="IPGroup Profile Creation Results";
	if ($writedb)
	{
		$subject=$subject." - Database Updated";
	}
my $msg = MIME::Lite->new(
    From    => $mail_mgr_addr, 
    To      => $notify_email_addr, 
    Cc      => $cc_email_addr, 
    Subject => $subject, 
    Type    => 'multipart/mixed',
);

$msg->attach(
    Type     => 'text/csv',
    Path     => $tfile, 
    Filename => 'results.csv'
);
$msg->send;


}

sub display
{
	my ($message, $displayValue)	= @_;
	
	print "\n" . '*' x 30 ."\n\n";
	print "$message: " . Dumper($displayValue) . "\n";	
}

sub getIpGroupProfiles
{
	my $sql;
	my $rows=[];

	$sql="select IpProfileID,profileName,minNumGroups,minIpGroupSize,pType,useBulkIps from IpGroupProfile where profileStatus='Active'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (my $row=$sth->fetchrow_hashref())
	{
		push(@$rows,$row);
	}
	$sth->finish();
	return($rows);
}
sub getNodes
{
	my ($profileID)=@_;
	my $sql;
	my $rows=[];
	my $params={};

	$sql="select ipNode from IpGroupProfileNode where IpProfileID=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($profileID);
	while (my $row=$sth->fetchrow_hashref())
	{
		$params->{'managementIp'}=$row->{'ipNode'};
		my ($errors, $results1) = $serverInterface->getNodeServers($params);
		$row->{'servID'}=$results1->[0]->{'serverID'};
		push(@$rows,$row);
	}
	$sth->finish();
	return($rows);
}
sub getSeeds
{
	my ($profileID)=@_;
	my $sql;
	my @seeds; 
	my $em;

	$sql="select emailAddr from IpGroupProfileSeed where IpProfileID=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($profileID);
	while (($em)=$sth->fetchrow_array())
	{
		push(@seeds,$em);
	}
	$sth->finish();
	return(@seeds);
}
sub getCurdate
{
	my $sql;
	my $cdate;

	$sql="select curdate()";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($cdate)=$sth->fetchrow_array();
	$sth->finish();
	return($cdate);
}
sub bldIPGroup
{
	my ($ptype,$pname,$value,$cnt,$grpsize,$ipcnt,$grpcnt,$IP,$spam)=@_;
	my $addit;
	my $field;

	print "<$ptype> <$value> $cnt $grpsize $ipcnt $grpcnt <$spam>\n";
	if (($ptype eq "Use Same Node Only") or ($ptype eq "Use Same C-Class Only") or ($ptype eq "Use Same MTA Only"))
	{
		if ($cnt < $grpsize)
		{
			print "Skipping $value only $cnt need $grpsize\n";
			$ipcnt=0;
			return($grpcnt,$ipcnt);
		}
		my $tcnt=int($cnt/$grpsize);
		$grpsize=int(($cnt+$tcnt-1)/$tcnt);		
		print "Setting group size to $grpsize\n";
	}
	foreach (keys %{$IP})
	{
		my $ip=$_;
		$addit=0;
		$field=$IP->{$ip}{include};;
		if ($spam)
		{
			$field=$IP->{$ip}{spaminclude};
		}
		if ($field)
		{
			if (($IP->{$ip}{mta} eq $value) and (($ptype eq "Use Same Node Only") or ($ptype eq "Use Same MTA Only")))
			{
				$addit=1;
			}
			elsif (($IP->{$ip}{cclass} eq $value) and ($ptype eq "Use Same C-Class Only"))
			{
				$addit=1;
			}
		}
		if ($addit)
		{			
			$ipcnt++;
			if ($ipcnt <= $grpsize)
			{
				($tname)=bldName($pname,$dstr,$IP->{$ip}{mta},$grpcnt);
				addIP($tname,$ip,$IP->{$ip}{mta});
#				print "Adding $value $ip to $tname\n";
			}
			else
			{
				$grpcnt++;
				$ipcnt=1;
				($tname)=bldName($pname,$dstr,$IP->{$ip}{mta},$grpcnt);
				addIP($tname,$ip,$IP->{$ip}{mta});
#				print "Adding $value $ip to $tname\n";
			}
		}
	} 
	if (($ptype eq "Use Same Node Only") or ($ptype eq "Use Same C-Class Only"))
	{
		$ipcnt=0;
		$grpcnt++;
	}
	return($grpcnt,$ipcnt);
}

sub addIP
{
	my ($pname,$ip,$mta)=@_;
	my $ipstr=$GRP->{$pname}{ips};
	$ipstr=$ipstr.$ip."|";
	$GRP->{$pname}{ips}=$ipstr;
	$GRP->{$pname}{mta}=$mta;
	print "Adding $ip to $pname for $mta\n";
}

sub getCclass
{
	my ($ip)=@_;
    my @T=split('\.',$ip);
    my $cclass=$T[0].".".$T[1].".".$T[2];
	return $cclass;
}

sub bldName
{
	my ($pname,$dstr,$mta,$grpcnt)=@_;
	my ($tmta,@T)=split('\.',$mta);
	$tmta=~s/mta//;
#	my $ipname=$pname."_".$dstr."_".$tmta."_".$grpcnt;
	my $ipname=$pname."_".$dstr."_".$grpcnt;
	return($ipname);	
}

sub log_or_execute 
{
    my ($q)=@_;

    $dbhu=DBI->connect("DBI:mysql:supp:masterdb.routename.com","db_user","sp1r3V") unless $dbhu->ping();
    $dbhu->do($q);
    if ($dbhu->err() && $dbhu->err() != 0) {
        my $err_msg=$dbhu->errstr();
        print "ERROR: $err_msg <$q>\n";
    }
}

sub getIpGroup
{
	my ($gname)=@_;
	my $gid="";
	my $sql;

	while ($gid eq "")
	{
		$sql="select group_id from IpGroup where group_name=? and status='Active'";
		my $sth=$dbhu->prepare($sql);
		$sth->execute($gname);
		if (($gid)=$sth->fetchrow_array())
		{
		}
		else
		{
			$sql="insert into IpGroup(group_name,status) values('$gname','Active')";
			log_or_execute($sql);
		}
		$sth->finish();
	}
	$sql="delete from IpGroupIps where group_id=$gid";
	log_or_execute($sql);
	return $gid;
}

sub addIPdb
{
	my ($gid,$ip)=@_;
	my $sql="insert into IpGroupIps(group_id,ip_addr) values($gid,'$ip')";
	log_or_execute($sql);
}
