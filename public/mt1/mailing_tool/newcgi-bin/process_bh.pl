#!/usr/bin/perl
use Net::FTP;
use Text::CSV;
$|=1;
my $cstatus;
my $datadir=$ENV{'DATA_DIRECTORY'} || "/var/local/programdata/recordProcessing/idebt";
my $ftpserver="23.92.22.64";
my $ftpusername="lvlcty1";
my $ftppassword="V9rXhKf6Xl*v";
my $cdir=$ARGV[0];

my @files;

my $ftp = Net::FTP->new($ftpserver, Timeout => 20, Debug => 0, Passive => 1) or die "Cannot connect to $server: $@";
$ftp->login($ftpusername,$ftppassword) or die "Cannot login ", $ftp->message;
print "$ftpusername $ftppassword\n";
$ftp->cwd('BH Reports');
$ftp->cwd($cdir);
$cprfile="";
$noncprfile="";
@files=$ftp->ls();
print "Files: $#files\n";
foreach my $f (@files)
{
	print "File: $f\n";
	$ftp->ascii();
	my $tf="/data1/bh/".$f;
	$_=$f;
	if (/CPR/)
	{
		$ftp->get($f,$tf);
		$cprfile=$f;
	}
	elsif (/full/)
	{
	}
	else
	{
		$ftp->get($f,$tf);
		$noncprfile=$f;
	}
}
$ftp->quit();
if (($cprfile eq "") or ($noncprfile eq ""))
{
	exit();
}
print " CPR: $cprfile\n";
my $csv = Text::CSV->new({
	binary => 1,
	auto_diag => 1,
	sep_char => ','
});
my $E;
my $tf="/data1/bh/".$cprfile;
open($data,'<',$tf);
while (my $fields = $csv->getline($data))
{
	my $code=$fields->[5];
	my $cnt=$fields->[7];
	$E->{$code}=$cnt;
	print "$code - $cnt\n";
}
close $data;
#
# open the other file
#
my $outputfile="/data1/bh/".$noncprfile;
$outputfile=~s/.csv/_full.csv/;
open(OUT,">$outputfile");
my $tf="/data1/bh/".$noncprfile;
open($data,'<',$tf);
while ($fields = $csv->getline($data))
{
	my $comp_cnt=0;
	my $code=$fields->[4];
	if ($E->{$code})
	{
		$comp_cnt=$E->{$code};
	}
	foreach my $f (@{$fields})
	{
		chomp($f);
		$f=~s///g;
		print OUT "$f,";
	}
	print OUT "$comp_cnt\n";
}
close $data;
close(OUT);
my $ftp = Net::FTP->new($ftpserver, Timeout => 20, Debug => 0, Passive => 1) or die "Cannot connect to $server: $@";
$ftp->login($ftpusername,$ftppassword) or die "Cannot login ", $ftp->message;
print "$ftpusername $ftppassword\n";
$ftp->cwd('BH Reports');
$ftp->cwd($cdir);
$ftp->delete($cprfile);
$ftp->delete($noncprfile);
$ftp->ascii();
$ftp->put($outputfile);
$ftp->quit();
