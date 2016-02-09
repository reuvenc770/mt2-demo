#!/usr/bin/perl
# *****************************************************************************************
# update_list_cnt.pl
#
# History
# Jim Sobeck,   04/09/03,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $sth1a;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "update_list_cnt.pl";
my $errmsg;
my $bin_dir_http;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $uid;
my $list_aol_cnt_p;
my $list_hotmail_cnt;
my $list_msn_cnt = 0;
my $list_cnt;
my $did;
my $list_yahoo_cnt;
my $list_foreign_cnt = 0;
my $last_email_user_id;
my $max_emails;
my $clast60;
my $aolflag;
my $openflag;
my $first_email_user_id;
my $addrec;
my $begin;
my $end;
my $list_str;
my $bend;
#
#  Set up array for servers
#
my $sarr_cnt = 6;
my $cnt2;

# connect to the util database

$| = 1;

# Get list counts 
mail_send();

$util->clean_up();
exit(0);

# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{
	my $subject;
	my $from_addr;
	my $list_id;
	my $list_name;
	my $email_user_id;
	my $cemail;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$cnt2 = 0;
	$curcnt = 1;
	$cnt = 0;
	$total_cnt = 0;
	$aol_cnt = 0;

    $sql="
    insert into 
    	total_mailable 
    select 
	    user_id,
	    date_sub(curdate(),interval 1 day),
	    sum(yahoo_cnt),
	    sum(member_cnt)-sum(aol_cnt)-sum(hotmail_cnt)-sum(msn_cnt)-sum(yahoo_cnt)-sum(comcast_cnt),
	    sum(aol_cnt),
	    sum(hotmail_cnt)+sum(msn_cnt),
	    sum(comcast_cnt)
    from list 
    where 
	    status='A' 
	    and list_type != 'CHUNK' 
	    and user_id in (select user_id from user where tab='email_list') 
    group by user_id,2";
    $util->db_connect();
    $dbh = $util->get_dbh;
    $rows = $dbh->do($sql);
    $util->clean_up();
}
