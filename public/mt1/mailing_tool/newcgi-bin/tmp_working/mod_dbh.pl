#!/usr/bin/perl -w

use strict;
use POSIX;
use vars qw($hrINIT);

	init();
	system('/bin/mkdir backup') unless -e 'backup';
	my @files=`ls *.cgi`;
	foreach my $file (@files) {
		chomp $file;
		log_msg("Start processing $file .....\n");
		unless ($hrINIT->{debug}) {
			my ($name,$ext)=split /\./, $file;
			my $bak=$name.'.bak';
			log_msg("Backing up $file --> $bak ....\n");
			system("/bin/cp $file $bak");
			open (BAK, "$bak") or die "Can't open backup file to read: $!\n";
			open (FILE, ">$file") or die "Can't open file to write: $!\n";
			my $flag=0;
			while (<BAK>) {
				chomp $_;
#				if ($_=~/(\$+\w+)->db_connect/) {
#					my $db_name=$1;
#					$db_name=~s/^\s//;
#					$db_name=~s/\s$//;
#					my $add='my ($dbhq,$dbhu)='."$db_name".'->get_dbh();';
#					$_.="\n\n".$add;
#					$flag=1;
#				}
				if ($_=~/(\$+\w+)->db_connect/) {
					$_='###'.$_;

##                if ($_=~/^\$dbh\s?=/) {
##                    $_='###'.$_;
##					$flag=1;
                }
				$_=~s/\$dbh->quote/\$dbhq->quote/g if $flag==1;
#				$_=~s/\$dbh->do/\$dbhu->do/g if $flag==1;
#				$_=~s/\$dbh->prepare/\$dbhq->prepare/g if $flag==1;
#				$_=~s/\$dbh->err\(/\$dbhu->err\(/g if $flag==1;
#				$_=~s/\$dbh->errstr\(/\$dbhu->errstr\(/g if $flag==1;
#				$_=~s/\$dbh->errmsg\(/\$dbhu->errmsg\(/g if $flag==1;
				print FILE "$_\n";
			}
			close FILE;
			close BAK;
			log_msg("Moing backup file to backup DIR...\n");
			system("/bin/mv $bak backup");
		}
	}
exit;

sub init {

	for (my $i=0; $i< @ARGV; $i++) {
		if ($ARGV[$i] eq '-v') {
			$hrINIT->{verbose}=1;
		}
		elsif ($ARGV[$i] eq '-d') {
			$hrINIT->{debug}=1;
		}
	}
}

sub log_msg {
        my $msg=shift;
        print "$msg\n" if $hrINIT->{debug} || $hrINIT->{verbose};
}
