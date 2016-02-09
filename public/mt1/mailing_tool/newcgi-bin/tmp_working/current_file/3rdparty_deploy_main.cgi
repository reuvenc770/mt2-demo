#!/usr/bin/perl
# include Perl Modules
use strict;
use CGI;

# get some objects to use later

my $query = CGI->new;
my $id=$query->param('id');
my $mode=$query->param('mode');
if ($id eq "")
{
	$id=0;
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Deploy Creative</TITLE>
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/3rdparty_deploy.cgi?id=$id&mode=$mode" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/blank.html" name="bottom" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
