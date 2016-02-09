#!/usr/bin/perl -X
use strict;
use Mail::IMAPClient;
use Lib::Database::Perl::Interface::Unsubscribe;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

my ($host, $id, $pass) = qw( imap-mail.outlook.com anielkateige@hotmail.com 24V!sion);

my $imap = Mail::IMAPClient->new(
  Server => $host,
  User    => $id,
  Password=> $pass,
  Port => 993,
  Ssl => 1,
) or die "Cannot connect to $host as $id: $@";
my $unsubscribeInterface=Lib::Database::Perl::Interface::Unsubscribe->new();

my $ct = 0;
my @allMsgs;
my @FOLDERS=("Inbox","Junk");
foreach my $folder ( @FOLDERS ){
  $imap->select($folder);
  $imap->Peek(1);
  my @msgs = grep $_, $imap->unseen();
  next unless scalar @msgs;
  push @allMsgs, { folder => $folder, msgs => \@msgs };
  $ct += scalar (@msgs);
}
foreach my $h (@allMsgs){
  my $folder = $h->{folder};
  my @msgs = @{$h->{msgs}};
  $imap->select($folder);
  $imap->Peek(1);
  foreach my $msgId ( reverse @msgs )
  {
    printf "%-2d [%s] %s\n", $ct, $folder, $imap->subject($msgId); 
    my $from=$imap->get_header($msgId, 'From');
	my ($t1,$cfrom)=split("<",$from);
	$cfrom=~s/>//g;
	print "From: <$cfrom>\n";
    $ct--;
	util::addGlobal({ 'emailAddress' => $cfrom, 'suppressionReasonCode' =>  'UNSUB'} );
	my $sql="update email_list set status='U',unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_addr='$cfrom' and status='A'";
	my $rows=$dbhu->do($sql);
	$imap->delete_message($msgId);
  }
}
