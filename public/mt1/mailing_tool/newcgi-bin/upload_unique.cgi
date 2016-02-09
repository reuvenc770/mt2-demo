#!/usr/bin/perl
#===============================================================================
# File   : upload_unique.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;


# ------- Get fields from html Form post -----------------


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $utype=$query->param('utype');
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Unique Upload </title></head>
<body>
<center>
<form method="post" action="upload_unique_save.cgi" encType=multipart/form-data accept-charset="UTF-8">
<input type=hidden name=utype value="$utype">
Unique File: <input type=file name=upload_file><br>
<input type=submit value=Load>
</form>
</center>
</body>
</html>
end_of_html
