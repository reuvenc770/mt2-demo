#!/usr/bin/perl

# *****************************************************************************************
# appendEmail_add.cgi
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use Net::FTP;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $bgcolor;
my $reccnt=0;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $ftpServer;
my $ftpUser;
my $ftpPassword;
my $pid=$query->param('pid');
if ($pid eq "")
{
	$pid=0;
}
# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}
my $username;
my $dataExportTool;
my $ctime;
my $BusinessUnit;
$sql = "select username, dataExportTool,now(),BusinessUnit from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $dataExportTool,$ctime,$BusinessUnit) = $sth->fetchrow_array();
$sth->finish();
if ($dataExportTool eq "N")
{
	open(LOG2,">>/tmp/export.log");
	print LOG2 "$ctime - $username\n";
	close(LOG2);
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Export Error</title></head>
<body>
<center><h3>You do not have permission to Export Data.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
		exit();
}

# print out the html page

util::header("Add Append Email");

print << "end_of_html";
</TD>
</TR>
</TBODY>
</TABLE>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<center>
		<form method=post name="campform" action=appendEmail_upd.cgi target=_top>
		<input type=hidden name=pid value=$pid>
		<TABLE cellSpacing=0 cellPadding=0 width=1200 bgColor=#ffffff border=0>
		<TBODY>
end_of_html
	print qq^<tr><td colspan=2>Filename:&nbsp;&nbsp;<select name=pname>^;
    my $host = "23.92.22.64";
	my $ftpuser="lvlcty6";
	my $ftppass="Xe8azepr";
    my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
    if ($ftp)
    {
        $ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
		$ftp->cwd("Incoming");
        my @remote_files = $ftp->ls();
        $ftp->quit;
		foreach my $file (@remote_files)
		{
			print qq^<option value="$file">$file</option>^;
		}
    }
	print qq^</select></td></tr>^;
print<<"end_of_html";
		<tr><td colspan=2>Output Filename(use {{date}} in name for unique date): <input type=text name=outname size=60 maxlength=255 value=""></td></tr>
<tr><td colspan=2 align=middle><input type=submit value="Add"></td></tr>
		<tr>
<td align="center" valign="top"><br>
                <a href="appendEmail_list.cgi" target=_top>
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
