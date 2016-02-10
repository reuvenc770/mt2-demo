#!/usr/bin/perl
#===============================================================================
# Name   : adv_search.cgi 
#
#--Change Control---------------------------------------------------------------
# 07/05/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $aim;
my $website;
my $username;
my $password;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Weekly</title>
</head>

<body><!-- <font face="Verdana"><b>Search Criteria:</b></font><br> -->
<form method=post action="/cgi-bin/advertiser_list.cgi" target="_blank">
<input type=hidden name=search value="Y">
<table border="1" width="100%" id="table2">
	<tr>
			<td width="134"><b><font face="Verdana" size="2">Network:</font></b></td>
		<td>
											<font face="Arial" color="#509c10" size="2">
											<select name="client_id">
<option value="0" selected>ALL</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select>
</font>
&nbsp;&nbsp;Not
<select name="not_client_id">
<option value="0" selected>None</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
</font></td>
	</tr>
	<tr>
			<td width="134"><b><font face="Verdana" size="2">Category:</font></b>
		</td>
		<td colspan=1>
											<select name="catid">
end_of_html
		print "<option value=0 selected>ALL</option>\n";
$sql="select category_id,category_name from category_info where status='A' order by category_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select><font face="Verdana" size="2">or</font><select name="catid1">
end_of_html
		print "<option value=0 selected>ALL</option>\n";
$sql="select category_id,category_name from category_info where status='A' order by category_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select><font face="Verdana" size="2">or</font><select name="catid2">
		print "<option value=0 selected>ALL</option>\n";
end_of_html
$sql="select category_id,category_name from category_info where status='A' order by category_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select></td>
<td width="134"><b><font face="Verdana" size="2"># Lists Scheduled:</font></b></td>
			<td><select name="lists_scheduled">
			<option value selected></option>
			<option value="=">=</option>
			<option value="&gt;">&gt;</option>
			<option value="&lt;">&lt;</option>
			</select><select name="lists_scheduled_value">
			<option value selected></option>
			<option value="0">None</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
			<option value="15">15</option>
			</select></td>
	</tr>
	<tr>
		<td width="134">
<b><font face="Verdana" size="2">Last Run:</font></b></td>
		<td>
									<select name="last_run1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="last_run2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="last_run3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="last_run4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		<td width="134">
<b><font face="Verdana" size="2">Rotation Modified:</font></b></td>
		<td>
									<select name="rotation_modified1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="rotation_modified2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="rotation_modified3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="rotation_modified4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		</tr>
	<tr>
		<td width="134">
<b><font face="Verdana" size="2">Advertiser URL Updated:</font></b></td>
		<td>
									<select name="adurl_1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="adurl_2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="adurl_3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="adurl_4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		<td width="134">
<b><font face="Verdana" size="2">Creative #:</font></b></td>
            <td><select name="creative_num">
            <option value selected></option>
            <option value="=">=</option>
            <option value="&gt;">&gt;</option>
            <option value="&lt;">&lt;</option>
            </select><select name="creative_num_value">
            <option value selected></option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            </select></td>
</tr>
	<tr>
		<td width="134">
<b><font face="Verdana" size="2">Creative Modified:</font></b></td>
		<td>
									<select name="creative_modified1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="creative_modified2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="creative_modified3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="creative_modified4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		<td width="134">
<b><font face="Verdana" size="2">Approved:</font></b></td>
		<td>
									<select name="approved1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="approved2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="approved3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="approved4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		</tr>
	<tr>
			<td width="134"><b><font face="Verdana" size="2">Payin:</font></b></td>
		<td><select name="payout">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" value="" name="payout_value"><font face="Verdana" size="2">and</font><select name="payout1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="payout1_value"></td>
			<td width="134"><b><font face="Verdana" size="2">AOL Complaint %:</font></b></td>
		<td><select name="aol_comp">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" value="" name="aol_comp_value"><font face="Verdana" size="2">and</font><select name="aol_comp1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="aol_comp1_value"></td>
		</tr>
	<tr>
			<td width="134"><b>eCPM:</b></td>
			<td><select name="ecpm">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="ecpm_value"><font face="Verdana" size="2">and</font><select name="ecpm1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="ecpm1_value"></td>
			<td width="134"><b>Advertiser Rating:</b></td>
			<td><select name="adv_rating">
                    <option value="" selected></option>
                                    <option value="=">=</option>
                                    <option value=">">></option>
                                    <option value="<"><</option>
</select><select name="adv_rating_value">
					<option value="" ></option>
					<option value="0">None</option>
					                <option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
</select></td>
		</tr>
			<tr>
            <tr>
              <td width="134"><b>3rd Party URL</b></td>
              <td>
                <select name="third_url_flag">
                  <option value="">
                  <option value="Y">Y
                  <option value="N">N
                </select>
              </td>
			<td width="134"><b>Pixel Verified</b></td>
			<td><select name="pixel_verified">
					<option value="" selected></option>
									<option value="Y">Y</option>
									<option value="N">N</option>
									<option value="?">?</option>
				</select>
				OR 
				<select name="pixel_verified_logic">
                    <option value="" selected></option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                    <option value="?">?</option>
                </select>
		</td>
            </tr>
<tr>
			<td width="134"><b>Direct Track</b></td>
			<td><select name="direct_track">
					<option value="" selected></option>
									<option value="Y">Y</option>
									<option value="N">N</option>
									<option value="?">?</option>
				</select>
				OR 
				<select name="direct_track_logic">
                    <option value="" selected></option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                    <option value="?">?</option>
                </select>
		</td>
 
 		<td width="12%"><b>Suppression Updated</b></td>
		<td width="27%"><select name="supp_updated">
		<option value selected></option>
		<option value="=">=</option>
		<option value="&lt;">&gt;</option>
		<option value="&gt;">&lt;</option>
		</select><select name="supp_updated_value">
		<option value selected></option>
		<option value="0">Never</option>
		<option value="3">3 Days</option>
		<option value="4">4 Days</option>
		<option value="5">5 Days</option>
		<option value="6">6 Days</option>
		<option value="7">7 Days</option>
		<option value="10">10 Days</option>
		</select></td>
 
            </tr>



<tr>
			<td width="134"><b>Advertiser Name</b></td>
			<td><input type=text size=100 maxlength=100 name=adname>
		</td>
			<td width="134"><b>Creative Name</b></td>
			<td><input type=text size=80 maxlength=80 name=crename>
		</td>
		<td>
		</tr>
		<tr>
			<td width="134"><b>Rotation From</b></td>
			<td><input type=text size=20 maxlength=30 name=from>
		</td>
			<td width="134"><b>Rotation Subject</b></td>
			<td><input type=text size=20 maxlength=30 name=subject>
		</td>
		</tr>
		<tr>
			<td width="134"><b>All From</b></td>
			<td><input type=text size=20 maxlength=30 name=afrom>
		</td>
			<td width="134"><b>All Subject</b></td>
			<td><input type=text size=20 maxlength=30 name=asubject>
		</td>
		</tr>
		<tr>
			<td width="134"><b>Company Name</b></td>
			<td><input type=text size=20 maxlength=30 name=cname>
		</td>
			<td width="134"><b>Contact Info Name</b></td>
			<td><input type=text size=20 maxlength=30 name=contact_name>
		</td>
		</tr>
		<tr>
			<td width="134"><b>Status</b></td>
			<td><select name=cstatus><option value="">ALL</option><option value=A>Active</option><option value=C>Pending</option><option value=I>Inactive</option><option value=W>In Progress</option><option value=P>Paused</option><option value=B>Problem</option><option selected value=R>Requested</option><option value=T>Testing</option><option value=U>Update</option></select>&nbsp;or&nbsp;<select name=cstatus2><option value=""></option><option value=A>Active</option><option value=C>Pending</option><option value=I>Inactive</option><option value=W>In Progress</option><option value=P>Paused</option><option value=B>Problem</option><option value=R>Requested</option><option selected value=T>Testing</option><option value=U>Update</option></select>&nbsp;or&nbsp;<select name=cstatus3><option value=""></option><option selected value=A>Active</option><option value=C>Pending</option><option value=I>Inactive</option><option value=W>In Progress</option><option value=P>Paused</option><option value=B>Problem</option><option value=R>Requested</option><option value=T>Testing</option><option value=U>Update</option></select>&nbsp;or&nbsp;<select name=cstatus4><option value=""></option><option value=A>Active</option><option value=C>Pending</option><option value=I>Inactive</option><option value=W>In Progress</option><option value=P selected>Paused</option><option value=B>Problem</option><option value=R>Requested</option><option value=T>Testing</option><option value=U>Update</option></select>&nbsp;or&nbsp;<select name=cstatus5><option selectd value=""></option><option value=A>Active</option><option value=C>Pending</option><option value=I>Inactive</option><option value=W>In Progress</option><option value=P>Paused</option><option value=B>Problem</option><option value=R>Requested</option><option value=T>Testing</option><option value=U>Update</option></select>&nbsp;or&nbsp;<select name=cstatus6><option value="" selected></option><option value=A>Active</option><option value=C>Pending</option><option value=I>Inactive</option><option value=W>In Progress</option><option value=P>Paused</option><option value=B>Problem</option><option value=R>Requested</option><option value=T>Testing</option><option value=U>Update</option></select>
		</td>
			<td width="134"><b>SID</b></td>
			<td><input type=text size=40  name=sid>
		</td>
		</tr>
		<tr>
			<td width="134"><b>Cake Creative ID</b></td>
			<td><input type=text size=40  name=cake_creative_id>
		</td>
			<td width="134"><b>Cake Offer ID</b></td>
			<td><input type=text size=40  name=cake_offer_id>
		</td>
		</tr>
		<tr>
			<td width="134"><b>Traffic Source</b></td>
			<td><select name=csource><option selected value=""></option><option value=Internal>Internal</option><option value=Network>Network</option><option value=Display>Display</option><option value="LinkOut">Link out</option></select>&nbsp;or&nbsp;<select name=csource1><option value=""></option><option value=Internal>Internal</option><option value=Network>Network</option><option value=Display>Display</option><option value="LinkOut">Link out</option></select>&nbsp;or&nbsp;<select name=csource2><option value=""></option><option value=Internal>Internal</option><option value=Network>Network</option><option value=Display>Display</option><option value="LinkOut">Link out</option></select>&nbsp;or&nbsp;<select name=csource3><option value=""></option><option value=Internal>Internal</option><option value=Network>Network</option><option value=Display>Display</option><option value="LinkOut">Link out</option></select>
	</td>
			<td width="134"><b>Original Subject</b></td>
			<td><select name=oflag><option selected value="">All</option><option value="Y">Yes</option><option value="N">No</option></select></td>
		</tr>
		<tr>
			<td width="134"><b>Country</b></td>
			<td>
end_of_html
	my $abbr;
	my $country_name;
	print "<select name=country1><option value=\"\"></option>";
	$sql="select countryID,countryName from Country where visible=1 order by countryName"; 
	my $sthdd=$dbhu->prepare($sql);
	$sthdd->execute();
	while (($abbr,$country_name)=$sthdd->fetchrow_array())
	{
		print "<option value=$abbr>$country_name</option>";
	}
	$sthdd->finish();
	print "</select>&nbsp;or&nbsp;";
	print "<select name=country2><option value=\"\"></option>";
	$sql="select countryID,countryName from Country where visible=1 order by countryName";
	$sthdd=$dbhu->prepare($sql);
	$sthdd->execute();
	while (($abbr,$country_name)=$sthdd->fetchrow_array())
	{
		print "<option value=$abbr>$country_name</option>";
	}
	$sthdd->finish();
	print "</select>&nbsp;or&nbsp;";
	print "<select name=country3><option value=\"\"></option>";
	$sql="select countryID,countryName from Country where visible=1 order by countryName";
	$sthdd=$dbhu->prepare($sql);
	$sthdd->execute();
	while (($abbr,$country_name)=$sthdd->fetchrow_array())
	{
		print "<option value=$abbr>$country_name</option>";
	}
	$sthdd->finish();
	print "</select>&nbsp;or&nbsp;";
	print "<select name=country4><option value=\"\"></option>";
	$sql="select countryID,countryName from Country where visible=1 order by countryName";
	$sthdd=$dbhu->prepare($sql);
	$sthdd->execute();
	while (($abbr,$country_name)=$sthdd->fetchrow_array())
	{
		print "<option value=$abbr>$country_name</option>";
	}
	$sthdd->finish();
	print "</select>";
print<<"end_of_html";
		</td></tr>
		<tr>
			<td width="134"><b>Requested By</b></td>
			<td><select name=inmanager_id>
			<option value="" selected>ALL</option>
end_of_html
$sql="select manager_id,manager_name from CampaignManager order by manager_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $mid;
my $mname;
while (($mid,$mname)=$sth->fetchrow_array())
{
	print "<option value=$mid>$mname</option>";
}
$sth->finish();
print<<"end_of_html";
</select></td>
			<td width="134"><b>Requested By Group</b></td>
			<td><select name=manager_group>
			<option value="" selected>ALL</option>
			<option value="DigitalPub">DigitalPub</option>
			<option value="Mailops">Mailops</option>
			<option value="Other">Other</option>
			<option value="Sales">Sales</option>
			<option value="SocalMedia">SocialMedia</option>
			</select></td></tr>
			<tr>
			<td width="134"><b>Order By</b></td>
			<td><select name=orderby>
			<option value=Alpha selected>Alpha</option>
			<option value=Priority>Priority</option>
			</select></td>
</tr>
	<tr>
			<td width="134"><b>Save As</b></td>
			<td colspan=4><input type=text size=150 maxlength=255 name=query_name></td></tr>
<tr><td width="134"><b>Saved Queries: <b></td><td colspan=2><select name=squery><option value="0">-- Select One --</option>
end_of_html
$sql="select query_id,query_name from saved_query order by query_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $qid;
my $qname;
while (($qid,$qname)=$sth->fetchrow_array())
{
	print "<option value=$qid>$qname</option>";
}
$sth->finish();
print<<"end_of_html"
</select>
		</td>
<td>
		<input type="submit" value="Submit" name="B27"></td>
			</tr>
			</table>
</form>
</body>
</html>
end_of_html
