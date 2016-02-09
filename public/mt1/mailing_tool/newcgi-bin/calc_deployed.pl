#!/usr/bin/perl 
#
#use strict;
use DBI;
use Sys::Hostname;
use MIME::Base64;
use Lib::Database::Perl::Interface::Suppression;
use Net::Ping;
use File::Sort qw(sort_file);
use Net::FTP;
use vars qw($hrINIT $DBHU $hrSVR $hrADV $FILE $LIST $DBHQ);
use lib "/var/www/bin";
use mta;
my $mta = mta->new;
my $total_cnt;
my $DAY_SECONDS=24*3600;
my $ret_stat;
my $sortfile;
my $DUP={};
my $CUSTOMEM={};
my $old_creativeid;
my @GENDER;

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
my @CLASS;
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

$FILE=$LIST='';
my $host=hostname();
my $params = {};

my $check_string="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | /bin/grep -c calc_deployed.pl";
my $alreadyRunning=`$check_string`;
chomp($alreadyRunning);
if ($alreadyRunning >= 3)
{
    exit;
}

#set log file
$hrINIT->{'log'} = setLogFile();

$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to db: " . DBI->errstr, 1);
$DBHQ=DBI->connect('DBI:mysql:new_mail:slavedb.i.routename.com', 'db_readuser', 'Tr33Wat3r',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to db: " . DBI->errstr, 1);

cleanup_old_ones();

my %suppressionObjectParams;
$suppressionObjectParams{'random'}  = 1;
my $suppressionInterface    = Lib::Database::Perl::Interface::Suppression->new(%suppressionObjectParams);

$sql="select check_id,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,send_international,send_confirmed,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,source_url,gender,min_age,max_age,DeliveryDays,client_id,type,volume_desired,fieldsToExport,advertiser_id,randomize_flag,dupCnt from UniqueCheck where status='A' and type in ('Old','Export','Export Suppression') limit 1 for update";
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
	@GENDER=split('\|',$hrProf->{gender});
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

## Get number of email classes
$sql="select class_id from email_class where status='Active' order by priority,class_id";
my $sth1=prep_and_exec($sql);
my $cind;
my $class_id;
$cind=0;
while (($class_id)=$sth1->fetchrow_array())
{
	$CLASS[$cind]=$class_id;
	$cind++;
}
$sth1->finish();
log_notice("INFO: Number of classes - $#CLASS",0);

$sql="select client_id from UniqueCheckClient where check_id=?"; 
my $sth=$DBHU->prepare($sql);
$sth->execute($hrProf->{check_id});
my $tcid;
my $tclient;
my $link_id;
my $i=0;
while (($tclient)=$sth->fetchrow_array())
{
	$CAMPCLIENT[$i]=$tclient;
	$sql="select level from UniqueAttribution where client_id=?";
	my $stha=prep_and_exec($sql,[$tclient]);
	if (($attrib)=$stha->fetchrow_array())
	{
		$ATTRIB[$i]=$attrib;
	}
	else
	{
		$ATTRIB[$i]=999;
	}
	$stha->finish();
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
my $regexcountry="";
my $tcountry;
$sql="select countryID from UniqueCheckCountry  where check_id=?"; 
$sth=prep_and_exec($sql,[$hrProf->{check_id}]);
while (($tcountry)=$sth->fetchrow_array())
{
	$regexcountry.=$tcountry."|";
}
chop($regexcountry);
my $uaexzips="";
my $tua;
$sql="select userAgentStringLabelID from UniqueCheckUA where check_id=?"; 
$sth=prep_and_exec($sql,[$hrProf->{check_id}]);
while (($tua)=$sth->fetchrow_array())
{
	$regexua.=$tua."|";
}
chop($regexua);
#
# check dates
#
if (($hrProf->{opener_start_date} ne '') and ($hrProf->{opener_start_date} ne "0000-00-00"))
{
	if (($hrProf->{opener_end_date} ne '') and ($hrProf->{opener_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{opener_end_date}'),datediff(curdate(),'$hrProf->{opener_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{opener_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{opener_start},$hrProf->{opener_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
	$hrProf->{opener_start1}=0;
	$hrProf->{opener_start2}=0;
	$hrProf->{opener_end1}=0;
	$hrProf->{opener_end2}=0;
}
if (($hrProf->{clicker_start_date} ne '') and ($hrProf->{clicker_start_date} ne "0000-00-00"))
{
	if (($hrProf->{clicker_end_date} ne '') and ($hrProf->{clicker_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{clicker_end_date}'),datediff(curdate(),'$hrProf->{clicker_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{clicker_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{clicker_start},$hrProf->{clicker_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
	$hrProf->{clicker_start1}=0;
	$hrProf->{clicker_start2}=0;
	$hrProf->{clicker_end1}=0;
	$hrProf->{clicker_end2}=0;
}
if (($hrProf->{deliverable_start_date} ne '') and ($hrProf->{deliverable_start_date} ne "0000-00-00"))
{
	if (($hrProf->{deliverable_end_date} ne '') and ($hrProf->{deliverable_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{deliverable_end_date}'),datediff(curdate(),'$hrProf->{deliverable_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{deliverable_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{deliverable_start},$hrProf->{deliverable_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
	$hrProf->{deliverable_start1}=0;
	$hrProf->{deliverable_start2}=0;
	$hrProf->{deliverable_end1}=0;
	$hrProf->{deliverable_end2}=0;
}
if (($hrProf->{convert_start_date} ne '') and ($hrProf->{convert_start_date} ne "0000-00-00"))
{
	if (($hrProf->{convert_end_date} ne '') and ($hrProf->{convert_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{convert_end_date}'),datediff(curdate(),'$hrProf->{convert_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{convert_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{convert_start},$hrProf->{convert_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
	$hrProf->{convert_start1}=0;
	$hrProf->{convert_start2}=0;
	$hrProf->{convert_end1}=0;
	$hrProf->{convert_end2}=0;
}
#
# check for custom data
#
if ($hrProf->{client_id} > 0)
{
	getCustomProfileInfo($hrProf->{check_id},$hrProf->{client_id});
}
$total_cnt=0;
open(OUT,">$FLATDIR/$hrProf->{check_id}.txt");
for (my $c1ind=0; $c1ind <= $#CAMPCLIENT; $c1ind++)
{
	for (my $cind=0; $cind <= $#CLASS; $cind++) 
	{
		my $class=$CLASS[$cind];
		$class_id=$class;
		# 
		# Check to see if class selected for domain
		#
		$sql="select distinct lower(class_name) from email_class,UniqueCheckIsp where email_class.class_id=? and email_class.status='Active' and email_class.class_id=UniqueCheckIsp.class_id and UniqueCheckIsp.check_id=?";
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

		if (($hrProf->{opener_start} != 0) or ($hrProf->{opener_end} != 0))
		{
			my $tparams={};
			$tparams->{'emailActionType'}="opener";
			$tparams->{'emailClass'}=$class_name;
			($tparams->{'minDay'},$tparams->{'maxDay'})=$mta->getDateRange($hrProf->{opener_start},$hrProf->{opener_end},$hrProf->{opener_start1},$hrProf->{opener_end1},$hrProf->{opener_start2},$hrProf->{opener_end2});
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
            	my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
        		build_file("$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$hrProf->{opener_start},$hrProf->{opener_end},$class,$ATTRIB[$c1ind],$hrProf->{opener_start1},$hrProf->{opener_end1},$hrProf->{opener_start2},$hrProf->{opener_end2},$urlregex,$CAMPCLIENT[$c1ind],$regexzips,$hrProf,$class_name,"O",3,$regexcountry,$regexua); 
				$i++;
			}
		}
		if (($hrProf->{clicker_start} != 0) or ($hrProf->{clicker_end} != 0))
		{
			my $tparams={};
			$tparams->{'emailActionType'}="clicker";
			$tparams->{'emailClass'}=$class_name;
			($tparams->{'minDay'},$tparams->{'maxDay'})=$mta->getDateRange($hrProf->{clicker_start},$hrProf->{clicker_end},$hrProf->{clicker_start1},$hrProf->{clicker_end1},$hrProf->{clicker_start2},$hrProf->{clicker_end2});
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
            	my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
       			build_file("$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$hrProf->{clicker_start},$hrProf->{clicker_end},$class,$ATTRIB[$c1ind],$hrProf->{clicker_start1},$hrProf->{clicker_end1},$hrProf->{clicker_start2},$hrProf->{clicker_end2},$urlregex,$CAMPCLIENT[$c1ind],$regexzips,$hrProf,$class_name,"C",2,$regexcountry,$regexua); 
				$i++;
			}
		}
		if (($hrProf->{convert_start} != 0) or ($hrProf->{convert_end} != 0))
		{
			my $tparams={};
			$tparams->{'emailActionType'}="converter";
			$tparams->{'emailClass'}=$class_name;
			($tparams->{'minDay'},$tparams->{'maxDay'})=$mta->getDateRange($hrProf->{convert_start},$hrProf->{convert_end},$hrProf->{convert_start1},$hrProf->{convert_end1},$hrProf->{convert_start2},$hrProf->{convert_end2});
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
           		my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
        		build_file("$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$hrProf->{convert_start},$hrProf->{convert_end},$class,$ATTRIB[$c1ind],$hrProf->{convert_start1},$hrProf->{convert_end1},$hrProf->{convert_start2},$hrProf->{convert_end2},$urlregex,$CAMPCLIENT[$c1ind],$regexzips,$hrProf,$class_name,"CO",1,$regexcountry,$regexua); 
				$i++;
			}
		}

		if (($hrProf->{deliverable_start} != 0) or ($hrProf->{deliverable_end} != 0))
		{
			my $tparams={};
			$tparams->{'emailActionType'}="deliverable";
			$tparams->{'emailClass'}=$class_name;
			($tparams->{'minDay'},$tparams->{'maxDay'})=$mta->getDateRange($hrProf->{deliverable_start},$hrProf->{deliverable_end},$hrProf->{deliverable_start1},$hrProf->{deliverable_end1},$hrProf->{deliverable_start2},$hrProf->{deliverable_end2});
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
         	 	my $retstat=$mta->getFlatFile($flatFile, $CAMPCLIENT[$c1ind]);
   			 	build_file("$FLATDIR/$CAMPCLIENT[$c1ind]/$flatFile",$hrProf->{deliverable_start},$hrProf->{deliverable_end},$class,$ATTRIB[$c1ind],$hrProf->{deliverable_start1},$hrProf->{deliverable_end1},$hrProf->{deliverable_start2},$hrProf->{deliverable_end2},$urlregex,$CAMPCLIENT[$c1ind],$regexzips,$hrProf,$class_name,"D",4,$regexcountry,$regexua); 
				$i++;
			}
		}
	}
}
close(OUT);
my $infile=$FLATDIR."/".$hrProf->{check_id}.".txt";
$sortfile=$FLATDIR."/".$hrProf->{check_id}.".sorted";
$fsize=`wc -l $infile | cut -d" " -f1`;
chop($fsize);
log_notice("INFO : Starting sort process - $fsize");
system("sort --key=4,4 --key=3,3n --key=5,5r -t'|' -S 20M -T /tmp $infile -o $sortfile");
log_notice("INFO : Finished sort process");
unlink($infile);
get_send_cnt($sortfile,$hrProf->{check_id},$hrProf->{type},$hrProf->{advertiser_id},$hrProf->{dupCnt});
$fsize=`wc -l $sortfile | cut -d" " -f1`;
chop($fsize);
log_notice("INFO : Sort filesize - $fsize");
if (($hrProf->{type} eq "Export") or ($hrProf->{type} eq "Export Suppression"))
{
	randomizeSplit($sortfile,$hrProf,$fsize);
}
unlink($sortfile);
$sql="update UniqueCheck set status='F' where check_id=$hrProf->{check_id}";
log_or_execute($sql);


sub build_file 
{
	my ($input_file,$start,$end,$class,$attrib,$start1,$end1,$start2,$end2,$surl,$clientid,$zipregex,$hrProf,$class_name,$cstatus,$priority,$countryregex,$uaregex)=@_;
	my $zip;
	my $daycnt;
	my $lrListNum=[];
	my $hrInfo;
	my $line;
	my ($cwc3,$cwcid,$cwprogid,$cr,$landing_page);
	my $age;
	my $gender;

	my $qSelEm;
	log_notice("File <$input_file> <$total_cnt> <$start> <$end> <$start1> <$end1> <$start2> <$end2>",0);
	open(IN,"<$input_file") or return; 
	RECORD: while (<IN>)
	{
		$line=$_;
        $line=~ s/^M//g;
        $line=~ s/\n//g;
        ($hrInfo->{email},$hrInfo->{eID},$hrInfo->{state},$hrInfo->{fname},$hrInfo->{lname},$hrInfo->{city},$zip,$daycnt,$hrInfo->{url},$hrInfo->{cdate},$hrInfo->{ip},$hrInfo->{rtime},$gender,$age,$hrInfo->{phone},$hrInfo->{address},$hrInfo->{address2},$hrInfo->{dob},$hrInfo->{times_in_db},$hrInfo->{country},$hrInfo->{countryID},$hrInfo->{uaID}) = split('\|',$line);
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
            	if (($daycnt < $start1) or ($daycnt > $end1))
                {
                	if (($daycnt < $start2) or ($daycnt > $end2))
                    {
                    	next RECORD;
                    }
                }
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
        if ($countryregex ne "")
        {
        	if ($hrInfo->{countryID} =~ m/$countryregex/i)
            {
            }
            else
            {
            	next RECORD;
            }
        }
        if ($uaregex ne "")
        {
        	if ($hrInfo->{uaID} =~ m/$uaregex/i)
            {
            }
            else
            {
            	next RECORD;
            }
        }
		if ($hrProf->{gender} ne "")
		{
			my $got_record=0;
			foreach my $g (@GENDER)
			{
            	if ($g eq "Empty")
            	{
                	if ($gender ne "")
                	{
                    	next RECORD;
                	}
					else
					{
						$got_record=1;
					}
            	}
				elsif ($g eq "")
				{
					$got_record=1;
				}
				elsif ($g eq $gender)
				{
					$got_record=1;
				}
			}
			if (!$got_record)
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
		if ($hrProf->{client_id} > 0)
		{
            if ($CUSTOMEM->{$hrInfo->{eID}})
            {
            }
            else
            {
                next RECORD;
            }
		}
		if ($hrProf->{DeliveryDays} > 0)
		{
			$params->{'emailAddress'}=$hrInfo->{email};
			$params->{'lastDeliveredDay'}=$hrProf->{DeliveryDays};
            my ($errors, $results) = $suppressionInterface->emailDeliveredRecord($params);
            if ($results->[0]->{'md5sum'})
            {
            	next RECORD;
            }
		}
		
		print OUT "$clientid|$class|$attrib|$hrInfo->{email}|$hrInfo->{cdate}|$hrInfo->{fname}|$hrInfo->{lname}|$zip|$daycnt|$hrInfo->{url}|$hrInfo->{ip}|$gender|$class_name|$cstatus|$hrInfo->{eID}|$priority|$hrInfo->{phone}|$hrInfo->{city}|$hrInfo->{state}|$zip|$hrInfo->{address}|$hrInfo->{address2}|$hrInfo->{dob}|$hrInfo->{times_in_db}|\n";
		$total_cnt++;
	}
	close(IN);
}
sub setLogFile {

	my ($date, $time)=get_current_datetime(1);
	
	return("/tmp/calc_deployed.log.$date");	
	
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

sub prep_and_exec1 {
    my ($qSel, $lrParams)=@_;
    $lrParams||=[];

unless ($DBHQ && $DBHQ->ping) {
$DBHQ= DBI->connect("DBI:mysql:new_mail:slavedb.i.routename.com","db_readuser","Tr33Wat3r");
   }
    my $sth=$DBHQ->prepare($qSel);
    $sth->execute(@$lrParams);
    return $sth;
}

sub get_send_cnt
{
	my ($input_file,$check_id,$ctype,$aid,$dupCnt)=@_;
	my $cnt;
	my ($clientid,$class,$attrib,$email,$eID,$state,$fname,$lname,$zip,$daycnt,$sdate,$url,$cdate,$ip,$rtime);
	my ($priority,$phone,$address,$address2,$dob,$times_in_db);
	my $city;
	my $gender;
	my $class_name;
	my $cstatus;
	my $old_email;
	my $ISPCNT;
	my $outfile;
	my $md5_suppression;
	my $vsID;
	my $eid;
	my $emcnt;
	my $qSel="select count(*) from email_list where client_id != ? and email_addr=?";

	if (($ctype eq "Export") or ($ctype eq "Export Suppression"))
	{
		$outfile=$FLATDIR."/".$hrProf->{check_id}.".tmp2";
		open(OUT,">$outfile");
		if ($aid > 0)
		{
			my $sql="select md5_suppression,vendor_supp_list_id from advertiser_info where advertiser_id=$aid";
			my $sth=$DBHU->prepare($sql);	
			$sth->execute();
			($md5_suppression,$vsID)=$sth->fetchrow_array();
			$sth->finish();
		}
		else
		{
			$md5_suppression="N";
			$vsID=0;
		}
	}
	$cnt=0;
	open(IN,"<$input_file") or return ; 
	RECORD: while (<IN>)
	{
		my $line=$_;
        ($clientid,$class,$attrib,$email,$cdate,$fname,$lname,$zip,$daycnt,$url,$ip,$gender,$class_name,$cstatus,$eid,$priority,$phone,$city,$state,$zip,$address,$address2,$dob,$times_in_db) = split('\|',$line);
		if ($old_email eq $email)
		{
			next RECORD;
		}
		$old_email=$email;
		if (($ctype eq "Export") or ($ctype eq "Export Suppression"))
		{
            if ($md5_suppression eq "Y")
            {
                my $params = {};
                $params->{'advID'} = $aid;
				if ($vsID > 0)
				{
                	$params->{'advID'} = $vsID;
				}
                $params->{'emailAddress'} = $email;
                $params->{'lastDeliveredDay'}=0;
                $params->{'groupSuppListID'}=0;
                my ($errors, $results) = $suppressionInterface->getGlobalMD5SuppressionRecord($params);
                if (($results->[0]->{'md5sum'}) and ($ctype eq "Export"))
                {
                    next RECORD;
                }
                if (($results->[0]->{'md5sum'}) and ($ctype eq "Export Suppression"))
                {
                }
				elsif ($ctype eq "Export Suppression")
				{
					next RECORD;
				}
            }
            elsif ($vsID > 1)
            {
                my $params = {};
                $params->{'emailAddress'} = $email;
                $params->{'listID'} = $vsID;
                $params->{'lastDeliveredDay'}=0;
                $params->{'groupSuppListID'}=0;
                my ($errors, $results) = $suppressionInterface->getGlobalVendorSuppressionRecord($params);
                if (($results->[0]->{'email_addr'}) and ($ctype eq "Export"))
                {
                    next RECORD;
                }
                if (($results->[0]->{'email_addr'}) and ($ctype eq "Export Suppression"))
                {
                }
				elsif ($ctype eq "Export Suppression")
				{
					next RECORD;
				}
            }
			if ($dupCnt > 0)
			{
				$sth=prep_and_exec1($qSel,[$clientid,$email]);
				($emcnt)=$sth->fetchrow_array();
				$sth->finish();
				if ($emcnt < $dupCnt)
				{
					next RECORD;
				}
			}
			$ISPCNT->{$clientid}{$class}++;
			print OUT "$line";
		}
		else
		{
			$ISPCNT->{$clientid}{$class}++;
		}
	}
	close(IN);
	if (($ctype eq "Export") or ($ctype eq "Export Suppression"))
	{
		close(OUT);
		system("mv $outfile $input_file");
	}
    foreach (keys %{$ISPCNT})
    {
		$clientid=$_;
    	foreach (keys %{$ISPCNT->{$clientid}})
    	{
			my $domain_id=$_;
        	$cnt=$ISPCNT->{$clientid}{$domain_id};
			$sql="update UniqueCheckIsp set reccnt=$cnt where check_id=$check_id and client_id=$clientid and class_id=$domain_id";
			log_or_execute($sql);
		}
	}
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
        $sql="delete from UniqueCheckCustom where check_id=$check_id";
        $rows=$DBHU->do($sql);
        $sql="delete from UniqueCheckSeed where check_id=$check_id";
        $rows=$DBHU->do($sql);
        $sql="delete from UniqueCheckUrl where check_id=$check_id";
        $rows=$DBHU->do($sql);
        $sql="delete from UniqueCheckZip where check_id=$check_id";
        $rows=$DBHU->do($sql);
	}
	$sth->finish();
}
sub getCustomProfileInfo
{
	my ($check_id,$client_id)=@_;
	my $oldKey=0;
	my $valstr="";
	my $eids="";

	my $sql="select clientRecordKeyID,clientRecordValueID from UniqueCheckCustom where check_id=? order by clientRecordKeyID,clientRecordValueID";
	my $sth1=prep_and_exec($sql,[$check_id]);
	while (($keyID,$valID)=$sth1->fetchrow_array())
	{
		if ($keyID != $oldKey)
		{
			if ($oldKey != 0)
			{
				chop($valstr);
				$eids=getEIDs($client_id,$oldKey,$valstr,$eids);
			}
			$valstr="";
			$oldKey=$keyID;
		}
		$valstr.=$valID.",";
	}
	$sth1->finish();
	if ($oldKey != 0)
	{
		chop($valstr);
		$eids=getEIDs($client_id,$oldKey,$valstr,$eids);
	}
	my @E=split(',',$eids);
	foreach my $eid (@E)
	{
		$CUSTOMEM->{$eid}=1;
	}
}

sub getEIDs
{
	my ($client_id,$key,$valstr,$eids)=@_;
	my $eid;
	my $tstr="";

	my $sql="select distinct emailUserID from ";
	if ($eids ne "")
	{
		$sql.=" (select distinct emailUserID from ClientRecordCustomData where clientID=$client_id and clientRecordKeyID = $key  and clientRecordValueID in ($valstr))as tempEID where emailUserID in ($eids)";
	}
	else
	{
		$sql.="ClientRecordCustomData where clientID=$client_id and clientRecordKeyID = $key  and clientRecordValueID in ($valstr)";
	}
	my $stheid=prep_and_exec1($sql);
	while (($eid)=$stheid->fetchrow_array())
	{
		$tstr.=$eid.",";
	}
	$stheid->finish();
	chop($tstr);
	return $tstr;
}

sub randomizeSplit
{
	my ($sortfile,$hrProf,$fsize)=@_;
    my ($clientid,$class,$attrib,$email,$cdate,$fname,$lname,$zip,$daycnt,$url,$ip,$gender);
	my ($phone,$address,$address2,$dob,$times_in_db);
	my $city;
	my $state;
	my $eid;
	my $class_name;
	my $cstatus;
	my $outstr;
	my $SDATE;
	my @SEEDS;
	my $em;
	my $seed_cnt;
	my $seed_interval;
	my $priority;
	my $SEEDEID;

	my $primarySortField=4;
	my $secondarySortField=8;
	my $outfile=$FLATDIR."/".$hrProf->{check_id}.".tmp2";

	if ($hrProf->{randomize_flag} eq "N")
	{
		$outfile=$sortfile;
	}
	else
	{
	   	my $primarySortCharacter    = int(rand() * 10) + 2;
    	my $secondarySortCharacter  = int(rand() * 5) + 2;
    	my $reverseSwitch   = ($secondarySortCharacter % 2 == 0) ? '' : '-r';

    	## randomize on a character in the email address, then on a character in the zip code
    	my $command         = "sort --key=$primarySortField.$primarySortCharacter --key=$secondarySortField.$secondarySortCharacter $reverseSwitch -t'|' -S 5M -T /dev/shm -T /var/www/util/data $sortfile -o $outfile";

    	log_notice("INFO: Randomizing using command: $command",0);
    	system($command);
	}
#   	my $command         = "sort --key=16,16n -t'|' -S 5M -T /dev/shm -T /var/www/util/data $sortfile -o $outfile";
#   	log_notice("INFO: Randomizing using command: $command",0);
#   	system($command);
	#
	# Get seeds
	$seed_cnt=0;
	$sql="select email_addr from UniqueCheckSeed where check_id=? order by checkSeedID";
	my $sth=prep_and_exec($sql,[$hrProf->{check_id}]);
	while (($em)=$sth->fetchrow_array())
	{
		$SEEDS[$seed_cnt]=$em;
		$seed_cnt++;
	}
	$sth->finish();

	my @FLD=split(',',$hrProf->{fieldsToExport});
	my $cnt=0;
	my $totalcnt=0;
	my $filecnt=1;
	open(IN,"<$outfile") or return ; 
	my $splitfile=$FLATDIR."/".$hrProf->{check_id}."_".$filecnt.".csv";
	open(OUT,">$splitfile"); 
	print OUT "$hrProf->{fieldsToExport}\n";
	RECORD: while (<IN>)
	{
		my $line=$_;
        ($clientid,$class,$attrib,$email,$cdate,$fname,$lname,$zip,$daycnt,$url,$ip,$gender,$class_name,$cstatus,$eid,$priority,$phone,$city,$state,$zip,$address,$address2,$dob,$times_in_db) = split('\|',$line);
		my $t1=$daycnt;
        $t1=~tr/A-Z/a-z/;
		$t1=~s/ //g;
		if ($t1 eq "")
		{
			next;
		}
        $_=$t1;
        if (/[a-z]/)
        {
            next; 
        }
		$cnt++;
		if (($cnt == 1) and ($filecnt == 1))
		{
			foreach my $s (@SEEDS)
			{
				# put seed into file
				$outstr="";
				foreach my $f (@FLD)
				{
					if ($f eq  "email_addr")
					{
						$outstr.=$s.",";
					}
					elsif ($f eq  "eid")
					{
						my $teid;
						if ($SEEDEID->{$s})
						{
							$teid=$SEEDEID->{$s};
						}
						else
						{
		        			$sql="select email_user_id from email_list where status='A' and email_addr=? limit 1"; 
                        	my $sth1=$DBHU->prepare($sql);
                        	$sth1->execute($s);
                        	if (($teid)=$sth1->fetchrow_array())
							{
								$SEEDEID->{$s}=$teid;
							}
							else
							{
								$teid=0;
							}
                        	$sth1->finish();
						}
						$outstr.="$teid,";
					}
					else
					{
						$outstr.=",";
					}
				}
				chop($outstr);
				print OUT "$outstr\n";
			}
		}
		$totalcnt++;
		if (($cnt > $hrProf->{volume_desired}) and ($hrProf->{volume_desired} > 0))
		{
			$cnt=1;
			$filecnt++;
			close(OUT);
			$splitfile=$FLATDIR."/".$hrProf->{check_id}."_".$filecnt.".csv";
			open(OUT,">$splitfile"); 
			print OUT "$hrProf->{fieldsToExport}\n";
			foreach my $s (@SEEDS)
			{
				# put seed into file
				$outstr="";
				foreach my $f (@FLD)
				{
					if ($f eq  "email_addr")
					{
						$outstr.=$s.",";
					}
					elsif ($f eq  "eid")
					{
						my $teid;
						if ($SEEDEID->{$s})
						{
							$teid=$SEEDEID->{$s};
						}
						else
						{
		        			$sql="select email_user_id from email_list where status='A' and email_addr=? limit 1"; 
                        	my $sth1=$DBHU->prepare($sql);
                        	$sth1->execute($s);
                        	if (($teid)=$sth1->fetchrow_array())
							{
								$SEEDEID->{$s}=$teid;
							}
							else
							{
								$teid=0;
							}
                        	$sth1->finish();
						}
						$outstr.="$teid,";
					}
					else
					{
						$outstr.=",";
					}
				}
				chop($outstr);
				print OUT "$outstr\n";
			}
		}
		$outstr="";
		foreach my $f (@FLD)
		{
			if ($f eq  "email_addr")
			{
				$outstr.=$email.",";
			}
			elsif ($f eq  "client_id")
			{
				$outstr.=$clientid.",";
			}
			elsif ($f eq  "eid")
			{
				$outstr.=$eid.",";
			}
			elsif ($f eq  "IP")
			{
				$outstr.=$ip.",";
			}
			elsif ($f eq  "first_name")
			{
				$outstr.=$fname.",";
			}
			elsif ($f eq  "last_name")
			{
				$outstr.=$lname.",";
			}
			elsif ($f eq  "url")
			{
				$outstr.=$url.",";
			}
			elsif ($f eq  "gender")
			{
				$outstr.=$gender.",";
			}
			elsif ($f eq  "Status")
			{
				$outstr.=$cstatus.",";
			}
			elsif ($f eq  "cdate")
			{
				$outstr.=$cdate.",";
			}
			elsif ($f eq  "sdate")
			{
				my $tdate;
				if ($SDATE->{$daycnt})
				{
					$tdate=$SDATE->{$daycnt};
				}
				else
				{
		        	$sql="select date_sub(curdate(),interval $daycnt day)";
                        my $sth=$DBHU->prepare($sql);
                        $sth->execute();
                        ($tdate)=$sth->fetchrow_array();
                        $sth->finish();
					$SDATE->{$daycnt}=$tdate;
				}
				$outstr.=$tdate.",";
			}
			elsif ($f eq  "ISP")
			{
				$outstr.=$class_name.",";
			}
			elsif ($f eq  "phone")
			{
				$outstr.=$phone.",";
			}
			elsif ($f eq  "address")
			{
				$outstr.=$address.",";
			}
			elsif ($f eq  "address2")
			{
				$outstr.=$address2.",";
			}
			elsif ($f eq  "dob")
			{
				$outstr.=$dob.",";
			}
			elsif ($f eq  "city")
			{
				$outstr.=$city.",";
			}
			elsif ($f eq  "state")
			{
				$outstr.=$state.",";
			}
			elsif ($f eq  "zip")
			{
				$outstr.=$zip.",";
			}
		}
		chop($outstr);
		print OUT "$outstr\n";
	}
	close(OUT);
	close(IN);
	if ($hrProf->{randomize_flag} ne "N")
	{
		unlink($outfile);
	}
	#
	# ftp files out
	#
	my $host = "ftp.aspiremail.com";
	my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
	if ($ftp)
	{
		if ($hrProf->{type} eq "Export Suppression")
		{
    		$ftp->login('espsuppression','rbbqF3EY') or print "Cannot login ", $ftp->message;
		}
		else
		{
    		$ftp->login('espdata','ch3frexA') or print "Cannot login ", $ftp->message;
		}
    	$ftp->ascii();
		my $cid=$hrProf->{check_id};
		my @files=`ls /var/www/util/data/${cid}_*.csv`;
		foreach my $f (@files)
		{
	        $f=~s///;
   	    	$f=~s/\n//;
    		$ftp->put($f) or print "put failed ", $ftp->message;
		}
    	$ftp->quit;
	}
}
