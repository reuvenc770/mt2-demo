#!/usr/bin/perl5
# -*- perl -*-
# $Header: /home/johnl/hack/jmail/RCS/Qspam.pm,v 1.2 2001/05/18 16:35:02 johnl Exp $
#
# qspam_start(N, donefunc) - max number of concurrent deliveries,
#	callback when delivery done
#
#  callback is donefunc(mfile, code, resultflag, resultmsg)
#   mfile and code from qspam_send
#   resultflag is "y" for delivered, "n" for rejected, "" for queued
#   resultmsg is from SMTP session
#
# qspam_send(to, from, mfile [, code]) - send mfile, using to and from as
#	envelope addresses, optional code to identify message
#
# qspam_flush() - flush uncompleted messages 
#
#
 
package Qspam;
require Exporter;
@ISA = qw(Exporter);
@EXPORT = qw(qspam_start qspam_send qspam_flush);

#use strict;

my $codecounter;
my $debug;

my %dels;

my ($maxdels, $donefunc);

my $activedels;

sub qspam_start {
    $maxdels = shift || 20;
    $donefunc = shift;
    $debug = shift;

    $codecounter = $activedels = 0;
    %dels = ();
}

sub qspam_send {
    my ($to, $from, $mfile, $code) = @_;
    my $pid;
    my ($tohost) = ($to =~ /.*\@(.*)/);
 
    $code = ++$codecounter unless $code;

    $pid = fork();
    die "Fork $code failed" unless defined $pid;
    if($pid == 0) {
	open(STDIN, $mfile) or die "Cannot reopen $mfile";
	open(STDOUT, ">/home/tmp/qspam-$code") or die "Cannot create qspam-$code";
	exec "/var/qmail/bin/qmail-remote", $tohost, $from, $to;
	die "Cannot run qmail-remote";
    }
    $dels{$pid} = [ 'r', $to, $from, $mfile, $code ];
    $activedels++;
    
    downto($maxdels);
}

sub qspam_flush {
    downto(0);

}

sub downto {
    my ($max) = @_;

    while($activedels > $max) {
	my $pid = wait();
	my ($type, $to, $from, $mfile, $code);

	if($pid < 0) {
	    print "?? wait with no pids\n";
	    return;
	}
	my $del = $dels{$pid};

	if(!defined $del) {
	    print "?? mystery pid $pid\n";
	    next;
	}
	delete $dels{$pid};

	($type, $to, $from, $mfile, $code) = @$del;

	if($type eq 'r') {
	    my ($rbuf, $acode, $arpt, $rcode, $rrpt);

	    # check qmail-remote status, do queue if needed
	    open(RPT, "/home/tmp/qspam-$code") or die "Cannot open qspam-$code";
	    sysread RPT,$rbuf,4000;
	    close RPT;

	    while($rbuf =~ m/(.)([^\000]*)\000/sg) {
		$acode = $1; $arpt = $2;

		if($acode =~ m{[a-z]}) { # recipient code
		    $rcode = $acode;
		    $rrpt = $arpt;
		    next;
		}

		if($rcode eq "r" and $acode eq "K") { # it worked

		    $donefunc and &$donefunc($mfile, $code, "y", $arpt);
		    unlink "/home/tmp/qspam-$code";
		    $activedels--;

		} elsif($rcode eq "h" or $acode eq "D") { # it failed

		    $donefunc and &$donefunc($mfile, $code, "n", "$rrpt/$arpt");
		    unlink "/home/tmp/qspam-$code";
		    $activedels--;

		} else {		# didn't work, queue it

		    print "Queue $to $code $arpt\n" if $debug;
            $_ = $arpt;
            if (/571/)
            {
            	$donefunc and &$donefunc($mfile, $code, "n", "$rrpt/$arpt");
                unlink "/home/tmp/qspam-$code";
                $activedels--;
            }
            elsif (/452/)
            {
            	$arpt = "This account is over quota.";
                $donefunc and &$donefunc($mfile, $code, "n", "$rrpt/$arpt");
                unlink "/home/tmp/qspam-$code";
                $activedels--;
            }
            else
            {
		    open(CTL, ">/home/tmp/qspam-$code")
			or die "Cannot recreate qspam-$code";
		    print CTL "F$from\0T$to\0\0";
		    close CTL;
		    my $pid = fork();
		    die "Fork $code failed" unless defined $pid;
		    if($pid == 0) {
			open(STDIN, $mfile) or die "Cannot reopen $mfile";
			close(STDOUT);
			open(IN2, "/home/tmp/qspam-$code") 
			    or die "Cannot reopen qspam-$code";
			die "wrong fd " . fileno(IN2) if fileno(IN2) != 1;
			exec "/var/qmail/bin/qmail-queue";
			die "Cannot run qmail-queue";
		    }
		    $dels{$pid} = [ 'q', $to, $from, $mfile, $code ];
			}
		}
		last;
	    }
	} elsif($type eq 'q') {	# clean up after queueing

	    print "Queue fail $? for $code\n" if $?;
	    $donefunc and &$donefunc($mfile, $code, "", undef);
	    unlink "/home/tmp/qspam-$code";
	    $activedels--;

	} else {

	    die "strange type $type";

	}
    }
}

1;

