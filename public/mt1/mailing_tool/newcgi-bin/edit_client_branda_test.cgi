#!/usr/bin/perl
#===============================================================================
# Purpose: Allows editing of client brand 
# File   : edit_client_brand.cgi
#
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $bid = $query->param('bid');
my $cid = $query->param('cid');
my $mode = $query->param('mode');
my $bname;
my ($sth, $reccnt, $sql, $dbh ) ;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $sth1;
my $uid;
my $aserver;
my $ip_addr;
my $url;
my $old_ip;
my $vid;
my $vname;
my $old_vid;
my $font_id;
my $color_id;
my $bg_color_id;
my $tid;
my $fid;
my $fname;
my $color_name;
my ($bname,$ourl,$yurl,$o_imageurl,$y_imageurl,$ons1,$ons2,$yns1,$yns2,$oip,$yip,$addr1,$addr2,$whois_email,$abuse_email,$personal_email,$dns_host, $clean_host, $others_host,$yahoo_host,$cns1,$cns2);
my $header_text;
my $footer_text;
my $notes;
my $aolw_flag;
my $aol_comments;
my $aolw_str;

# ------- connect to the util database ---------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

if ($mode eq "U")
{
	$sql = "select client_id,brand_name,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,footer_variation,footer_text,header_text,footer_font_id,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,notes,aolw_flag,aol_comments from client_brand_info where brand_id=$bid";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	if (($cid,$bname,$ons1,$ons2,$yns1,$yns2,$oip,$yip,$addr1,$addr2,$whois_email,$abuse_email,$personal_email,$dns_host, $clean_host, $others_host,$yahoo_host,$old_vid,$footer_text,$header_text,$font_id,$color_id,$bg_color_id,$cns1,$cns2,$notes,$aolw_flag,$aol_comments) = $sth->fetchrow_array())
	{
	}
	$sth->finish();
	$aolw_str="";
	if ($aolw_flag eq "Y")
	{
		$aolw_str="checked";
	}
}
else
{
	$aolw_str="";
	$bname="";
	$ons1 = "";
	$ons2 = "";
	$yns1 = "";
	$yns2 = "";
	$oip = "";
	$yip = "";
	$addr1 = "";
	$addr2 = "";
	$dns_host||='';
	$clean_host||='';
	$whois_email = "";
	$abuse_email = "";
	$personal_email = "";
	$old_vid = 0;
	$font_id = 0;
	$color_id = 0;
	$bg_color_id = 0;
	$footer_text = "";
	$notes="";
	$header_text = "";
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Contact Information</title>
<script language=JavaScript>
function add_url(type)
{
	var cpage = "/cgi-bin/add_url.cgi?type="+type+"&bid=$bid";
   	var newwin = window.open(cpage, "URL", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function edit_url(type)
{
	var url_value;
	if (type == 'O')
	{
		url_value = document.edit_client.ourl.value;
	}
	else if (type == 'Y')
	{
		url_value = document.edit_client.yurl.value;
	}
	else if (type == 'OI')
	{
		url_value = document.edit_client.o_imageurl.value;
	}
	else if (type == 'YI')
	{
		url_value = document.edit_client.y_imageurl.value;
	}
	else if (type == 'C')
	{
		url_value = document.edit_client.curl.value;
	}
	else if (type == 'CI')
	{
		url_value = document.edit_client.c_imageurl.value;
	}
	if (url_value != "")
	{
		var cpage = "/cgi-bin/edit_url.cgi?type="+type+"&uid="+url_value;
    	var newwin = window.open(cpage, "URL", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
	}
}
function del_url(type)
{
	var url_value;
	if (type == 'O')
	{
		url_value = document.edit_client.ourl.value;
	}
	else if (type == 'Y')
	{
		url_value = document.edit_client.yurl.value;
	}
	else if (type == 'OI')
	{
		url_value = document.edit_client.o_imageurl.value;
	}
	else if (type == 'YI')
	{
		url_value = document.edit_client.y_imageurl.value;
	}
	else if (type == 'C')
	{
		url_value = document.edit_client.curl.value;
	}
	else if (type == 'CI')
	{
		url_value = document.edit_client.c_imageurl.value;
	}
	if (url_value != "")
	{
        if (confirm("Are you sure you want to delete this url?"))
        {
		var cpage = "/cgi-bin/del_url.cgi?type="+type+"&uid="+url_value;
    	var newwin = window.open(cpage, "URL", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
		}
	}
}
function add_host(type)
{
	var cpage = "/cgi-bin/add_host.cgi?type="+type+"&bid=$bid";
   	var newwin = window.open(cpage, "HOST", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function edit_host(type)
{
	var url_value;
	if (type == 'O')
	{
		url_value = document.edit_client.o_host.value;
	}
	else if (type == 'Y')
	{
		url_value = document.edit_client.y_host.value;
	}
	else if (type == 'C')
	{
		url_value = document.edit_client.c_host.value;
	}
	else if (type == 'A')
	{
		url_value = document.edit_client.a_host.value;
	}
	else if (type == 'T')
	{
		url_value = document.edit_client.t_host.value;
	}
	else if (type == 'H')
	{
		url_value = document.edit_client.h_host.value;
	}
	if (url_value != "")
	{
		var cpage = "/cgi-bin/edit_host.cgi?type="+type+"&uid="+url_value;
    	var newwin = window.open(cpage, "HOST", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
	}
}
function del_host(type)
{
	var url_value;
	if (type == 'O')
	{
		url_value = document.edit_client.o_host.value;
	}
	else if (type == 'Y')
	{
		url_value = document.edit_client.y_host.value;
	}
	else if (type == 'C')
	{
		url_value = document.edit_client.c_host.value;
	}
	else if (type == 'A')
	{
		url_value = document.edit_client.a_host.value;
	}
	else if (type == 'T')
	{
		url_value = document.edit_client.t_host.value;
	}
	else if (type == 'H')
	{
		url_value = document.edit_client.h_host.value;
	}
	else if (type == 'L')
	{
		url_value = document.edit_client.ac_host.value;
	}
	if (url_value != "")
	{
        if (confirm("Are you sure you want to delete this host?"))
        {
		var cpage = "/cgi-bin/del_host.cgi?type="+type+"&uid="+url_value;
    	var newwin = window.open(cpage, "HOST", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
		}
	}
}
function addIP(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    edit_client.ip_addr.add(newOpt);
}

function update_ahost()
{
    var selObj = document.getElementById('a_host');
    var selIndex = selObj.selectedIndex;
    var selLength = edit_client.ip_addr.length;
    while (selLength>0)
    {
        edit_client.ip_addr.remove(selLength-1);
        selLength--;
    }
    edit_client.ip_addr.length=0;
    parent.frames[1].location="/newcgi-bin/upd_ip.cgi?ahost="+selObj.options[selIndex].value;
}
function preview_foot(fontID,txtcolorID,bgcolorID,vid) {
	window.open("/preview.php?font_id="+fontID+"&txtcolor="+txtcolorID+"&bgcolor="+bgcolorID+"&vid="+vid,'Preview','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500,left=25,top=50');
}
</script>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table1">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="719" border="0" id="table2">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">
				<img src="/mail-images/header.gif" border="0"></td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Edit 
						Contact Information</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table4">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table5">
					<tr>
						<td vAlign="center" align="left">
						<b>
						<font face="verdana,arial,helvetica,sans serif" color="#509C10">
						Brand Info</font></b></td>
					</tr>
					<tr>
						<td>
						<img height="3" src="/mail-images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td colSpan="10">
						&nbsp;</td>
					</tr>
				</table>
				<form name="edit_client" method="post" action="/cgi-bin/client_brand_upd.cgi">
				<input type=hidden name=bid value=$bid>
				<input type=hidden name=cid value=$cid>
					<table cellSpacing="0" cellPadding="0" width="100%" bgColor="#ffffff" border="0" id="table7">
						<tr>
							<td>
							<table cellSpacing="0" cellPadding="5" width="100%" border="0" id="table8">
								<tr>
									<td align="middle">
									<table cellSpacing="0" cellPadding="0" width="100%" bgColor="#e3fad1" border="0" id="table9">
										<tr align="top" bgColor="#509c10" height="18">
											<td vAlign="top" align="left" height="15" width="2%">
											<img height="7" src="/mail-images/blue_tl.gif" width="7" border="0"></td>
											<td height="15" width="1%">
											<img height="1" src="/mail-images/spacer.gif" width="3" border="0"></td>
											<td align="middle" height="15" width="95%">
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table10">
												<tr bgColor="#509c10" height="15">
													<td align="middle" width="100%" height="15">
													<b>
													<font face="Verdana,Arial,Helvetica,sans-serif" size="2" color="#FFFFFF">
													Brand Info</font></b></td>
												</tr>
											</table>
											</td>
											<td height="15" width="1%">
											<img height="1" src="/mail-images/spacer.gif" width="3" border="0"></td>
											<td vAlign="top" align="right" bgColor="#509c10" height="15" width="1%">
											<img height="7" src="/mail-images/blue_tr.gif" width="7" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td colSpan="5">
											<img height="3" src="/mail-images/spacer.gif" width="1" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td width="2%">
											<img height="3" src="/mail-images/spacer.gif" width="3"></td>
											<td align="middle" width="1%">
											<img height="3" src="/mail-images/spacer.gif" width="3"></td>
											<td align="middle" width="95%">
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table11">
												<tr>
													<!-- --------- account Type ----------------- -->
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Brand: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=text name=brandname value="$bname" maxlength=50>
													</font></td>
												</tr>
                                                <tr>
                                                    <td vAlign="center" noWrap align="right" width="9%">
                                                    <font size="1">
                                                    <input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
                                                    <td vAlign="center" noWrap align="right" width="31%">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Footer: </font></td>
                                                    <td vAlign="center" align="left">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=vid>
end_of_html
$sql = "select variation_id,name from footer_variation where status='A'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($vid,$vname) = $sth1->fetchrow_array())
{
	if ($vid == $old_vid)
	{
		print "<option selected value=$vid>$vname</option>";
	}
	else
	{
		print "<option value=$vid>$vname</option>";
	}	
}
$sth1->finish();
print<<"end_of_html";
</select></td></tr>
                                                <tr>
                                                    <td vAlign="center" noWrap align="right" width="9%">
                                                    <font size="1">
                                                    <input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
                                                    <td vAlign="center" noWrap align="right" width="31%">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Font: </font></td>
                                                    <td vAlign="center" align="left">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=font_id>
end_of_html
$sql = "select font_id,font_name from fonts order by font_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($fid,$fname) = $sth1->fetchrow_array())
{
	if ($fid == $font_id)
	{
		print "<option selected value=$fid>$fname</option>";
	}
	else
	{
		print "<option value=$fid>$fname</option>";
	}	
}
$sth1->finish();
print "</select></td></tr>";
print<<"end_of_html";
                                                <tr>
                                                    <td vAlign="center" noWrap align="right" width="9%">
                                                    <font size="1">
                                                    <input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
                                                    <td vAlign="center" noWrap align="right" width="31%">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Text Color: </font></td>
                                                    <td vAlign="center" align="left">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=color_id>
end_of_html
$sql = "select color_id,color_name from colors where color_type='F' order by color_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($tid,$color_name) = $sth1->fetchrow_array())
{
	if ($tid == $color_id)
	{
		print "<option selected value=$tid>$color_name</option>";
	}
	else
	{
		print "<option value=$tid>$color_name</option>";
	}	
}
$sth1->finish();
print "</select></td></tr>";
print<<"end_of_html";
                                                <tr>
                                                    <td vAlign="center" noWrap align="right" width="9%">
                                                    <font size="1">
                                                    <input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
                                                    <td vAlign="center" noWrap align="right" width="31%">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Background Color: </font></td>
                                                    <td vAlign="center" align="left">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=bg_color_id>
end_of_html
$sql = "select color_id,color_name from colors where color_type='B' order by color_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($tid,$color_name) = $sth1->fetchrow_array())
{
	if ($tid == $bg_color_id)
	{
		print "<option selected value=$tid>$color_name</option>";
	}
	else
	{
		print "<option value=$tid>$color_name</option>";
	}	
}
$sth1->finish();
print qq^</select><input type=button onClick="javascript:preview_foot(font_id.value,color_id.value,bg_color_id.value,vid.value);" value='Preview Footer'></td></tr>^;
if ($mode eq "U")
{
print<<"end_of_html";
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed5"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Others Mailing URL: </font></td>
													<td vAlign="center" align="left">
													<select name="ourl" size="1">
end_of_html
$sql="select url_id,url from brand_url_info where brand_id=$bid and url_type='O'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_url('O');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_url('O');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_url('O');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed6"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Yahoo Mailing URL: </font></td>
													<td vAlign="center" align="left">
													<select name="yurl" size="1">
end_of_html
$sql="select url_id,url from brand_url_info where brand_id=$bid and url_type='Y'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_url('Y');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_url('Y');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_url('Y');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed6"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Cleanser Mailing URL: </font></td>
													<td vAlign="center" align="left">
													<select name="curl" size="1">
end_of_html
$sql="select url_id,url from brand_url_info where brand_id=$bid and url_type='C'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_url('C');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_url('C');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_url('C');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed7"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Others Image URL: </font></td>
													<td vAlign="center" align="left">
													<select name="o_imageurl" size="1">
end_of_html
$sql="select url_id,url from brand_url_info where brand_id=$bid and url_type='OI'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_url('OI');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_url('OI');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_url('OI');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed8"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Yahoo Image URL: </font></td>
													<td vAlign="center" align="left">
													<select name="y_imageurl" size="1">
end_of_html
$sql="select url_id,url from brand_url_info where brand_id=$bid and url_type='YI'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_url('YI');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_url('YI');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_url('YI');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed6"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Cleanser Image URL: </font></td>
													<td vAlign="center" align="left">
													<select name="c_imageurl" size="1">
end_of_html
$sql="select url_id,url from brand_url_info where brand_id=$bid and url_type='CI'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_url('CI');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_url('CI');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_url('CI');"></td>
												</tr>
end_of_html
}
print<<"end_of_html";

												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed9"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Others</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													NS1: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="ons1" value="$ons1"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed10"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Others</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													NS2: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="ons2" value="$ons2"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed11"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Yahoo NS1: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="yns1" value="$yns1"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed12"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Yahoo </font>
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													NS2: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="yns2" value="$yns2"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed9"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Cleanser</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													NS1: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="cns1" value="$cns1"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed10"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Cleanser</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													NS2: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="cns2" value="$cns2"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed99"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													DNS Host:</font></td>
													<td vAlign="center" align="left">
													<select name="dns_host">
end_of_html
##  need to figure out which machine is actually doing the hosting - jp Thu Dec  1 12:29:59 EST 2005
my $hrServs={};
my $qSel=qq^SELECT id, server FROM server_config WHERE inService=1 ORDER BY server ASC^;
my $sthServ=$dbhq->prepare($qSel);
$sthServ->execute();
while (my ($servID, $server)=$sthServ->fetchrow_array) {
	$hrServs->{$servID}=$server;
}
$sthServ->finish;

foreach (sort {$hrServs->{$a} cmp $hrServs->{$b}} keys %$hrServs) {
	my $selected=($_ == $dns_host) ? 'SELECTED' : '';
	print qq^	<option value='$_' $selected>$hrServs->{$_}</option>\n^;
}
print<<"end_of_html";
													</select></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed98"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Cleanser DNS Host:</font></td>
													<td vAlign="center" align="left">
													<select name="clean_host">
end_of_html
foreach (sort {$hrServs->{$a} cmp $hrServs->{$b}} keys %$hrServs) {
	my $selected=($_ == $clean_host) ? 'SELECTED' : '';
	print qq^	<option value='$_' $selected>$hrServs->{$_}</option>\n^;
}
print<<"end_of_html";
													</select></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed13"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Others Host:</font></td>
													<td vAlign="center" align="left">
													<select name="o_host">
end_of_html
$sql="select brand_host_id,server_name from brand_host where brand_id=$bid and server_type='O' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('O');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('O');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('O');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Yahoo Host:</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													</font></td>
													<td vAlign="center" align="left">
													<select name="y_host">
end_of_html
$sql="select brand_host_id,server_name from brand_host where brand_id=$bid and server_type='Y' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('Y');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('Y');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('Y');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													AOL Host:</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													</font></td>
													<td vAlign="center" align="left">
													<select name="a_host">
end_of_html
$sql="select brand_host_id,server_name,ip_addr from brand_host where brand_id=$bid and server_type='A' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url,$old_ip) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url - $old_ip</option>\n";
}
$sth1->finish();
print<<"end_of_html";
</select>
				&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('A');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('A');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('A');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Test AOL Host(s):</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													</font></td>
													<td vAlign="center" align="left">
													<select name="t_host">
end_of_html
$sql="select brand_host_id,server_name,ip_addr from brand_host where brand_id=$bid and server_type='T' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url,$old_ip) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url - $old_ip</option>\n";
}
$sth1->finish();
print<<"end_of_html";
</select>
				&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('T');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('T');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('T');"></td>
												</tr>
												<tr>
													<td vAlign="top" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="top" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10"> AOL Whitelisted:</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"></font></td>
													<td vAlign="top" align="left"><input type=checkbox value="Y" $aolw_str name=aolw_flag></td></tr>
<tr><td vAlign="top" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="pixel_placed14"></font></td><td align=right><font fa
ce="verdana,arial,helvetica,sans serif" color="#509c10" size="2">AOL Comments: </font></td><td><textarea name=aol_comments rows=3 cols=50>$aol_comments</textarea></td></tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Hotmail Host:</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													</font></td>
													<td vAlign="center" align="left">
													<select name="h_host">
end_of_html
$sql="select brand_host_id,server_name from brand_host where brand_id=$bid and server_type='H' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('H');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('H');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('H');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Cleanser Host:</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													</font></td>
													<td vAlign="center" align="left">
													<select name="c_host">
end_of_html
$sql="select brand_host_id,server_name from brand_host where brand_id=$bid and server_type='C' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('C');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('C');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('C');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													AOL Cleanser Host:</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													</font></td>
													<td vAlign="center" align="left">
													<select name="ac_host">
end_of_html
$sql="select brand_host_id,server_name from brand_host where brand_id=$bid and server_type='L' order by server_name"; 
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_host('L');">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_host('L');">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_host('L');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed15"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Others</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													IP Range: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="oip" value="$oip"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed16"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Yahoo IP Range: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="yip" value="$yip"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed17"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Mailing Address 1: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="addr1" value="$addr1"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed18"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Mailing Address 2: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="addr2" value="$addr2"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed19"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Whois E-mail:</font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="whois_email" value="$whois_email"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed20"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													Abuse E-mail</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="abuse_email" value="$abuse_email"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Personal E-mail: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="personal_email" value="$personal_email"></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Header Text: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><textarea name=header_text cols=80 rows=3>$header_text</textarea></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Footer Text: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><textarea name="footer_text" cols=80 rows=3>$footer_text</textarea></font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Notes: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><textarea name="notes" cols=80 rows=5>$notes</textarea></font></td>
												</tr>
												<tr>
													<td align="middle">
													<img height="3" src="/mail-images/spacer.gif" width="3"></td>
													<td align="middle">
													&nbsp;</td>
												</tr>
											</table>
											</td>
											<td align="middle" width="1%">
											<img height="3" src="/mail-images/spacer.gif" width="3"></td>
											<td width="1%">
											<img height="3" src="/mail-images/spacer.gif" width="3"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td colSpan="5">
											<img height="3" src="/mail-images/spacer.gif" width="1" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1" height="10">
											<td vAlign="bottom" align="left" width="2%">
											<img height="7" src="/mail-images/lt_purp_bl.gif" width="7" border="0"></td>
											<td width="1%">
											<img height="3" src="/mail-images/spacer.gif" width="1" border="0"></td>
											<td align="middle" bgColor="#e3fad1" width="95%">
											<img height="3" src="/mail-images/spacer.gif" width="1" border="0">
											<img height="3" src="/mail-images/spacer.gif" width="1" border="0"></td>
											<td width="1%">
											<img height="3" src="/mail-images/spacer.gif" width="1" border="0"></td>
											<td vAlign="bottom" align="right" width="1%">
											<img height="7" src="/mail-images/lt_purp_br.gif" width="7" border="0"></td>
										</tr>
									</table>
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<table cellSpacing="0" cellPadding="7" width="100%" border="0" id="table12">
								<tr>
									<td align="middle" width="50%">
									<a href="/cgi-bin/mainmenu.cgi">
									<img src="/mail-images/home_blkline.gif" border="0"></a></td>
									<td align="middle" width="50%">
									<input type="image" src="/mail-images/save.gif" border="0" name="BtnAdd"> 
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
				</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td noWrap align="left" height="17"><br>
&nbsp;<p align="center">
		<img src="/images/footer.gif" border="0"></td>
	</tr>
</table>

</body>

</html>
end_of_html
