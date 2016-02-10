#!/usr/bin/perl

use strict;
use CGI;
use CGI::Cookie;
use util;
use DBI;
use vars qw($DBHQ);
require "/var/www/html/newcgi-bin/modules/Common.pm";

# get some objects to use later
my $util = util->new;

# connect to the util database
$DBHQ=DBI->connect('DBI:mysql:new_mail:updatep.routename.com','db_user','sp1r3V') or die "Can't connect to DB: $!\n";
my $args=Common::get_args();

if ($args->{submit}) {
	my ($err_flag,$hrInfo)=validate_data($args);
	if ($err_flag==1) {
		display_header();
		display_login_form($args,$hrInfo);
		display_footer();
	}
	else {
		if ($hrInfo->{user_id} eq 'cbs') {
			$args->{redir}||='cbs_deployed_view.cgi';
		}
		else {
			$args->{redir}||='cbs_deployed_view_new.cgi';
		}
		my $cookie = "cbslogin=$hrInfo->{user_id}; path=/;";
		print "Set-Cookie: $cookie\n";
		print "Location: $args->{redir}\n\n";
	}
}
elsif ($args->{action} eq 'logout') {
	clear_cookie();
	print "Location: cbs_login.cgi?redir=$args->{redir}\n\n";
}
else {
	display_header();
	display_login_form($args);
	display_footer();
}

sub display_header {

print "Content-type: text/html\n\n";
print qq^
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Deployed Creative</title>
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
                        <td align="left"><b><font face="Arial" size="2">&nbsp;Deployed Creative View</font></b></td>
                    </tr>
                    <tr>
                        <td align="right"><b>
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
			my $dataHR={'cbs'=>'cbs1creative', 'dave'=>'dave'};
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

sub display_login_form {
	my ($args,$info)=@_;

	print qq^
	<tr><td>
	<table border=0 width=450 align=center>
	<form method="post" action="cbs_login.cgi">
	<input type=hidden name="redir" value="$args->{redir}">
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
			  <td class='txt'>&nbsp;<input type='submit' class='input' name='submit' value='submit'></td>
			</tr>
		  </table>
		</td>
	  </tr>
	</form>
	</table>
	</td></tr>
	^;
}

sub clear_cookie {

	my $cookie = "doslogin=''; path=/;";
	print "Set-Cookie: $cookie\n";
}
