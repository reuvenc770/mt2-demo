#!/usr/bin/perl

use CGI;

print "Content-type: text/html\n\n";
print qq^
<html>
<body>^;
foreach (keys %ENV) {
print "$_ = $ENV{$_}<br>";
}
print qq^
</body></html>^;
exit;
