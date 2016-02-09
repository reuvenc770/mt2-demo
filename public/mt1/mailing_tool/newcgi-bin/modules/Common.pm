package Common;
use strict;
use DB;
use DBI;
use CGI qw(:all);
use URI::Escape;
use Carp qw(carp);
use vars qw($hrPARAM);
$hrPARAM->{debug}||=0;

$SIG{__WARN__}=sub {
        if ($_[0]!~m/Use of uninitialized value/) {
                carp "$$ - @_";
        }
};

sub get_args {
        my $q=new CGI;
        my $hrArgs;
        foreach my $req ($q->param) {
                my $value;
                my @lVals=$q->param($req);
                if ($lVals[1]) { $value=\@lVals; }
                else { $value=$lVals[0]; }
                $hrArgs->{$req}=$value;
        }
        return $hrArgs;
}

sub debug {
        my ($lvl)=@_;
        $lvl||=1;
        $hrPARAM->{debug}=$lvl;
}

sub log {
        my ($msg)=shift;
        for (my $i=1; $i < $hrPARAM->{debug}; $i++) {
                print "\t";
        }
        print "$msg\n" if $hrPARAM->{debug};
}
sub log_notice {
        my ($msg)=shift;
        my ($date, $time)=Date::get_current_datetime();
        open(LOG, ">>/home/etny/logs/error_log");
        print LOG "$date $time\t$msg\t$ENV{SCRIPT_NAME}\n";
        close LOG;
}

sub escape_url {
        my $url=shift;
        my $ret_val=uri_escape($url, "^A-Za-z");
        return $ret_val;
}

sub connect_db {

	my $dbh=DBI->connect('DBI:mysql:new_mail:updatep.routename.com','db_user','sp1r3V') or die "can't make DB connection: $!\n";
	unless ($dbh) {
		$dbh=connect_db();
	}
	return $dbh;
}
