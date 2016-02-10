#!/usr/bin/perl
#===============================================================================
# Name   : bulk_ipgroup_show_info.cgi 
#
#--Change Control---------------------------------------------------------------
# 10/19/12  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $filename;
my $images = $util->get_images_url;

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $groupnames= $query->param('name');
my $export= $query->param('export');
if ($export eq "")
{
	$export=0;
}
if ($export ==1)
{
my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
	$month++;
	$year=$year+1900;
    $filename=$user_id."_ipgroup_".$month.$day.$year.$hr.$min.$sec.".csv";
    open(LOG,">/data3/3rdparty/$filename");
}

if ($export == 0)
{
util::header("IP Group Information");
	
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

end_of_html

print << "end_of_html" ;
        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR align=top bgColor=#509C10 height=18>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
					border=0 width="7" height="7"></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
					<tr><td align=center><form method=post action=bulk_ipgroup_show_info.cgi><input type=hidden name=name value="$groupnames"><input type=hidden name=export value=1><input type=submit value="Export Data"></form></td></tr>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15><FONT 
						face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>IP Group Information</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
                    src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
	<tr><th>IP Group</th><th>IP</th></tr>
end_of_html
}
else
{
	print LOG "IP Group,IP\n";
}
$groupnames =~ s/[\n\r\f\t]/\|/g ;
$groupnames =~ s/\|{2,999}/\|/g ;
my @grp_array = split '\|', $groupnames;
my $group;
my $ip;
foreach $group (@grp_array)
{
	$sql = "select ip_addr from IpGroup ig, IpGroupIps igi where ig.group_id=igi.group_id and group_name=? order by ip_addr";
	$sth = $dbhq->prepare($sql);
	$sth->execute($group);
	while (($ip) = $sth->fetchrow_array())
	{
		if ($export)
		{
			print LOG "$group,$ip\n";
		}
		else
		{
			print "<tr><td>$group</td><td>$ip</td></tr>";
		}
		
	}
	$sth->finish();
}
if (!$export)
{
print<< "end_of_html";
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TABLE>
				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
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
        <TD>
            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
			<td align="center">
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></TD>	
			<td align="center">
				&nbsp;&nbsp;&nbsp;<A HREF="/cgi-bin/get_ipgroup_info.cgi">Return to IP Group Search</a></td>
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

$util->footer();
}
else
{
	print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<body>
<center>
<h4><a href="/downloads/$filename">Click here</a> to download file</h4>
<br>
<A HREF="mainmenu.cgi"><IMG src="$images/home_blkline.gif" border=0></A>&nbsp;&nbsp;&nbsp;<A HREF="/cgi-bin/get_ipgroup_info.cgi">Return to IP Group Search</a>
</center>
<br>
</body>
</html>
end_of_html
}
$util->clean_up();
exit(0);
