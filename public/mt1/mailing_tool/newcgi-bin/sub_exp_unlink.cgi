#!/usr/bin/perl
#===============================================================================
# Purpose: Remove file from ../html dir then goto specified URL.
# File   : sub_exp_unlink.cgi
#
# Input  : 
# Output : 
#
# print $query->redirect("file_layout_disp.cgi?mesg=$mesg") ;#
#
#--Change Control---------------------------------------------------------------
#  Sep 20, 2001  Mike Baker  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my ($goto_url, $del_file) ;

$goto_url = $query->param('goto_url') ;
$del_file = $query->param('del_file') ;
$goto_url = $query->param('goto_url') ;

$goto_url = "mainmenu.cgi" ;

print $query->redirect($goto_url) ;

