#!/usr/bin/perl
# *****************************************************************************************
# login_form.cgi
#
# this page displays the login form
#
# History
# Grady Nash, 11/21/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use util;
my $util = util->new;
my $dbh;
my $sth;
my $sql;
my $ctitle;

#------ connect to the util database -------------------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

$sql = "select parmval from sysparm where parmkey = 'SITENAME'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($ctitle) = $sth->fetchrow_array();
$sth->finish();
$util->clean_up();

print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
    <head>
        <title>$ctitle.com Mail Log On</title>
   </head>
    <body>
        <form method="POST" action="login.cgi">
            <table cellSpacing=0 cellPadding=0 border=0>
                <tr>
                    <td colspan="2">
                        <img height=3 src="/images/spacer.gif">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <font face="verdana,arial,helvetica,sans serif" color=#509c10 size=3>
                            <b>New Mailing Tool Log On</b>
                        </font>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><IMG height=6 src="/images/spacer.gif"></td>
                </tr>
                <tr>
                    <td vAlign=center noWrap align=right>
                        <font face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>Username&nbsp;&nbsp</font>
                    </td>
                    <td vAlign=center align=left>
                        <font face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>
                            <input name=username size="20">
                        </font>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img height=7 src="/images/spacer.gif">
                    </td>
                </tr>
                <tr>
                    <td vAlign=center noWrap align=right>
                        <font face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>Password&nbsp;&nbsp;</font>
                    </td>
                    <td vAlign=center align=left>
                        <font face="verdana,arial,helvetica,sans serif" color=#509c10 size=2>
                        <input type=password name=password size="20"> </font>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img height=6 src="/images/spacer.gif">
                    </td>
                </tr>
                <tr>
                    <td align=middle colspan="2">
                        <input type=submit value="Log On">
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <a href="forgot.cgi">Forgot Your Password?</a>
        <br>
    </body>
</html>
end_of_html

exit(0);
