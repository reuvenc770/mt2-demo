#!/usr/bin/perl

# *****************************************************************************************
# list_chunk_list.cgi
#
# this page displays the list of lists and lets the user edit / add
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
require "/var/www/html/newcgi-bin/modules/Common.pm";
# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $list_id;
my $list_name;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;
my $ip_addr;
my $server_name;
my $company;
my $member_cnt;
my $yahoo_cnt;
my $aol_cnt;
my $hotmail_cnt;
my $msn_cnt;
my $other_cnt;

my $args=Common::get_args();

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# print out the html page

util::header("CHUNK LISTS");

my $client_dropdown=client_drop($args,$dbhq);
print << "end_of_html";
    <script language="Javascript">
	function move_list(list_id)
	{
    	var newwin = window.open("/cgi-bin/move_chunk_list.cgi?list_id="+list_id, "Move", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width=500,height=300,left=25,top=200");
    	newwin.focus();
	}
    </script> 
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<tr bgcolor=#FFFFFF>
	  <td bgcolor=#FFFFFF colspan=10>
    <form method=post action="list_chunk_list.cgi">
    <table cellspacing=0 cellpadding=2 bgcolor=#FFFFFF width=100%>
      <tr>
        <td width=30%><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>CLIENT:</td>
        <td>$client_dropdown</td>
      </tr>
      <tr>
        <td width=30%><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Search by Domain:</td>
        <td><input type=text name="sch_domain" size=20 maxlength=45></td>
      </tr>
      <tr>
        <td colspan=2><input type=submit name="search" value="Filter It"></td>
      </tr>
    </table>
    </form>
	  </td>
	</tr>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=14>

		<TABLE cellSpacing=0 cellPadding=0 width=1000 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a list to edit or click Add to create a new list</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="14" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>List of Current Lists</b></font></TD>
		</TR>
		<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2><b>List Name</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2><b>Network</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2><b>Server</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2><b>IP Address</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="10%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>AOL Cnt</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="10%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Yahoo Cnt</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="10%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Other Cnt</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="10%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Hotmail Cnt</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="10%">
			<FONT face="verdana,arial,helvetica,sans serif" color="$table_text_color" size=2> 
			<b>Status</b></font></td>
		<TD bgcolor="#EBFAD1" align="left" width="08%">
			<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
			<b>&nbsp;</b></font></td>
		</TR> 
end_of_html

# read info about the lists

my $client_clause=qq^AND l.user_id=$args->{cID}^ if $args->{cID};
my $domain_clause=qq^AND list_name like '%$args->{sch_domain}%'^ if $args->{sch_domain} ne '';
$sql=qq|SELECT list_id,list_name,l.status,ip_addr,server,company,member_cnt,aol_cnt,hotmail_cnt,yahoo_cnt,msn_cnt FROM list l, server_config sc, user u WHERE |
	.qq|u.user_id=l.user_id AND l.server_id=sc.id AND list_type='CHUNK' AND l.status != 'D' $client_clause $domain_clause |
	.qq|ORDER BY company,list_name|;
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($list_id,$list_name,$status,$ip_addr,$server_name,$company,$member_cnt,$aol_cnt,$hotmail_cnt,$yahoo_cnt,$msn_cnt) = $sth->fetchrow_array())
{
	$other_cnt=$member_cnt - $aol_cnt - $hotmail_cnt - $yahoo_cnt - $msn_cnt;
	$hotmail_cnt = $hotmail_cnt + $msn_cnt;
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }

	if ($status eq "A")
	{
		$status_name = "Active";
	}
	elsif ($status eq "I")
	{
		$status_name = "Inactive";
	}
	elsif ($status eq "D")
	{
		$status_name = "Deleted";
	}

	print qq { <TR bgColor=$bgcolor> 
		<TD>&nbsp;</td> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			<A HREF="list_chunk_edit.cgi?list_id=$list_id&mode=EDIT">$list_name</a>
			</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$company</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$server_name</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$ip_addr</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$aol_cnt</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$yahoo_cnt</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$other_cnt</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$hotmail_cnt</font></TD> 
		<TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$status_name</font></TD> 
 		<TD><a href="#" onClick="move_list($list_id);">Move</a></TD> 
 		</TR> \n };
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
			<td width="50%" align="center">
				<a href="/list_chunk.html">
				<img src="$images/add.gif" border=0></a></td>
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

# exit function

$util->clean_up();

sub client_list {
	my ($dbh)=@_;

	my @clients=();
	my $q=qq|SELECT company, user_id FROM user WHERE status='A' AND company<>'' ORDER BY company ASC|;
	my $sth=$dbh->prepare($q);
	$sth->execute;
	while (my $hr=$sth->fetchrow_hashref) {
		push @clients, $hr;
	}
	$sth->finish;

	return \@clients;
}

sub client_drop {
	my ($args,$dbh)=@_;

	my $arClients=client_list($dbh);
	my $html=qq^
	<select name="cID">
	  <option value=0>ALL Clients
	^;
	foreach my $ref (@$arClients) {
#		my $selected=$args->{cID} eq "$ref->{user_id}" ? "SELECTED" : "";
		$html.=qq^<option value="$ref->{user_id}">$ref->{company}\n^;
	}
	$html.=qq^</select>^;
	return $html;
}

exit(0);
