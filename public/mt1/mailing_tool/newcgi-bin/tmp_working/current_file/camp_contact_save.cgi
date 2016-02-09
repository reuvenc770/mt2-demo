#!/usr/bin/perl

# *****************************************************************************************
# camp_contact_save.cgi
#
# this page is step 5c in the email campaign creation process
# edit the campaign contact info
#
# History
# Grady Nash, 8/2/01, Creation
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
my $rows;
my $errmsg;

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

# get the fields from the form 

my $campaign_id = $query->param('campaign_id');
my $status = $query->param('status');
my $contact_url = $query->param('contact_url');
my $contact_phone = $query->param('contact_phone');
my $contact_email = $query->param('contact_email');
my $contact_name = $dbh->quote($query->param('contact_name'));
my $contact_company = $dbh->quote($query->param('contact_company'));


# figure out which button was clicked, and go to the appropriate screen

my $nextfunc = $query->param('nextfunc');
if ($nextfunc eq "preview")
{
    #print "Location: camp_preview.cgi?campaign_id=$campaign_id\n\n";
    print "Location: camp_contact.cgi?campaign_id=$campaign_id&mode=preview\n\n";
}
elsif ($nextfunc eq "save")
{
    print "Location: camp_contact.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "test")
{
    print "Location: camp_test.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "previous")
{
    print "Location: camp_article.cgi?campaign_id=$campaign_id&article_num=1\n\n";
}
elsif ($nextfunc eq "next" || $nextfunc eq "introduction")
{
    print "Location: camp_step5.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "promo")
{
    print "Location: camp_promo.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "article")
{
    my $article = $query->param('article');
    print "Location: camp_article.cgi?campaign_id=$campaign_id&article_num=$article\n\n";
}
elsif ($nextfunc eq "exit")
{
    print "Location: mainmenu.cgi\n\n";
}
elsif ($nextfunc eq "contact")
{
    print "Location: camp_contact.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "advanced")
{
    print "Location: camp_advanced.cgi?campaign_id=$campaign_id\n\n";
}
else
{
    print "Content-type: text/html\n\n";
    print "<html><body>Unknown function <br> nextfunc=$nextfunc</body></html>\n";
}

$util->clean_up();
exit(0);
