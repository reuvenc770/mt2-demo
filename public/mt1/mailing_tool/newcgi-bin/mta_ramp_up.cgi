#!/usr/bin/perl

# *****************************************************************************************
# mta_ramp_up.cgi
#
# this page displays the ramp up values for a mta setting 
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
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $mta_id= $query->param('mta_id');
my $class_id = $query->param('class_id');

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# print out the html page

util::header("MTA Ramp Up Settings");

print << "end_of_html";
</TD>
</TR>
<script language="JavaScript">
function delete_rec(id)
{
	document.location.href="/cgi-bin/mta_delete_ramp_up.cgi?id="+id;
}
</script>
end_of_html
my $mname;
my $cname;
$sql="select name from mta where mta_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($mta_id);
($mname)=$sth->fetchrow_array();
$sth->finish();
$sql="select class_name from email_class where class_id=? and status='Active'";
$sth=$dbhu->prepare($sql);
$sth->execute($class_id);
($cname)=$sth->fetchrow_array();
$sth->finish();
print "<tr><td><b>MTA Setting: </b>$mname&nbsp;&nbsp;&nbsp;<b>ISP: </b>$cname</td></tr>\n";
print<<"end_of_html";
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=760 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=mta_add_ramp_up.cgi><input type=hidden name=mta_id value=$mta_id><input type=hidden name=class_id value=$class_id>Max # of Records per IP: <input type=text name=maxrecs size=10 maxlength=10>&nbsp;&nbsp;&nbsp;<input type=submit value="Add Day"></form>
		</td>
		</tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="50%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TH>Day</TH><th>Max per IP</th><th>Updated Max Per IP</th><th></th></tr>
end_of_html

# read info about the lists
my $id;
my $maxrecs;
$sql = "select id,max_records_per_ip from mta_ramp_up where mta_id=? and class_id=? order by id"; 
$sth = $dbhq->prepare($sql);
$sth->execute($mta_id,$class_id);
$reccnt=0;
while (($id,$maxrecs) = $sth->fetchrow_array())
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
	print qq { <form action="mta_edit_ramp_up.cgi"><input type=hidden name=id value=$id><TR bgColor=$bgcolor><TD align=center>$reccnt</td><td align=center>$maxrecs</td><td><input type=text name=umaxrecs value=$maxrecs size=10 maxlength=10><td><input type=submit value="Update">&nbsp;&nbsp;<input type=button value="Delete" onClick="delete_rec($id);"></td></tr></form> };
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

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
