use Sys::Hostname;
use IO::Socket;
my $host = hostname();
print "$host\n";
my($addr)=inet_ntoa((gethostbyname($host))[4]);
print "$addr\n";
