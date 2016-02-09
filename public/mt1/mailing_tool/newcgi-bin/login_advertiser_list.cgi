#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Advertisers 
# File   : login_advertiser_list.cgi for special user
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use CGI::Cookie;
use util;
use DBI;
use vars qw($DBHQ);
require "/var/www/html/newcgi-bin/modules/Common.pm";

$DBHQ=DBI->connect('DBI:mysql:new_mail:updatep.routename.com','db_user','sp1r3V') or die "Can't connect to DB: $!\n";

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my ($supp_name,$last_updated,$filedate,$sid);
my $sth1a;
my $day_cnt;
my $mediactivate_str;
my $pixel_requested;
my $aid_list="";
my $tables;
my $pixel_placed;
my $pixel_verified;

my $args=Common::get_args();

my ($sth, $sth1,$reccnt, $sql, $dbh ) ;
my $no_thumb_cnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

my ($dbhq,$dbhu)=$util->get_dbh();

my $cookie=retrieve_cookie();

if (!$cookie) {
	if ($args->{login}) {
		my ($err,$hrInfo)=validate_data($args);
		if ($err==1) {
			display_header();
			display_login_form($args,$hrInfo);
			display_footer();
		}
		else {
			my $cookie = "listadv=$hrInfo->{user_id}; path=/;";
			print "Set-Cookie: $cookie\n";
			print "Location: login_advertiser_list.cgi\n\n";
		}
	}
	else {
		display_header();
		display_login_form($args);
		display_footer();
	}
}
else {
	display_header();
	options($args);
	&disp_body();
	display_footer();
}
#------------------------
# End Main Logic
#------------------------




#===============================================================================
# Sub: disp_body
#===============================================================================
sub disp_body
{
	my ($bgcolor) ;

	print qq^
	<tr><td>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="8" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Advertisers</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the advertiser)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Advertiser Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Creative<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Approved</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="6%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Advertiser<br>Rating</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Last<br>Run Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Rotation<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Category</b></font></td>
	</TR> 
	^;
unless (!$args->{search}) {
	my $cate=my $adv=my $company="";
	if ($args->{adv_name}) {
		$adv=qq^AND a.advertiser_name like '%$args->{adv_name}%'^;
	}
	if ($args->{catid}) {
		if (ref ($args->{catid}) eq 'ARRAY') {
			my $temp;
			foreach my $cat (@{$args->{catid}}) {
				$temp.=qq^'$cat',^;
			}
			$temp=~s/,$//;
			$cate=qq^AND a.category_id IN ($temp)^;
		}
		else {
			$cate=qq^AND a.category_id='$args->{catid}'^;
		}
	}
	if ($args->{company}) {
		$company=qq^AND aci.contact_name LIKE '%$args->{company}%'^;
	}

	my $qAdv=qq|SELECT a.advertiser_id AS aid, advertiser_name, advertiser_rating, a.status, category_name,approval_requested_date |
			.qq|FROM advertiser_info a, category_info ci, advertiser_contact_info aci WHERE a.category_id=ci.category_id |
			.qq|AND a.advertiser_id=aci.advertiser_id AND a.status in ('A','S','I') $adv $cate $company |
			.qq|ORDER BY a.advertiser_name ASC|;
warn "$qAdv\n";
	$sth = $DBHQ->prepare($qAdv) ;
	$sth->execute();
	$reccnt = 0 ;
	while (my $hr=$sth->fetchrow_hashref) {
#		if ($args->{last_run1} ne '') {
#			if ($args->{last_run2}>0) {
#				my $q=qq|SELECT advertiser_id FROM
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "#EBFAD1" ;     # Light Green
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;     # Light Yellow
		}

		print qq{<TR bgColor=$bgcolor> \n} ;
		$sql = "select count(*) from creative where thumbnail ='' and advertiser_id=$hr->{aid} and status='A'";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($no_thumb_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($no_thumb_cnt == 0)
		{
			print qq{	<TD align=left>&nbsp;</td> \n} ;
		}
		else
		{
			print qq{	<TD align=left><font color=red>X</font></td> \n} ;
		}
		if ($hr->{status} eq "S")
		{
        	print qq{	<TD align=left> \n } ;
			print qq{		<A HREF="login_creative_deploy_it.cgi?aid=$hr->{aid}"><font color="red" face="Arial" size="2">$hr->{advertiser_name}</font></a></TD> \n } ;
		}
		elsif ($hr->{status} eq "I")
		{
        	print qq{	<TD align=left> \n } ;
			print qq{		<A HREF="login_creative_deploy_it.cgi?aid=$hr->{aid}"><font color="green" face="Arial" size="2"><b>$hr->{advertiser_name}</b></font></a></TD> \n } ;
		}
		else
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
			print qq{		<A HREF="login_creative_deploy_it.cgi?aid=$hr->{aid}">$hr->{advertiser_name}</a></font></TD> \n } ;
		}
		$sql = "select max(creative_date) from creative where advertiser_id=$hr->{aid} and status='A'"; 
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		my ($creativeDate) = $sth1->fetchrow_array();
		$sth1->finish();
        print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$creativeDate</font></TD> \n } ;
		$sql = "select date_format(max(date_approved),'%Y-%m-%d') from advertiser_tracking where advertiser_id=$hr->{aid} union select date_format(max(date_approved),'%Y-%m-%d') from creative where advertiser_id=$hr->{aid} order by 1 desc";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		my $approvedDate = $sth1->fetchrow_array();
		$sth1->finish();
		if ($approvedDate ne "")
		{
			my $tcnt;
			$sql = "select sum(campaign_log.sent_cnt) from campaign_log,campaign where campaign_log.campaign_id=campaign.campaign_id and campaign.advertiser_id=?";
			$sth1 = $dbhq->prepare($sql) ;
			$sth1->execute($hr->{aid});
			($tcnt) = $sth1->fetchrow_array();
			$sth1->finish();
			if ($tcnt eq "")
			{
				$tcnt = 0;
			}
			if ($tcnt > 0)
			{	
        		print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$approvedDate</font></TD> \n } ;
			}
			else
			{
        		print qq{	<TD align=left><font color="red" face="Arial" size="2">$approvedDate</font></TD> \n } ;
			}
		}
		else
		{
			my $req_date="";
			if ($hr->{approval_requested_date} ne "")
			{
				$req_date= "<font color=red>R" . $hr->{approval_requested_date}. "</font>";
			}
        	print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$creativeDate</font>$req_date</TD> \n } ;
		}
        $sql = "select max(date(sent_datetime)) from campaign where advertiser_id=$hr->{aid}"; 
        $sth1a = $dbhq->prepare($sql) ;
        $sth1a->execute();
       	my ($sdate) = $sth1a->fetchrow_array();
       	$sth1a->finish();
        $sql = "select date(max(date_modified)) from advertiser_setup where advertiser_id=$hr->{aid}"; 
        $sth1a = $dbhq->prepare($sql) ;
        $sth1a->execute();
       	my ($mdate) = $sth1a->fetchrow_array();
       	$sth1a->finish();
       	print qq{	<TD align=middle><font color="black">$hr->{advertiser_rating}</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$sdate</font></TD> \n } ;
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$mdate</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$hr->{category_name}</font></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();
}

	print qq^
	</tbody>
	</table>
	</td></tr>
	^;
} # end sub disp_body


sub display_header {

print "Content-type: text/html\n\n";
print qq^
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Advertisers</title>
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
                        <td align="left"><b><font face="Arial" size="2">&nbsp;Advertisers</font></b></td>
                    </tr>
                    <tr>
                        <td align="right"><b>
                        </td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
        </td>
    </tr>
^;
}

sub display_login_form {
    my ($args,$info)=@_;

    print qq^
    <tr><td>
    <table border=0 width=450 align=center>
    <form method="post" action="login_advertiser_list.cgi">
      <tr>  
        <td>    
          <table width='100%' align='center' bgcolor='#FFFFFF'>
            <tr>        
              <td class='err' colspan='2' align='center'>$info->{err}</td>
            </tr>           
            <tr>            
              <td class='txt' align='right'>UserID:</td>
              <td align='left'>&nbsp;<input type='text' name='userID' class='input' size='10' value='$args->{userID}'></td>
            </tr>       
            <tr>    
              <td class='txt' align='right'>Password:</td>
              <td align='left'>&nbsp;<input type='password' name='passwd' class='input' size='10' value='$args->{passwd}'></td>
            </tr>
            <tr>
              <td class='txt'>&nbsp;</td>
              <td class='txt'>&nbsp;<input type='submit' class='input' name='login' value='submit'></td>
            </tr>
          </table>
        </td>
      </tr>
    </form>
    </table>
    </td></tr>
    ^;  
}

sub display_footer {

print qq^
    <tr>
        <td>
        <table>
            <tr>
                <td>
                    <table id="table8" cellPadding="5" width="66%" bgColor="white">
                        <tr>
                            <td align="middle" width="47%">
                            <td align="middle" width="47%">
                            &nbsp;</td>
                            <td align="middle" width="50%">
                            &nbsp;</td>
                        </tr>
                    </table>
                    </b>
                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</body>
</html>
^;
}

sub validate_data {
        my ($hrArgs)=@_;
        my $err_flag=0; my $hrInfo={};

        if (!$hrArgs->{userID}) {
                $err_flag=1;
                $hrInfo->{err}='Please enter a userID';
        }       
        elsif (!$hrArgs->{passwd}) {
                $err_flag=1;
                $hrInfo->{err}='Please enter a password';
        }       
        else {      
            my $dataHR={'web_user'=>'spireV'};
                if (!$dataHR->{$hrArgs->{userID}}) {
                        $err_flag=1;
                        $hrInfo->{err}='Sorry, that userID doesn\'t exist';
                }       
                elsif (lc($dataHR->{$hrArgs->{userID}}) ne lc($hrArgs->{passwd})) {
                        $err_flag=1;
                        $hrInfo->{err}='Sorry, bad password';
                }   
                else {
                       $hrInfo->{user_id}=$hrArgs->{userID};
                }
        }
        return ($err_flag, $hrInfo);
}

sub retrieve_cookie {
    
    my ($hr, %cookies,$login_ok);
    my @rawCookies = split (/; /,$ENV{'HTTP_COOKIE'});
    foreach (@rawCookies) {
        my ($key, $val) = split (/=/,$_);
        $cookies{$key} = $val;
    }
  
    if ($cookies{'listadv'} ne "0") {
        $login_ok = $cookies{'listadv'};
    }
    else {
        $login_ok = 0;
    }
    return ($login_ok);
}

sub options {
	my $args=shift;

	print qq^
	<tr><td>
	<form method=post action="login_advertiser_list.cgi">
	<table border="1" width=600 align=left>
    <tr>    
        <td width="134"><b><font face="Verdana" size="2">Category:</font></b>
        </td>
        <td>
			<select name="catid" multiple size=6>
	^;
	my $sql="select category_id,category_name from category_info order by category_name";
	my $sth = $dbhq->prepare($sql);
	$sth->execute();
	while (my ($id,$category) = $sth->fetchrow_array()) {   
        print "<option value=$id>$category</option>\n";
    }
	$sth->finish;
	print qq^
			</select>
		</td>
	</tr>
<!--    <tr>
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
	</tr> -->
	<tr>
		<td>
			<b><font face="Verdana" size="2">Advertiser Name:</font></b></td>
		<td>
			<input type=text name='adv_name' size=30>
		</td>
	</tr>
	<tr>
		<td>
			<b><font face="Verdana" size="2">Company Name:</font></b></td>
		<td>
			<input type=text name='company' size=30>
		</td>
	</tr>
	<tr>
		<td colspan=2><input type=submit name='search' value="Search"> &nbsp;&nbsp;<a href="login_advertiser_list.cgi?search=1&all=1">Show All Advertisers</a> 
		</td>
	</tr>
	</table>
	</form></td></tr>
	^;
}

