#!/usr/bin/perl

# *****************************************************************************************
# dosmonos_deploy.cgi
#
# this page displays the DosMonos deploy page 
#
# History
# Jim Sobeck, 02/24/06, Creation
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
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $curl;
my $sdate1;
my $sdate;
my $old_mailer_id;
my $old_client_id;
my $old_brand_id;
my $old_catid;
my $old_aid;
my $old_name;
my $am_pm;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
$sql = "select date_format(curdate(),'%m/%d/%Y')";
$sth = $dbh->prepare($sql);
$sth->execute();
($sdate1) = $sth->fetchrow_array();
$sth->finish();
$sql = "select date_format(curdate(),'%m/%d/%Y')";
$sth = $dbh->prepare($sql);
$sth->execute();
($sdate) = $sth->fetchrow_array();
$sth->finish();
#
my $id;
my $hour="";
my $old_id= $query->param('id');
my $mode = $query->param('mode');
if ($mode eq "")
{
	$mode="Deploy";
}
else
{
	$mode="Copy";
}
	$old_catid = 0;
	$old_mailer_id=0;
	$old_client_id=0;
	$old_aid=0;
	$old_name="";
if ($old_id eq "")
{
	$old_id = 0;
}
if ($old_id > 0)
{
	$sql="select third_party_id,client_id,brand_id,catid,advertiser_id,deploy_name,date_format(scheduled_datetime,'%m/%d/%Y'),hour(scheduled_datetime) from 3rdparty_campaign where id=$old_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($old_mailer_id,$old_client_id,$old_brand_id,$old_catid,$old_aid,$old_name,$sdate,$hour) = $sth->fetchrow_array();
	$sth->finish();
}
if ($mode eq "Copy")
{
	$old_id=0;
}
my $mailer_name;
my $num_subject;
my $num_from;
my $num_creative;
my $rname;
my $rloc;
my $rdate;
my $remail;
my $rcid;
my $remailid;
my $unsub_flag;
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Logout&nbsp;&nbsp;&nbsp; Customer Assistance</title>
<script language="JavaScript">
function ProcessForm(mode)
{
    var selObj = document.getElementById('advertiser_id');
    var selIndex = selObj.selectedIndex;
	if (selIndex < 1)
	{
		alert('You must select an advertiser');
		return false;
	}
	if (campform.campaign_name.value == "")
	{
		alert('You must enter a Deploy As Name');
		campform.campaign_name.focus();
		return false;
	}
	return true;
}
function addAdvertiser(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.advertiser_id.add(newOpt);
}
function set_advertiser()
{
    var selObj = document.getElementById('advertiser_id');
    var i;
    for (i=0; i<selObj.options.length; i++) {
    if (selObj.options[i].value == $old_aid)
    {
            selObj.selectedIndex = i;
        }
    }
}
function update_name()
{
end_of_html
if ($old_id == 0)
{
print<<"end_of_html";
    var selObj1 = document.getElementById('advertiser_id');
    var selIndex1 = selObj1.selectedIndex;
	if (selObj1.options[selIndex1].value == 0)
	{
		campform.campaign_name.value = "(Dos Monos)";
	}
	else
	{
		 var str1 = selObj1.options[selIndex1].text;		
         var pos = str1.indexOf('('); 
          if (pos >= 0) 
          { 
               var argname = str1.substring(0,pos); 
		  }
		  else
		  {
               var argname = selObj1.options[selIndex1].text; 
		  }	

		campform.campaign_name.value = argname + " (Dos Monos)";
	}		
end_of_html
}
print<<"end_of_html";
}
function update_advertiser()
{
    var selObj = document.getElementById('catid');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.advertiser_id.length;
    while (selLength>0)
    {
        campform.advertiser_id.remove(selLength-1);
        selLength--;
    }
    campform.advertiser_id.length=0;
    parent.frames[1].location="/newcgi-bin/3rd_upd_advertiser.cgi?cid="+selObj.options[selIndex].value;
}
</script>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;</font></b></td>
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
</SCRIPT>
		</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table5">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans serif" color="#509C10">
						$mode Creative</font></td>
					</tr>
					<tr>
						<td>
						<img height="3" src="/images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
					<tr>
						<td colSpan="10">&nbsp;</td>
					</tr>
				</table>
				<form name="campform" onsubmit="return ProcessForm('A');" method="post" action="/cgi-bin/dosmonos_deploy_it.cgi" target=_top>
				<input type=hidden name=old_id value=$old_id>
					<table cellSpacing="0" cellPadding="0" width="100%" bgColor="#ffffff" border="0" id="table8">
						<tr>
							<td>
							<table cellSpacing="0" cellPadding="5" width="100%" border="0" id="table9">
								<tr>
									<td align="middle">
									<table cellSpacing="0" cellPadding="0" width="99%" bgColor="#e3fad1" border="0" id="table10">
										<tr align="top" bgColor="#509c10" height="18">
											<td vAlign="top" align="left" height="15">
											<img height="7" src="/images/blue_tl.gif" width="7" border="0"></td>
											<td height="15">
											<img height="1" src="/images/spacer.gif" width="3" border="0"></td>
											<td align="middle" height="15">
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table11">
												<tr bgColor="#509c10" height="15">
													<td align="middle" width="100%" height="15">
													<b>
													<font face="Verdana,Arial,Helvetica,sans-serif" size="2" color="#FFFFFF">
													$mode Creative</font></b></td>
												</tr>
											</table>
											</td>
											<td height="15">
											<img height="1" src="/images/spacer.gif" width="3" border="0"></td>
											<td vAlign="top" align="right" bgColor="#509c10" height="15">
											<img height="7" src="/images/blue_tr.gif" width="7" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td colSpan="5">
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td>
											<img height="3" src="/images/spacer.gif" width="3"></td>
											<td align="middle">
											<img height="3" src="/images/spacer.gif" width="3"></td>
											<td align="middle">
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table12">
												<tr>
													<td align="middle">
													<img height="3" src="/images/spacer.gif" width="3"></td>
												</tr>
												<caption>
												<tr>
													<td vAlign="top">
													&nbsp;</td><br>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td vAlign="top"><b>3rd Party Mailer:</b></td>
													<td>Dos Monos
													<br>
&nbsp;</td>
												</tr>
												<tr>
													<td vAlign="top"><b>
													Category:</b></td>
													<td>
													<select name="catid" onChange="update_advertiser();">
                                            <option value="-1">ALL CATEGORIES
                                            </option>
end_of_html
my $category_name;
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
	if ($catid == $old_catid)
	{
    	print "<option selected value=$catid>$cname</option>\n";
	}
	else
	{
    	print "<option value=$catid>$cname</option>\n";
	}
}
$sth->finish;
print<<"end_of_html";
											</select><br>
													<br>
&nbsp;</td>
												</tr>
												<tr>
													<td vAlign="top"><b>
													Advertiser:</b></td>
													<td>
													<select name="advertiser_id" onChange="update_name();">
													<option value="0">Select One
													</option>
													</select><br>
													<br>
&nbsp;</td>
												</tr>
												<tr>
													<td vAlign="top">
													<b>
													Scheduled For:</b></td>
													<td>
													<input maxLength="30" size="9" name="tid0" value="$sdate">&nbsp;
											<select name="tid1">
end_of_html
my $i=1;
$am_pm="AM";
if ($hour > 12)
{
	$am_pm="PM";
	$hour = $hour - 12;
}
while ($i < 12)
{
	if ($i == $hour)
	{
		print "<option selected value=$i>$i</option>\n";	
	}
	else	
	{
		print "<option value=$i>$i</option>\n";	
	}
	$i++;
}
print<<"end_of_html";
											</select>&nbsp; <select name="am_pm">
end_of_html
if ($am_pm eq "AM")
{
	print "<option value=AM selected>AM</option>\n";
	print "<option value=PM >PM</option>\n";
}
else
{
	print "<option value=AM >AM</option>\n";
	print "<option value=PM selected>PM</option>\n";
}
print<<"end_of_html";
</select>
<br>
													<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
													<b>(Today's Date: $sdate1)</b></font><br>
&nbsp;</td>
												</tr>
												<tr>
													<td vAlign="top">
													<b>
													Deploy As:</b></td>
													<td>
													<input maxLength="80" size="46" value="$old_name" name="campaign_name"><input type="submit" value="Deploy" name="B28"><br>
													<br>
&nbsp;</td>
												</tr>
											</table>
											</td>
											<td align="middle">
											<img height="3" src="/images/spacer.gif" width="3"></td>
											<td>
											<img height="3" src="/images/spacer.gif" width="3"></td>
										</tr>
										<tr bgColor="#e3fad1" height="10">
											<td vAlign="bottom" align="left">
											<img height="7" src="/images/lt_purp_bl.gif" width="7" border="0"></td>
											<td>
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
											<td align="middle" bgColor="#e3fad1">
											<img height="3" src="/images/spacer.gif" width="1" border="0">
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
											<td>
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
											<td vAlign="bottom" align="right">
											<img height="7" src="/images/lt_purp_br.gif" width="7" border="0"></td>
										</tr>
									</table>
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<table cellSpacing="0" cellPadding="7" width="100%" border="0" id="table14">
								<tr>
									<td align="middle" width="50%">
									<a target="_top" href="/cgi-bin/mainmenu.cgi">
									<img src="/images/home_blkline.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td align="middle" width="50%">
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
</table>
end_of_html
print "<script language=JavaScript>\n";
if ($old_catid > 0)
{
	print "update_advertiser();\n";
}
print "</script>\n";
print<<"end_of_html";
</body>

</html>
end_of_html
