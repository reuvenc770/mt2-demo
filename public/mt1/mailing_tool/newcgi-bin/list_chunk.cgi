#!/usr/bin/perl
# *****************************************************************************************
# list_chunk.cgi
#
# this page is to add a chunk list 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $list_name;
my $ip_addr;
my $server_id;
my ($status, $optin_flag, $list_name, $double_mail_template, $thankyou_mail_template);
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;

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

util::header("Add CHUNK LIST");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function update_brand()
{
    var selObj = document.getElementById('company_id');
    var selIndex = selObj.selectedIndex;
    var selLength = listform.brand_id.length;
    while (selLength>0)
    {
        listform.brand_id.remove(selLength-1);
        selLength--;
    }
    listform.brand_id.length=0;
    parent.frames[1].location="/newcgi-bin/upd_chunk_brand.cgi?cid="+selObj.options[selIndex].value;
}
function addBRAND(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    listform.brand_id.add(newOpt);
}
function update_server()
{
    var selObj = document.getElementById('brand_id');
	if (selObj)
	{
    	var selIndex = selObj.selectedIndex;
	}
    var selLength = listform.server_id.length;
    while (selLength>0)
    {
        listform.server_id.remove(selLength-1);
        selLength--;
    }
    listform.server_id.length=0;
	if (selIndex >= 0)
	{
    	parent.frames[1].location="/newcgi-bin/upd_chunk_server.cgi?bid="+selObj.options[selIndex].value;
	}
	else
	{
		update_ip();
	}
}
function addSERVER(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    listform.server_id.add(newOpt);
}
function update_ip()
{
    var selObj_brand = document.getElementById('brand_id');
    if (selObj_brand)
    {
        var selIndex_brand = selObj_brand.selectedIndex;
    }
    var selObj = document.getElementById('server_id');
	if (selObj)
	{
    	var selIndex = selObj.selectedIndex;
	}
    var selLength = listform.ip_addr.length;
    while (selLength>0)
    {
        listform.ip_addr.remove(selLength-1);
        selLength--;
    }
    listform.ip_addr.length=0;
	if (selIndex >= 0)
	{
    	parent.frames[1].location="/newcgi-bin/upd_ip_addr.cgi?sid="+selObj.options[selIndex].value+"&bid="+selObj_brand.options[selIndex_brand].value;
	}
}
function addIP(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    listform.ip_addr.add(newOpt);
}
</script>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>$list_name</B></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Use this screen to add or edit email lists.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="list_chunk_ins.cgi" method="post" name=listform target=_top>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<!-- Begin main body area -->

			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
			<TBODY>
			<TR bgColor=#509C10>
			<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
				border=0 width="7" height="7"></TD>
			<TD align=middle height=15><FONT face="verdana,arial,helvetica,sans serif" 
				color=#ffffff size=2><B>List</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Company</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<select name=company_id onChange="update_brand();">
end_of_html
my $uid;
my $company;
	$sql="select user_id,company from user where status='A' order by company";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	while (($uid,$company) = $sth->fetchrow_array())
	{
		print "<option value=$uid>$company</option>\n";
	}
	$sth->finish();
print<<"end_of_html";
</select></td></tr>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><B>Brand</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<select name=brand_id onChange="update_server();">
</select></td></tr>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><B>Server</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=server_id onChange="update_ip();">
</select></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>IP Address(es)</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <select multiple name=ip_addr size=3></select></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=center width="50%">
					<INPUT TYPE=IMAGE src="$images/save.gif" border=0 
						width="81" height="22"></TD>
				<TD align=middle width="50%"> 
					<a href="list_chunk_list.cgi"><img src="$images/cancel.gif" border=0 target=_top></a></TD>
				</TR>
				</TBODY>
				</TABLE>

			</TD>
			<TD>&nbsp;</TD>
			</TR>
			<TR>
			<TD vAlign=bottom align=left colSpan=2><IMG height=7 src="$images/lt_purp_bl.gif" 
				width=7 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
				width=7 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

			<!-- End main body area -->

		</TD>
		</TR>
		<TR>
		<TD>&nbsp;</TD>
		</TR>
		<TR>
		<TD><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

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
exit(0);
