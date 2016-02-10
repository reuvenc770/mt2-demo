#!/usr/bin/perl
#
#use strict;
use DBI;
use Sys::Hostname;
use MIME::Base64;
use Net::Ping;
use File::Sort qw(sort_file);
use vars qw($hrINIT $DBHU $hrSVR $hrADV $FILE $LIST);
use lib "/var/www/bin";
use mta;
my $total_cnt;
my $DAY_SECONDS=24*3600;
my $ret_stat;
my $sortfile;
my $DUP={};
my $CCNT;
my $old_creativeid;

$|=1;
my $campaign_str;
my $temp_id;
my $got_deal;
my $camp_id;
my $sql;
my $sth;
my $sent_seeds;
my $send_adv_seeds;
my $domain_id;
my $MAX_CAMPS=1;
my $camp_cnt;
my $close;
my $FLATDIR="/var/www/util/data";
my $lsdName;
my $qSel;
my $all_recs;
my $temp_flag;
my $temp_max;
my $old_total;
my $articleid;
my $blurbid;
my $headerid;
my $darr_cnt;
my $priority;
my $class_name;
my $master_str;
my @CAMPCLIENT;
my @ATTRIB;
my $LINKID;
my $startmonth;
my $endmonth;
my $monthcnt;
my $exclude_domain;
my $EXISP={};
my $hrProf;
$exclude_domain="";
my $ostart;
my $oend;
my $cstart;
my $cend;
my $dcalcstart;
my $dcalcend;
my $converter_start;
my $converter_end;

$ostart=-1;
$oend=-1;
$cstart=-1;
$cend=-1;
$dcalcstart=-1;
$dcalcend=-1;
$converter_start=-1;
$converter_end=-1;

$FILE=$LIST='';
my $host=hostname();

my $check_string="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | grep -v pipe_w | /bin/grep -c calc_profilev3.pl";
my $alreadyRunning=`$check_string`;
chomp($alreadyRunning);
#print "Count <$alreadyRunning>\n";
if ($alreadyRunning >= 5)
{
    exit;
}

#set log file
$hrINIT->{'log'} = setLogFile();
my $mta=mta->new;

$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to db: " . DBI->errstr, 1);

cleanup_old_ones();
$mta->set_dbhu($DBHU);
my (@emailClasses)  = mta->getEmailClasses($DBHU);

$sql="select check_id,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,send_international,send_confirmed,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,source_url,volume_desired,gender,min_age,max_age,type from UniqueCheck where status='A' and type='v3' limit 1 for update";
eval
{
	$DBHU->begin_work;
	my $sth=prep_and_exec($sql);
	my $err_msg = $DBHU->errstr();
		
	if (($hrProf) = $sth->fetchrow_hashref())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$hrProf->{check_id}=0;
	}
	if ($hrProf->{check_id} > 0)
	{
		##  set selected campaign to pending
		my $qUpdStat1=qq^UPDATE UniqueCheck SET status='P' WHERE check_id=$hrProf->{check_id}^;
		log_or_execute($qUpdStat1);
	}
	else
	{
		exit();
	}
	$DBHU->commit;
};
if ($@)
{
	local $DBHU->{RaiseError}=0;
	$DBHU->rollback;
	log_notice("ERROR: rolling back",0);
	exit();
}

$sql="select ucc.client_id,ifnull(level,999) from UniqueCheckClient ucc left outer join UniqueAttribution ua on ucc.client_id=ua.client_id where ucc.check_id=?"; 
my $sth=prep_and_exec($sql,[$hrProf->{check_id}]);
my $tcid;
my $tclient;
my $link_id;
my $i=0;
while (($tclient,$attrib)=$sth->fetchrow_array())
{
	$CAMPCLIENT[$i]=$tclient;
	$ATTRIB[$i]=$attrib;
	$i++;
}
$sth->finish();
my $urlregex="";
my $url;
$sql="select source_url from UniqueCheckUrl where check_id=?"; 
$sth=prep_and_exec($sql,[$hrProf->{check_id}]);
while (($url)=$sth->fetchrow_array())
{
	$urlregex.=$url."|";
}
chop($urlregex);
my $regexzips="";
my $tzip;
$sql="select zip from UniqueCheckZip where check_id=?"; 
$sth=prep_and_exec($sql,[$hrProf->{check_id}]);
while (($tzip)=$sth->fetchrow_array())
{
	$regexzips.=$tzip."|";
}
chop($regexzips);

$total_cnt=0;
my $curcnt=0;
$curcnt = getSegment(0,30,0,30,0,30,0,0,$curcnt);
if ($curcnt < $hrProf->{volume_desired})
{
	$curcnt = getSegment(0,0,0,0,0,0,0,10,$curcnt);
	if ($curcnt < $hrProf->{volume_desired})
	{
		$curcnt = getSegment(31,90,31,90,31,90,0,0,$curcnt);
		if ($curcnt < $hrProf->{volume_desired})
		{
			$curcnt = getSegment(0,0,0,0,0,0,11,90,$curcnt);
		}
	}
}
if ($ostart eq "")
{
	$ostart=0;
}
if ($cstart eq "")
{
	$cstart=0;
}
if ($dcalcstart eq "")
{
	$dcalcstart=0;
}
if ($converter_start eq "")
{
	$converter_start=0;
}
my $check_id=$hrProf->{check_id};
$sql="update UniqueCheck set volume_calculated=$curcnt,opener_start1=$ostart,opener_end1=$oend,clicker_start1=$cstart,clicker_end1=$cend,deliverable_start1=$dcalcstart,deliverable_end1=$dcalcend,convert_start1=$converter_start,convert_end1=$converter_end where check_id=$check_id"; 
log_or_execute($sql);
foreach (keys %{$CCNT})
{
	my $tclient=$_;
    $cnt=$CCNT->{$tclient};
	$sql="update UniqueCheckClient set record_cnt=$cnt where check_id=$check_id and client_id=$tclient";
	log_or_execute($sql);
}
$sql="update UniqueCheck set status='F' where check_id=$hrProf->{check_id}";
log_or_execute($sql);

sub getSegment
{
	($openstart,$openend,$clickerstart,$clickerend,$converterstart,$converterend,$dstart,$dend,$curcnt)=@_;
	my $infile=$FLATDIR."/".$hrProf->{check_id}.".txt";
	open(OUT,">$infile");
	for (my $c1ind=0; $c1ind <= $#CAMPCLIENT; $c1ind++)
	{
		for (my $cind=0; $cind <= $#emailClasses; $cind++) 
		{
			my $class=$emailClasses[$cind];
			$class_id=$class;
			# 
			# Check to see if class selected for domain
			#
			$sql="select lower(class_name) from email_class,UniqueCheckIsp where email_class.status='Active' and email_class.class_id=? and email_class.class_id=UniqueCheckIsp.class_id and UniqueCheckIsp.check_id=?";
			my $sthCL=prep_and_exec($sql,[$class,$hrProf->{check_id}]);
			if (($class_name)=$sthCL->fetchrow_array())
			{
				$sthCL->finish();
				log_notice("INFO: Class $class Name <$class_name>",0);
			}
			else
			{
				$sthCL->finish();
				next;
			}
	
			my $list_id;
			if (($openstart != 0) or ($openend != 0))
			{
				my $tparams={};
				$tparams->{'emailActionType'}="opener";
				$tparams->{'emailClass'}=$class_name;
				$tparams->{'minDay'}=$openstart;
				$tparams->{'maxDay'}=$openend;
				my $data=$mta->getFlatFileNameList($tparams);
				my $cnt=$#{$data};
				my $i=0;
				while ($i <= $cnt)
				{
        			my $flatFile=$data->[$i];
	        		my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
	        		build_file($list_id, "$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$openstart,$openend,$class,$ATTRIB[$c1ind],$urlregex,"OPENER",$CAMPCLIENT[$c1ind],$regexzips,$hrProf); 
					$i++;
				}
			}
			if (($clickerstart != 0) or ($clickerend != 0))
			{
				my $tparams={};
				$tparams->{'emailActionType'}="clicker";
				$tparams->{'emailClass'}=$class_name;
				$tparams->{'minDay'}=$clickerstart;
				$tparams->{'maxDay'}=$clickerend;
				my $data=$mta->getFlatFileNameList($tparams);
				my $cnt=$#{$data};
				my $i=0;
				while ($i <= $cnt)
				{
        			my $flatFile=$data->[$i];
	        		my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
	       			build_file($list_id,"$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$clickerstart,$clickerend,$class,$ATTRIB[$c1ind],$urlregex,"CLICKER",$CAMPCLIENT[$c1ind],$regexzips,$hrProf); 
					$i++;
				}
			}
			if (($converterstart != 0) or ($converterend != 0))
			{
				my $tparams={};
				$tparams->{'emailActionType'}="converter";
				$tparams->{'emailClass'}=$class_name;
				$tparams->{'minDay'}=$converterstart;
				$tparams->{'maxDay'}=$converterend;
				my $data=$mta->getFlatFileNameList($tparams);
				my $cnt=$#{$data};
				my $i=0;
				while ($i <= $cnt)
				{
        			my $flatFile=$data->[$i];
	        		my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
	        		build_file($list_id, "$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$converterstart,$converterend,$class,$ATTRIB[$c1lind],$urlregex,"CONVERTER",$CAMPCLIENT[$c1ind],$regexzips,$hrProf); 
					$i++;
				}
			}
			if (($dstart != 0) or ($dend != 0))
			{
				my $tparams={};
				$tparams->{'emailActionType'}="deliverable";
				$tparams->{'emailClass'}=$class_name;
				$tparams->{'minDay'}=$dstart;
				$tparams->{'maxDay'}=$dend;
				my $data=$mta->getFlatFileNameList($tparams);
				my $cnt=$#{$data};
				my $i=0;
				while ($i <= $cnt)
				{
        			my $flatFile=$data->[$i];
	        		my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
	        		build_file($list_id, "$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$dstart,$dend,$class,$ATTRIB[$c1lind],$urlregex,"DELIVERABLE",$CAMPCLIENT[$c1ind],$regexzips,$hrProf); 
					$i++;
				}
			}
		}
	}
	close(OUT);
	$sortfile=$FLATDIR."/".$hrProf->{check_id}.".tmp2";
	$outfile=$FLATDIR."/".$hrProf->{check_id}.".tmp3";
	log_notice("INFO : Starting sort process");
	system("sort --key=4,4 --key=3,3 --key=13,13r -t'|' -S 5M -T /tmp -T /dev/shm $infile -o $sortfile");
	log_notice("INFO : Finished sort process");
	unlink($infile);
	remove_dupes($sortfile,$outfile);
	unlink($sortfile);
	$sortfile=$FLATDIR."/".$hrProf->{check_id}.".sorted";
	log_notice("INFO : Starting sort process");
	system("sort --key=11,11n --key=3,3n -t'|' -S 5M -T /tmp -T /dev/shm $outfile -o $sortfile");
	log_notice("INFO : Finished sort process");
	unlink($outfile);
	my $tcnt=get_send_cnt($sortfile,$hrProf->{check_id},$hrProf->{volume_desired},$curcnt);
	log_notice("CNT: $tcnt $curcnt",0);
	return($tcnt);
}

sub build_file 
{
	my ($lrLists, $input_file,$start,$end,$class,$attrib,$surl,$ctype,$tclient,$zipregex,$hrProf)=@_;
	my $zip;
	my $daycnt;
	my $lrListNum=[];
	foreach (@$lrLists) { push @$lrListNum, $_->{listID}; }
	my $lists=join(', ', @$lrListNum);
	my $hrInfo;
	my $line;
	my ($cwc3,$cwcid,$cwprogid,$cr,$landing_page);
	my $gender;
	my $age;

	my $qSelEm;
	$hrInfo->{listID}=$lists;
	log_notice("File <$input_file> <$total_cnt> <$start> <$end>",0);
	open(IN,"<$input_file") or return; 
	RECORD: while (<IN>)
	{
		$line=$_;
        $line=~ s/^M//g;
        $line=~ s/\n//g;
        ($hrInfo->{email},$hrInfo->{eID},$hrInfo->{state},$hrInfo->{fname},$hrInfo->{lname},$hrInfo->{city},$zip,$daycnt,$hrInfo->{url},$hrInfo->{cdate},$hrInfo->{ip},$hrInfo->{rtime},$gender,$age) = split('\|',$line);
        $hrInfo->{rtime}=~ s/^M//g;
        $hrInfo->{rtime}=~ s/\n//g;
		$_=$hrInfo->{email};
		if (/@/)
		{
		}
		else
		{
			next RECORD;
		}
		if ($DUP->{$hrInfo->{email}})
		{
			next RECORD;
		}
		if ($exclude_domain ne "")
		{
        	my $blockCodeString = '(' . $exclude_domain . ')';
        	my $blockCodeRegExp = qr{$blockCodeString};
        	if ($hrInfo->{email} =~ m/$blockCodeRegExp/)
        	{
				next RECORD;	
			}
		}
        if ($end == -1)
        {
        	next RECORD if $daycnt < $start;
        }
        else
        {
        	if (($daycnt < $start) or ($daycnt > $end))
            {
            	next RECORD;
			}
        }
        if ($surl ne "")
        {
        	if ($hrInfo->{url} =~ m/$surl/i)
            {
            }
            else
            {
            	next RECORD;
            }
        }
        if ($zipregex ne "")
        {
        	if ($zip =~ m/$zipregex/i)
            {
            }
            else
            {
            	next RECORD;
            }
        }
		if ($hrProf->{gender} ne "")
		{
			if ($hrProf->{gender} eq "Empty")
			{
				if ($gender ne "")
				{
					next RECORD;
				}
			}
			elsif ($hrProf->{gender} eq $gender)
			{
			}
			else
			{
				next RECORD;
			}
		}
		if (($hrProf->{min_age} > 0) or ($hrProf->{max_age} > 0))
		{
			if (($age < $hrProf->{min_age}) or ($age > $hrProf->{max_age})) 
			{
				next RECORD;
			}
		}
		
		my $campid=0;
		print OUT "$tclient|$class|$attrib|$hrInfo->{email}|$hrInfo->{eID}|$hrInfo->{state}|$hrInfo->{fname}|$hrInfo->{lname}|$hrInfo->{city}|$zip|$daycnt|$hrInfo->{url}|$hrInfo->{cdate}|$hrInfo->{ip}|$hrInfo->{rtime}|$ctype|\n";
		$total_cnt++;
	}
	close(IN);
}
sub setLogFile {

	my ($date, $time)=get_current_datetime(1);
	
	return("/tmp/calc_v3.log.$date");	
	
}

sub get_current_datetime {
	my ($split)=@_;
	$split||=0;
	my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
	$month+=1; $year+=1900;
	$sec=(length($sec) < 2) ? "0$sec" : $sec;
	$min=(length($min) < 2) ? "0$min" : $min;
	$hr=(length($hr) < 2) ? "0$hr" : $hr;
	$day=(length($day) < 2) ? "0$day" : $day;
	$month=(length($month) < 2) ? "0$month" : $month;
	return ("$year-$month-$day", "$hr:$min:$sec") if $split == 1;
	return "$year-$month-$day $hr:$min:$sec";
}

sub log_or_execute {
	my ($q)=@_;
	
	log_notice("INFO : $q");
##
## 08/21/06 - JES - Added logic so only one version of code needed for internal and external servers
##
	$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to update db: " . DBI->errstr, 1) unless $DBHU->ping();
	unless ($hrINIT->{debug}) {
		$DBHU->do($q);
		if ($DBHU->err() && $DBHU->err() != 0) {
		my $err_msg=$DBHU->errstr();
			log_notice("ERROR: $err_msg <$q>");
		}
	}
}

sub prep_and_exec {
    my ($qSel, $lrParams)=@_;
    $lrParams||=[];

$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to update db: " . DBI->errstr, 1) unless $DBHU->ping();
    my $sth=$DBHU->prepare($qSel);
    $sth->execute(@$lrParams);
    return $sth;
}

sub remove_dupes 
{
	my ($input_file,$output_file)=@_;
	my $cnt;
	my ($campid,$class,$attrib,$email,$eID,$state,$fname,$lname,$zip,$daycnt,$sdate,$url,$cdate,$ip,$rtime);
	my $ctype;
	my $tclient;
	my $old_email;
	my $ISPCNT;

	$cnt=0;
	open(IN,"<$input_file") or return ; 
	open(OUT,">$output_file") or return ; 
	RECORD: while (<IN>)
	{
		my $line=$_;
		$_=$line;
        ($tclient,$class,$attrib,$email,$eID,$state,$fname,$lname,$city,$zip,$daycnt,$url,$cdate,$ip,$rtime,$ctype) = split('\|',$line);
		if ($old_email eq $email)
		{
			next;
		}
		$old_email=$email;
		print OUT "$tclient|$class|$attrib|$email|$eID|$state|$fname|$lname|$city|$zip|$daycnt|$url|$cdate|$ip|$rtime|$ctype|\n";
	}
	close(IN);
	close(OUT);
}
sub get_send_cnt
{
	my ($input_file,$check_id,$volume_desired,$cnt)=@_;
	my ($campid,$class,$attrib,$email,$eID,$state,$fname,$lname,$zip,$daycnt,$sdate,$url,$cdate,$ip,$rtime);
	my $ctype;
	my $tclient;
	my $old_email;
	my $ISPCNT;
	my $line;
	open(IN,"<$input_file") or return ; 
	RECORD: while (<IN>)
	{
		if (($cnt >= $volume_desired) and ($volume_desired > 0))
		{
			next;
		}
		$line=$_;
		$_=$line;
        ($tclient,$class,$attrib,$email,$eID,$state,$fname,$lname,$city,$zip,$daycnt,$url,$cdate,$ip,$rtime,$ctype) = split('\|',$line);
		if ($daycnt =~ /^[+-]?\d+$/ ) 
		{
		}
		else
		{
			next;
		}
		if ($DUP->{$email})
		{
			next;
		}
		$DUP->{$email}=1;
		$CCNT->{$tclient}++;
		if ($ctype eq "OPENER")
		{
			if ($ostart == -1)
			{
				$ostart=$daycnt;
				$oend=$daycnt;
			}
			elsif ($oend < $daycnt)
			{
				$oend=$daycnt;
			}
		}
		elsif ($ctype eq "CLICKER")
		{
			if ($cstart == -1)
			{
				$cstart=$daycnt;
				$cend=$daycnt;
			}
			elsif ($cend < $daycnt)
			{
				$cend=$daycnt;
			}
		}
		elsif ($ctype eq "DELIVERABLE")
		{
			if ($dcalcstart == -1)
			{
				$dcalcstart=$daycnt;
				$dcalcend=$daycnt;
			}
			elsif ($dcalcend < $daycnt)
			{
				$dcalcend=$daycnt;
			}
		}
		elsif ($ctype eq "CONVERTER")
		{
			if ($converter_start == -1)
			{
				$converter_start=$daycnt;
				$converter_end=$daycnt;
			}
			elsif ($converter_end < $daycnt)
			{
				$converter_end=$daycnt;
			}
		}
		$cnt++;
	}
	close(IN);
	log_notice("INFO: <$ostart> <$oend> <$cstart> <$cend> <$dcalcstart> <$dcalcend>",0);
	return($cnt);
}

sub log_notice {

    my ($msg, $die)=@_;
    open (LOG, ">>$hrINIT->{'log'}") or die "can't open log: $!";
    print LOG "$timest ($$ ) - $msg\n";
    close LOG;

    #script died but we want to capture error before close.
    if($die){
    	exit;
    } #end if

} 

sub cleanup_old_ones
{
	my $sql;
	my $sth;
	my $rows;
	
	$sql="select check_id from UniqueCheck where check_date < date_sub(curdate(),interval 3 day)";
	$sth=$DBHU->prepare($sql);
	$sth->execute();
	while (($check_id)=$sth->fetchrow_array())
	{
		$sql="delete from UniqueCheckIsp where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheckClient where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheck where check_id=$check_id";
		$rows=$DBHU->do($sql);
	}
	$sth->finish();
}
