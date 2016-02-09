#!/usr/bin/perl
#===============================================================================
# Purpose: Allows editing of client brand 
# File   : edit_client_brand.cgi
#
# Jim Sobeck	01/04/07	Added logic for newsletter brands
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
my $brand_cid;
my $mode = $query->param('mode');
my $bname;
my ($sth, $reccnt, $sql, $dbh ) ;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $sth1;
my $uid;
my $aserver;
my $num_domains_rotate;
my $i;
my $align;
my $font_type;
my $font_size;
my $ip_addr;
my $url;
my $old_ip;
my $vid;
my $vname;
my $old_vid;
my $old_nl_id;
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
my $brand_type;
my $client_type;
my $newsletter_font;
my $third_party_id;
my $unsub_img;
my $header_img;
my $footer_img;
my $privacy_img;
my $tag;
my ($phone, $bfname);
my $c_vsg;
my $uc_vsg;
my $exclude_subdomain;
my $template_id;
my $include_privacy;
my $exclude_thirdparty;
my $exclude_wiki;
my $ignore_mta_template_settings;
my $from_address;
my $subject;
my $display_name;
my $replace_domain;
my $enable_replace_yes;
my $enable_replace_no;
my $brand_priority;
my $disclaimer;
my $content;
my $purpose;
my $generateSpf;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

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
	$sql = "select client_id,brand_name, brand_fullname,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,
	others_ip,yahoo_ip,mailing_addr1,mailing_addr2, phone,whois_email,
	abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,
	footer_variation,footer_text,header_text,footer_font_id,footer_color_id,footer_bg_color_id,
	cleanser_ns1,cleanser_ns2,notes,aolw_flag,aol_comments,brand_type,third_party_id,align,font_type,
	font_size,unsub_img,privacy_img,newsletter_font,newsletter_header,newsletter_footer,nl_id,tag,c_vsgid,
	uc_vsgid,exclude_subdomain,creative_selection,exclude_thirdparty,template_id,include_privacy, from_address,
	display_name,exclude_wiki, replace_domain, brand_priority,num_domains_rotate,client_type,purpose,ignore_mta_template_settings,
	dc.disclaimer,dc.content,subject,generateSpf
	from client_brand_info cbi LEFT OUTER JOIN DoubleConfirmDisclaimer dc ON cbi.brand_id = dc.brandID where cbi.brand_id=$bid";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	if (($cid,$bname,$bfname, $ons1,$ons2,$yns1,$yns2,$oip,$yip,$addr1,$addr2,$phone,$whois_email,$abuse_email,$personal_email,$dns_host, $clean_host, $others_host,$yahoo_host,$old_vid,$footer_text,$header_text,$font_id,$color_id,$bg_color_id,$cns1,$cns2,$notes,$aolw_flag,$aol_comments,$brand_type,$third_party_id,$align,$font_type,$font_size,$unsub_img,$privacy_img,$newsletter_font,$header_img,$footer_img,$old_nl_id,$tag,$c_vsg,$uc_vsg,$exclude_subdomain,$brand_cid,$exclude_thirdparty,$template_id,$include_privacy,$from_address,$display_name,$exclude_wiki, $replace_domain, $brand_priority,$num_domains_rotate,$client_type,$purpose,$ignore_mta_template_settings, $disclaimer,$content, $subject,$generateSpf) = $sth->fetchrow_array())
	{
	}
	$sth->finish();
	$aolw_str="";
	if ($aolw_flag eq "Y")
	{
		$aolw_str="checked";
	}
	
	$enable_replace_yes = '';
	$enable_replace_no  = '';
	
	if ($replace_domain eq '1'){
		$enable_replace_yes = 'checked';
	}

	else{
		$enable_replace_no = 'checked';	
	}
	
}
else
{
	$aolw_str="";
	$bname="";
	$bfname="";
	$ons1 = "";
	$ons2 = "";
	$yns1 = "";
	$yns2 = "";
	$oip = "";
	$yip = "";
	$addr1 = "";
	$addr2 = "";
	$phone = "";
	$dns_host||='';
	$clean_host||='';
	$whois_email = "";
	$abuse_email = "";
	$personal_email = "";
	$exclude_subdomain="N";
	$template_id=1;
	$include_privacy="YES";
	$exclude_thirdparty="N";
	$exclude_wiki="N";
	$brand_cid=4;
	$old_vid = 0;
	$font_id = 0;
	$color_id = 0;
	$bg_color_id = 0;
	$footer_text = "";
	$notes="";
	$header_text = "";
	$brand_type="Internal";
	$newsletter_font="";
	$align="center";
	$font_type="Arial";
	$font_size=10;
	$unsub_img="";
	$header_img="";
	$footer_img="";
	$privacy_img="";
	$tag="";
	$c_vsg="";
	$uc_vsg="";
	$enable_replace_no  = 'checked';
	$purpose="Normal";
	$generateSpf="Y";
	
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Contact Information</title>
<script language=JavaScript>
function add_article()
{
	var cpage = "/cgi-bin/add_brand_article.cgi?bid=$bid";
   	var newwin = window.open(cpage, "Article", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function del_article()
{
	var url_value = document.edit_client.article.value;
	var cpage = "/cgi-bin/del_brand_article.cgi?bid=$bid&sid="+url_value;
   	var newwin = window.open(cpage, "Article", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function del_all_articles()
{
	var cpage = "/cgi-bin/del_brand_article.cgi?bid=$bid&sid=0";
   	var newwin = window.open(cpage, "Article", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function add_url(type)
{
    j=document.edit_client.updateall.length;
    for (i=0; i<j; i++)
    {
        if (document.edit_client.updateall[i].checked)
        {
            var upd=document.edit_client.updateall[i].value;
        }
    }
	var cpage = "/cgi-bin/add_url.cgi?type="+type+"&bid=$bid&upd="+upd;
   	var newwin = window.open(cpage, "URL", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function add_brand_url(type, action)
{   
	var cpage;
    j=document.edit_client.updateall.length;
    for (i=0; i<j; i++)
    {
        if (document.edit_client.updateall[i].checked)
        {
            var upd=document.edit_client.updateall[i].value;
        }
    }
	if (action=='e' || action=='d') {
		var url=document.edit_client.avail_urls.value;
		cpage = "/cgi-bin/add_brand_url.cgi?type="+type+"&bid=$bid&upd="+upd+"&act="+action+"&act_url="+url;
	}
	else {
    	cpage = "/cgi-bin/add_brand_url.cgi?type="+type+"&bid=$bid&upd="+upd;
	}
    var newwin = window.open(cpage, "URL", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
}

function chkform()
{
	if ((document.edit_client.from_address.value != "") && (document.edit_client.display_name.value == ""))
	{
		alert('If From Address specified then, Display Name cannot be blank');
		return false;
	}
	return true;
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
function test_url(type,ctype)
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
	else if (type == 'C')
	{
		url_value = document.edit_client.curl.value;
	}
	if (url_value != "")
	{
		if (ctype == 'D')
		{
			var cpage = "/cgi-bin/del_mon_tag1.cgi?&bid="+$bid+"&uid="+url_value+"&ctype="+ctype;
		}
		else
		{
			var cpage = "/cgi-bin/emailreach.cgi?&bid="+$bid+"&uid="+url_value+"&ctype="+ctype;
		}
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
function add_domain()
{
    j=document.edit_client.updateall.length;
    for (i=0; i<j; i++)
    {
        if (document.edit_client.updateall[i].checked)
        {
            var upd=document.edit_client.updateall[i].value;
        }
    }
	var cpage = "/cgi-bin/add_adv_domain.cgi?bid=$bid&upd="+upd;
   	var newwin = window.open(cpage, "DOMAIN", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function edit_domain()
{
	var url_value;
	url_value = document.edit_client.adv_domain.value;
	if (url_value != "")
	{
		var cpage = "/cgi-bin/edit_adv_domain.cgi?bid=$bid&aid="+url_value;
    	var newwin = window.open(cpage, "DOMAIN", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
	}
}
function del_domain()
{
	var url_value;
	url_value = document.edit_client.adv_domain.value;
	if (url_value != "")
	{
		var cpage = "/cgi-bin/del_adv_domain.cgi?bid=$bid&aid="+url_value;
    	var newwin = window.open(cpage, "DOMAIN", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
	}
}
function add_host(type)
{
    j=document.edit_client.updateall.length;
    for (i=0; i<j; i++)
    {
        if (document.edit_client.updateall[i].checked)
        {
            var upd=document.edit_client.updateall[i].value;
        }
    }
	var cpage = "/cgi-bin/add_host.cgi?type="+type+"&bid=$bid&upd="+upd;
   	var newwin = window.open(cpage, "HOST", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
}
function add_host1(type)
{
	var cpage = "/cgi-bin/add_host_main.cgi?type="+type+"&bid=$bid";
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
function check_clients(dtype)
{
	var cpage = "/cgi-bin/check_clients.cgi?bid=$bid&dtype="+dtype;
   	var newwin = window.open(cpage, "Clients", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50"); 
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
					<tr><td align="middle"><a href="/cgi-bin/emailreach.cgi?bid=$bid" target=_blank>EmailReach - Test all mailing domains</a></td></tr>
					<tr><td align="middle"><a href="/cgi-bin/del_mon_tag2.cgi?bid=$bid&ctype=D" target=_blank>Delivery Monitor - Test all mailing domains</a></td></tr>
					<tr><td align="middle"><a href="/cgi-bin/emailreach.cgi?bid=$bid&ctype=C" target=_blank>Compliance Check - Test all mailing domains</a></td></tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td colSpan="10">
						&nbsp;</td>
					</tr>
				</table>
				<form name="edit_client" onSubmit="return chkform();" method="post" action="/cgi-bin/client_brand_upd.cgi" target=_top encType="multipart/form-data">
				<input type=hidden name=bid value=$bid>
				<input type=hidden name=cid value=$cid>
end_of_html
				if ($brand_type ne "Newsletter")
				{
					print "<input type=hidden name=updateall value=N>\n";
				}
print<<"end_of_html";
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
end_of_html
if ($brand_type eq "Newsletter")
{
print<<"end_of_html";
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
<td colspan=2 vAlign="center" noWrap align="middle"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Update All Brands:&nbsp;&nbsp;<input type=radio id=updateall name=updateall value="Y" checked>Yes&nbsp;&nbsp;<input type=radio id=updateall name=updateall value="N">No</font></td></tr>
end_of_html
}
print<<"end_of_html";
												<tr>
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
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Brand Pretty Name: </font></td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=text name=brandfname value="$bfname" maxlength=50>
													</font></td>
												</tr>
                                                <tr>
                                                    <td vAlign="center" noWrap align="right" width="9%">
                                                    <font size="1">
                                                    <input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td>
                                                    <td vAlign="center" noWrap align="right" width="31%">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Type: </font></td>
                                                    <td vAlign="center" align="left">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=brand_type>
end_of_html
	if ($brand_type eq "Internal") 
	{
		print "<option selected value=\"Internal\">Internal</option>";
	}
	else
	{
		print "<option value=\"Internal\">Internal</option>";
	}	
	if ($brand_type eq "Chunking") 
	{
		print "<option selected value=\"Chunking\">Chunking</option>";
	}
	else
	{
		print "<option value=\"Chunking\">Chunking</option>";
	}	
	if ($brand_type eq "3rd Party") 
	{
		print "<option selected value=\"3rd Party\">3rd Party</option>";
	}
	else
	{
		print "<option value=\"3rd Party\">3rd Party</option>";
	}	
	if ($brand_type eq "Newsletter") 
	{
		print "<option selected value=\"Newsletter\">Newsletter</option>";
	}
	else
	{
		print "<option value=\"Newsletter\">Newsletter</option>";
	}	
print<<"end_of_html";
</select></td></tr>
                                                <tr>
                                                    <td vAlign="center" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="pixel_placed4" style="float: right"></font></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Third Party: </font></td>
                                                    <td vAlign="center" align="left">
                                                    <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=third_party_id>
<option select value=0>None</option> 
end_of_html
$sql = "select third_party_id,mailer_name from third_party_defaults where status='A'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($vid,$vname) = $sth1->fetchrow_array())
{
	if ($vid == $third_party_id)
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
end_of_html
if ($third_party_id==10 || $mode eq 'A') {
	print qq^
	<tr>
		<td vAlign="center" noWrap align="right" width="9%"><font size="1"></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Tag: </font></td>
		<td vAlign="center" align="left">
			<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=tag>
	^;
	for (1..2) {
		my $selected="";
		if ($tag>0 && $_==2) { $selected="SELECTED"; }
		print qq^<option value="$_" $selected>$_\n^;
	}
	print qq^</select>&nbsp;^;
	if ($mode eq 'U') {
		print qq^ To: <select name="upd_tag"><option value="0">Brands^;
		my $qB=qq|SELECT brand_name, brand_id FROM client_brand_info WHERE third_party_id=10 AND client_id=$cid AND status='A'|;
		my $sB=$dbhq->prepare($qB);
		$sB->execute;
		while (my ($bName,$bID)=$sB->fetchrow) {
			my $selected=$tag==$bID ? "SELECTED" : "" if $tag>0;
			print qq^<option value="$bID" $selected>$bName\n^;
		}
		$sB->finish;
		print qq^</select>^;
	}
	print qq^<td></tr>^;
}
if ($mode eq "A")
{
print<<"end_of_html";
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input style="FLOAT: right" type="checkbox" value="Y" name="pixel_placed104"></font></td>
<td vAlign="center" noWrap align="right" width="31%">
<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">Client Type to Mail to</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">:
<td vAlign="center" align="left">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name="client_type">
	<option selected value='ALL'>ALL</option>
end_of_html
$sql="select distinct client_type from user order by client_type"; 
my $s1=$dbhq->prepare($sql);
$s1->execute();
while (($client_type)=$s1->fetchrow_array())
{
	print "<option value='$client_type'>$client_type</option>\n";
}
$s1->finish();
print<<"end_of_html";
</select>&nbsp;&nbsp;<i>Note: Only applies to Newsletter Brands</i>
</tr>
end_of_html
}
if ($brand_type ne "Newsletter")
{
print<<"end_of_html";
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
}
else
{
print<<"end_of_html";
</select></td></tr>
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input style="FLOAT: right" type="checkbox" value="Y" name="pixel_placed104"></font></td>
<td vAlign="center" noWrap align="right" width="31%">
<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
Newsletter </font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">: 
<td vAlign="center" align="left">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name=nl_id>
end_of_html
$sql="select nl_id,nl_name from newsletter where nl_status='A' order by nl_name";
my $s1=$dbhq->prepare($sql);
$s1->execute();
my $nl_id;
my $nl_name;
while (($nl_id,$nl_name)=$s1->fetchrow_array())
{
	if ($nl_id == $old_nl_id)
	{
		print "<option value=$nl_id selected>$nl_name</option>\n";
	}
	else
	{
		print "<option value=$nl_id>$nl_name</option>\n";
	}
}
$s1->finish(); 
print<<"end_of_html";
</select>
</tr>
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input style="FLOAT: right" type="checkbox" value="Y" name="pixel_placed104"></font></td>
<td vAlign="center" noWrap align="right" width="31%">
<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">Client Type to Mail to</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">:
<td vAlign="center" align="left">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">$client_type</font>&nbsp;<input type="button" value="Check for new clients of this type" onClick="check_clients('Y');">&nbsp;&nbsp;<input type="button" value="Add other clients" onClick="check_clients('N');">
</tr>
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input style="FLOAT: right" type="checkbox" value="Y" name="pixel_placed104"></font></td>
<td vAlign="center" noWrap align="right" width="31%">
<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
Newsletter Header</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">: 
<td vAlign="center" align="left">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<input type=file name="newsletter_header">
end_of_html
if ($header_img ne "")
{
    print "&nbsp;&nbsp;<a href=\"http://www.affiliateimages.com/f/$header_img\" target=_new>View Header Image</a>\n";
}
print<<"end_of_html";
</tr>
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input style="FLOAT: right" type="checkbox" value="Y" name="pixel_placed104"></font></td>
<td vAlign="center" noWrap align="right" width="31%">
<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
Newsletter Footer</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">: 
<td vAlign="center" align="left">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<input type=file name="newsletter_footer">
end_of_html
if ($footer_img ne "")
{
    print "&nbsp;&nbsp;<a href=\"http://www.affiliateimages.com/f/$footer_img\" target=_new>View Footer Image</a>\n";
}
print<<"end_of_html";
</tr>
<tr>
<td vAlign="center" noWrap align="right" width="9%">
<font size="1">
<input style="FLOAT: right" type="checkbox" value="Y" name="pixel_placed105"></font></td>
<td vAlign="center" noWrap align="right" width="31%">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
Newsletter Default Font: </font></td>
<td vAlign="center" align="left">
<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name="newsletter_font">
end_of_html
if ($newsletter_font eq "Georgia")
{
	print "<option value=\"Georgia\" selected>Georgia</option>";
}
else
{
	print "<option value=\"Georgia\">Georgia</option>";
}
if ($newsletter_font eq "Times New Roman")
{
	print "<option value=\"Times New Roman\" selected>Times New Roman</option>";
}
else
{
	print "<option value=\"Times New Roman\">Times New Roman</option>";
}
if ($newsletter_font eq "Arial")
{
	print "<option value=\"Arial\" selected>Arial</option>";
}
else
{
	print "<option value=\"Arial\">Arial</option>";
}
if ($newsletter_font eq "Verdana")
{
	print "<option value=\"Verdana\" selected>Verdana</option>";
}
else
{
	print "<option value=\"Verdana\">Verdana</option>";
}
print<<"end_of_html";
					</select> </font></td>
</tr>
end_of_html
}
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
end_of_html
if ($brand_type eq "Newsletter")
{
	print "Confirmed Mailing URL: </font></td>";
}
else
{
	print "Others Mailing URL: </font></td>";
}
print<<"end_of_html";
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
									</select>&nbsp;&nbsp;<input type="button" value="Test" name="B22" onClick="test_url('O','E');">&nbsp;&nbsp;<input type="button" value="Test Delivery Monitor" name="B22" onClick="test_url('O','D');">&nbsp;&nbsp;<input type
="button" value="Compliance Check" name="B22" onClick="test_url('O','C');"></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed6"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
end_of_html
if ($brand_type eq "Newsletter")
{
	print "Unconfirmed Mailing URL: </font></td>";
}
else
{
	print "Yahoo Mailing URL: </font></td>";
}
print<<"end_of_html";
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
									</select>&nbsp;&nbsp;<input type="button" value="Test" name="B22" onClick="test_url('Y','E');">&nbsp;&nbsp;<input type="button" value="Test Delivery Monitor" name="B22" onClick="test_url('Y','D');">&nbsp;&nbsp;<input type="button" value="Compliance Check" name="B22" onClick="test_url('Y','C');"></td>
												</tr>
end_of_html
## Available URL(s) for brand that has exclude_subdomain='Y' ##
####
if ($exclude_subdomain eq 'Y') {
print qq^
	<tr>
		<td valign=center noWrap align="right" width="9%"></td>
		<td vAlign="center" noWrap align="right" width="31%">
		  <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Future Mailing Domains: </font></td>
		<td valign=center align=left>
		  <select name="avail_urls" size=1>
^;
my $qURL=qq|SELECT id,domain,rank FROM brand_available_domains WHERE brandID=$bid AND type='O' ORDER BY rank ASC|;
my $sURL=$dbhq->prepare($qURL);
$sURL->execute;
while (my $hr=$sURL->fetchrow_hashref) {
	print qq^<option value="$hr->{id}">$hr->{domain}\n^;
}
$sURL->finish;
print qq^
	</select>
^;
}
		
print<<"end_of_html";
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
									</select>&nbsp;&nbsp;<input type="button" value="Test" name="B22" onClick="test_url('C','E');">&nbsp;&nbsp;<input type="button" value="Test Delivery Monitor" name="B22" onClick="test_url('C','D');">&nbsp;&nbsp;<input type="button" value="Compliance Check" name="B22" onClick="test_url('C','C');"></td>
											</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed7"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
end_of_html
if ($brand_type eq "Newsletter")
{
	print "Confirmed Image URL: </font></td>";
}
else
{
	print "Others Image URL: </font></td>";
}
print<<"end_of_html";
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
									</select></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed8"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
end_of_html
if ($brand_type eq "Newsletter")
{
	print "Unconfirmed Image URL: </font></td>";
}
else
{
	print "Yahoo Image URL: </font></td>";
}
print<<"end_of_html";
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
									</select></td>
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
									</select></td>
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
end_of_html
if ($brand_type eq "Newsletter")
{
print<<"end_of_html";
													Confirmed VSG:</font></td>
													<td vAlign="center" align="left">
													<select name="c_vsg">
end_of_html
#my $ip;
#$sql="select sic.ip from server_ip_config sic, brand_ip bi where bi.brandID=$bid and bi.ip=sic.ip order by sic.ip";
#my $STH=$dbhq->prepare($sql);
#$STH->execute();
#$i=0;
#while (($ip)=$STH->fetchrow_array())
#{
#if ($i == 0)
#{
#if ($c_vsg eq $ip)
#{
#	print "<option value=\"$ip\" selected>$ip-[ALL IPs]</option>\n";
#}
#else
#{
#	print "<option value=\"$ip\">$ip-[ALL IPs]</option>\n";
#}
#}
#$i++;
#	my $temp_str=$ip."-".$ip;
#if ($c_vsg eq $temp_str)
#{
#	print "<option value=\"$ip-$ip\" selected>$ip-$ip</option>\n";
#}
#else
#{
#	print "<option value=\"$ip-$ip\">$ip-$ip</option>\n";
#}
#}
#$STH->finish();
print<<"end_of_html";
</select></td>
end_of_html
}
else
{
print<<"end_of_html";
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
				</select></td>
end_of_html
}
print<<"end_of_html";
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
end_of_html
if ($brand_type eq "Newsletter")
{
print<<"end_of_html";
													Unconfirmed VSG:</font></td>
													<td vAlign="center" align="left">
													<select name="uc_vsg">
end_of_html
#my $ip;
#$sql="select sic.ip from server_ip_config sic,brand_ip bi where bi.brandID=$bid and bi.ip=sic.ip order by sic.ip";
#my $STH=$dbhq->prepare($sql);
#$STH->execute();
#$i=0;
#while (($ip)=$STH->fetchrow_array())
#{
#if ($i == 0)
#{
#if ($uc_vsg eq $ip)
#{
#	print "<option value=\"$ip\" selected>$ip-[ALL IPs]</option>\n";
#}
#else
#{
#	print "<option value=\"$ip\">$ip-[ALL IPs]</option>\n";
#}
#}
#$i++;
#	my $temp_str=$ip."-".$ip;
#if ($uc_vsg eq $temp_str)
#{
#	print "<option value=\"$ip-$ip\" selected>$ip-$ip</option>\n";
#}
#else
#{
#	print "<option value=\"$ip-$ip\">$ip-$ip</option>\n";
#}
#}
#$STH->finish();
print<<"end_of_html";
</select></td>
end_of_html
}
else
{
print<<"end_of_html";
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
				</select></td>
end_of_html
}
print<<"end_of_html";
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
</select></td>
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
</select></td>
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
											</td></tr>
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
												</td></tr>
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
												</td></tr>
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
													<input type="checkbox" value="Y" name="pixel_placed18"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Phone: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<input maxLength="255" size="40" name="phone" value="$phone"></font></td>
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
												<tr><td vAlign="center" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="pixel_placed21"></font></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Unsub Footer Image: </font></td><td vAlign="center" align="left"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=file name="unsub_img"></font>
end_of_html
if ($unsub_img ne "")
{
	print "&nbsp;&nbsp;<a href=\"http://www.affiliateimages.com/f/$unsub_img\" target=_new>View Unsub Image</a>\n";
}
print<<"end_of_html";
</td></tr>
												<tr><td vAlign="center" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="pixel_placed21"></font></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Privacy Footer Image: </font></td><td vAlign="center" align="left"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=file name="privacy_img"></font>
end_of_html
if ($privacy_img ne "")
{
	print "&nbsp;&nbsp;<a href=\"http://www.affiliateimages.com/f/$privacy_img\" target=_new>View Privacy Image</a>\n";
}
print<<"end_of_html";
</td></tr>
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
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Font: </td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=font_type>
end_of_html
$sql = "select font_name from footer_font order by font_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($fname) = $sth1->fetchrow_array())
{
	if ($fname eq $font_type)
	{
		print "<option selected value=$fname>$fname</option>";
	}
	else
	{
		print "<option value=$fname>$fname</option>";
	}	
}
$sth1->finish();
print<<"end_of_html";
</select>
&nbsp;&nbsp;Size: <select name=font_size>
end_of_html
if ($font_size == 10)
{
	print "<option value=10 selected>10</option>";
}
else
{
	print "<option value=10>10</option>";
}
if ($font_size == 11)
{
	print "<option value=11 selected>11</option>";
}
else
{
	print "<option value=11>11</option>";
}
if ($font_size == 12)
{
	print "<option value=12 selected>12</option>";
}
else
{
	print "<option value=12>12</option>";
}
print<<"end_of_html";
</select>
</font>
</td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													Alignment: </td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><select name=align>
end_of_html
if ($align eq "center")
{
	print "<option value=center selected>Center</option>";
}
else
{
	print "<option value=center>Center</option>";
}
if ($align eq "left")
{
	print "<option value=left selected>Left Align</option>";
}
else
{
	print "<option value=left>Left Align</option>";
}
if ($align eq "right")
{
	print "<option value=right selected>Right Align</option>";
}
else
{
	print "<option value=right>Right Align</option>";
}
print<<"end_of_html";
</select></font></td>
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
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Creative Selection: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name=creative_selection>
end_of_html
if ($brand_cid == 0)
{
	print "<option value=0 selected>Based on Profile</option>\n";
}
else
{
	print "<option value=0>Based on Profile</option>\n";
}
my $sth9;
my $cid;
my $cname;
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth9 = $dbhq->prepare($sql);
$sth9->execute();
while (($cid,$cname) = $sth9->fetchrow_array())
{
	if ($cid == $brand_cid)
	{
		print "<option selected value=$cid>$cname</option>\n";
	}
	else
	{
		print "<option value=$cid>$cname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Exclude Subdomain: </font>
													</td>
													<td vAlign="center" align="left">
end_of_html
if ($exclude_subdomain eq "Y")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio checked value='Y' name="exclude_subdomain">Yes&nbsp;&nbsp;<input type=radio value='N' name="exclude_subdomain">No</font></td>
end_of_html
}
else
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Y' name="exclude_subdomain">Yes&nbsp;&nbsp;<input type=radio checked value='N' name="exclude_subdomain">No</font></td>
end_of_html
}
print<<"end_of_html";
												</tr>
												<tr>
													<td align="middle">
													<img height="3" src="/mail-images/spacer.gif" width="3"></td>
													<td align="middle">
													&nbsp;</td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Exclude From Thirdparty: </font>
													</td>
													<td vAlign="center" align="left">
end_of_html
if ($exclude_thirdparty eq "Y")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio checked value='Y' name="exclude_thirdparty">Yes&nbsp;&nbsp;<input type=radio value='N' name="exclude_thirdparty">No</font></td>
end_of_html
}
else
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Y' name="exclude_thirdparty">Yes&nbsp;&nbsp;<input type=radio checked value='N' name="exclude_thirdparty">No</font></td>
end_of_html
}
print<<"end_of_html";
												</tr>

												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Mailing Template: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name=template_id multiple="multiple" size=5>
end_of_html
my $tid;
my $tname;
$sql="select template_id,template_name from brand_template where status='A' order by template_name"; 
$sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tid,$tname) = $sth9->fetchrow_array())
{
	$sql="select count(*) from brand_template_join where brand_id=? and template_id=?";
	my $sth2d=$dbhq->prepare($sql);
	$sth2d->execute($bid,$tid); 
	($cnt)=$sth2d->fetchrow_array();
	$sth2d->finish();
	if ($cnt > 0)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</font></td>
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Include Privacy: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name=include_privacy>
end_of_html
if ($include_privacy eq "NO")
{
	print "<option value='YES'>Yes</option>\n";
	print "<option selected value='NO'>No</option>\n";
	print "<option value='RANDOM'>Random</option>\n";
}
elsif ($include_privacy eq "RANDOM")
{
	print "<option value='YES'>Yes</option>\n";
	print "<option value='NO'>No</option>\n";
	print "<option selected value='RANDOM'>Random</option>\n";
}
else 
{
	print "<option selected value='YES'>Yes</option>\n";
	print "<option value='NO'>No</option>\n";
	print "<option value='RANDOM'>Random</option>\n";
}
print<<"end_of_html";
</select></td></tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed5"></font></td>
													<td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Articles: </font></td>
													<td vAlign="center" align="left">
													<select name="article" size="1">
end_of_html
$sql = "select brand_article.article_id,article_name from brand_article,article where brand_id=$bid and brand_article.article_id=article.article_id and status='A' order by article_name"
;
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($uid,$url) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$url</option>\n";
}
$sth1->finish();
print<<"end_of_html";
									</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_article();">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_article();">&nbsp;&nbsp;<input type="button" value="Delete All" name="B22" onClick="del_all_articles();">
</td></tr>

												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed5"></font></td>
													<td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">From Address: </font></td>
													<td vAlign="center" align="left">
														<input type='text' name='from_address' value='$from_address'>
													</td></tr>
																									<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed5"></font></td>
													<td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Display Name: </font></td>
													<td vAlign="center" align="left">
														<input type='text' name='display_name' value='$display_name'>
													</td></tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed5"></font></td>
													<td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Subject: </font></td>
													<td vAlign="center" align="left">
														<input type='text' name='subject' value='$subject'>
													</td></tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="pixel_placed21"></font></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Exclude Wiki: </font></td><td vAlign="center" align="left">
end_of_html
if ($exclude_wiki eq "Y")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio checked value='Y' name="exclude_wiki">Yes&nbsp;&nbsp;<input type=radio value='N' name="exclude_wiki">No</font></td>
end_of_html
}
else
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Y' name="exclude_wiki">Yes&nbsp;&nbsp;<input type=radio checked value='N' name="exclude_wiki">No</font></td>
end_of_html
}
print<<"end_of_html";
</tr>

													<tr>
														<td vAlign="center" noWrap align="right" width="9%">
															<font size="1">
															<input type="checkbox" value="Y" name="pixel_placed5"></font></td>
															<td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Enable auto-replacement for mailing/future/image domains?: </font></td>
															<td vAlign="center" align="left">
															<input type=radio $enable_replace_yes value='1' name="enable_replace">Yes&nbsp;&nbsp;<input type=radio $enable_replace_no value='0' name="enable_replace">No</font>
														</td>
													</tr>

												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Brand Scheduling Priority: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name='brand_priority'>
end_of_html
if ($brand_priority eq "A")
{
	print "<option selected value='A'>A</option>\n";
	print "<option value='B'>B</option>\n";
	print "<option value='C'>C</option>\n";
	print "<option value='D'>D</option>\n";
}
elsif ($brand_priority eq "B")
{
	print "<option value='A'>A</option>\n";
	print "<option selected value='B'>B</option>\n";
	print "<option value='C'>C</option>\n";
	print "<option value='D'>D</option>\n";
}

elsif ($brand_priority eq "D")
{
	print "<option value='A'>A</option>\n";
	print "<option value='B'>B</option>\n";
	print "<option value='C'>C</option>\n";
	print "<option selected value='D'>D</option>\n";
}

#default priority
else 
{
	print "<option value='A'>A</option>\n";
	print "<option value='B'>B</option>\n";
	print "<option selected value='C'>C</option>\n";
	print "<option value='D'>D</option>\n";
}
print<<"end_of_html";
</select></td></tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Rdns URLs: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<textarea name="rdns_urls">
end_of_html
my $rdomain;
$sql="select rdns_domain from brand_rdns_info where brand_id=$bid";
my $sth1a=$dbhu->prepare($sql);
$sth1a->execute();
while (($rdomain)=$sth1a->fetchrow_array())
{
	print "$rdomain\n";
}
$sth1a->finish();
print<<"end_of_html";
</textarea></td></tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Number of Domains to Rotate: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
<select name='num_domains_rotate'>
end_of_html
my $i=1;
while ($i <= 10)
{
	if ($i == $num_domains_rotate)
	{
		print "<option value=$i selected>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
<tr>
<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed14"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
													</font><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Advertiser Mailing Domains:
													</font></td>
													<td vAlign="center" align="left">
													<select name="adv_domain">
end_of_html
$sql="select ai.advertiser_id,ai.advertiser_name,count(*) from advertiser_info ai, brand_advertiser_info bai where ai.advertiser_id=bai.advertiser_id and bai.brand_id=? group by ai.advertiser_id,advertiser_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute($bid);
my $aid;
my $aname;
my $cnt;
while (($aid,$aname,$cnt) = $sth1->fetchrow_array())
{
	print "<option value=$aid>$aname ($cnt)</option>\n";
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Add" onClick="add_domain();">&nbsp;&nbsp;<input type="button" value="Edit" onClick="edit_domain();">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="del_domain();"></td>
												</tr>

												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed21"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Purpose: </font>
													</td>
													<td vAlign="center" align="left">
end_of_html
if ($purpose eq "Normal")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio checked value='Normal' name="purpose">Normal&nbsp;&nbsp;<input type=radio value='Daily' name="purpose">Daily&nbsp;&nbsp;&nbsp;<input type=radio value='Trigger' name="purpose">Trigger</font></td>
end_of_html
}
elsif ($purpose eq "Daily")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Normal' name="purpose">Normal&nbsp;&nbsp;<input type=radio checked value='Daily' name="purpose">Daily&nbsp;&nbsp;&nbsp;<input type=radio value='Trigger' name="purpose">Trigger</font></td>
end_of_html
}
elsif ($purpose eq "Trigger")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Normal' name="purpose">Normal&nbsp;&nbsp;<input type=radio value='Daily' name="purpose">Daily&nbsp;&nbsp;&nbsp;<input type=radio checked value='Trigger' name="purpose">Trigger</font></td>
end_of_html
}
print<<"end_of_html";
												</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="pixel_placed21"></font></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Ignore MTA Template Settings: </font></td><td vAlign="center" align="left">
end_of_html
if ($ignore_mta_template_settings eq "Y")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio checked value='Y' name="ignore_mta_template_settings">Yes&nbsp;&nbsp;<input type=radio value='N' name="ignore_mta_template_settings">No</font></td>
end_of_html
}
else
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Y' name="ignore_mta_template_settings">Yes&nbsp;&nbsp;<input type=radio value='N' checked name="ignore_mta_template_settings">No</font></td>
end_of_html
}
print<<"end_of_html";
</tr>
												<tr><td vAlign="center" noWrap align="right" width="9%"><font size="1"><input type="checkbox" value="Y" name="generateSpf"></font></td><td vAlign="center" noWrap align="right" width="31%"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Generate Spf: </font></td><td vAlign="center" align="left">
end_of_html
if ($generateSpf eq "Y")
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio checked value='Y' name="generateSpf">Yes&nbsp;&nbsp;<input type=radio value='N' name="generateSpf">No</font></td>
end_of_html
}
else
{
print<<"end_of_html";
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type=radio value='Y' name="generateSpf">Yes&nbsp;&nbsp;<input type=radio value='N' checked name="generateSpf">No</font></td>
end_of_html
}
print<<"end_of_html";
</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Double Confirm Content: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<textarea name="content" cols="50" rows="10">$content</textarea></td>
													</tr>
												<tr>
													<td vAlign="center" noWrap align="right" width="9%">
													<font size="1">
													<input type="checkbox" value="Y" name="pixel_placed22"></font></td>
													<td vAlign="center" noWrap align="right" width="31%">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Double Confirm Disclaimer: </font>
													</td>
													<td vAlign="center" align="left">
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<textarea name="disclaimer" cols="50" rows="10">$disclaimer</textarea></td>
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
