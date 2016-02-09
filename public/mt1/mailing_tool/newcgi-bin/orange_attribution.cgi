#!/usr/bin/perl

# *****************************************************************************************
# orange_attribution.cgi
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $pmesg=$query->param('pmesg');

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
my $setAttribution;
my $ctime;
$sql = "select username, setAttribution,now() from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $setAttribution,$ctime) = $sth->fetchrow_array();
$sth->finish();
if ($setAttribution eq "N")
{
	open(LOG2,">>/tmp/attribution.log");
    print LOG2 "$ctime - $username\n";
    close(LOG2);
    print "Content-type: text/html\n\n";
    print<<"end_of_html";
<html><head><title>Attribution Error</title></head>
<body>
<center><h3>You do not have permission to set Attribution.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
	exit(0);
}

# print out the html page

my $ctitle="Mailing Tool";

print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Email Tool</title>
end_of_html
if ($pmesg ne "")
{
	print qq^<script language=JavaScript>alert($pmesg);</script>^;
}
print<<"end_of_html";
<script language="JavaScript">
function Remove(gid)
{
    document.location.href="/cgi-bin/orange_attribution_del.cgi?sid="+gid;
    return true;
}
</script>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#FFFFFF'>
<TBODY>
        <tr>
        <td><form method=POST action=orange_attribution_add.cgi>
        <table cellSpacing=3>
        <tr><td>Client: </td><td><select name=cid>
end_of_html

my $cid;
my $cname;
$sql="select user_id,username from user where status='A' and OrangeClient='Y' and AttributeLevel=255 order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>&nbsp;&nbsp;Level:</td>
		<td><input type=text size=3 maxlength=3 name=level></td>
		<td><input type=submit value="Add"></td></tr></table>
		</form>
		</td>
		</tr>
<TR bgcolor='#FFFFFF'>
<TD align='center'>
	<table border="0" cellpadding="0" cellspacing="0" width="800" align='center' border=0>
	<tr>
	<TD width=248 bgcolor='#FFFFFF'>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgcolor='#FFFFFF'>&nbsp;</TD>
	</tr>
	<tr>
	<td colspan=3>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align='center'><b><font face="Arial" size="2" color='#FFFFFF'>&nbsp;Attribution Setup</FONT></b></td>
		</tr>
		<tr>
		<td>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</TD>
</TR>
        <tr><td>
<form method="post" action="upload_orange_attribution.cgi" encType=multipart/form-data>
Attribution File(<i>Format: level,clientID</i>): <input type=file name=upload_file>&nbsp;&nbsp;<input type=submit value=Load>
</form></td></tr>
<TR>
<TD vAlign=top align=left>
		<TABLE cellSpacing=0 cellPadding=3 width="70%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="5" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Attributions</b></font></TD>
		</TR>
		<tr><th>Client</th><th>Country</th><th>DataGroup</th><th>Attribute Level</th><th></th></tr>
end_of_html

# read info about the lists
my $cid;
my $fname;
my $level;
my $country;
my $dataGroup;
$sql = "select user_id,username,AttributeLevel,countryCode,company from user u join Country c on c.countryID=u.countryID where OrangeClient='Y' and status='A' and AttributeLevel!=255 order by AttributeLevel"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$fname,$level,$country,$dataGroup) = $sth->fetchrow_array())
{
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }
	print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$fname</td><td>$country</td><td>$dataGroup</td><td>$level</td><td align=right><input type=button value="Delete" onClick="return Remove($cid);"</td></TR> \n };
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=2><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=2><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=2>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
