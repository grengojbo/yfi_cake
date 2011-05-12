#! /usr/bin/perl -w

use strict;

my $hotcakes_install_dir = '/var/www/c2/yfi_cake';

my @file_list = (
                'controllers/components/leftpane.php',
                'controllers/components/actions.php',
                'controllers/components/workspace.php',
                'controllers/vouchers_controller.php',
                'controllers/batches_controller.php',
                'views/vouchers/pdf.ctp',
                'views/batches/pdf.ctp',
                'vendors/fpdf/generic.php',
                'vendors/fpdf/label.php',
                'vendors/fpdf/invoice.php',
                'vendors/fpdf/receipt.php',
                'controllers/components/pdf.php',
            );

my $string_of_files = '';

foreach my $file (@file_list){

    my $file_location = $hotcakes_install_dir.'/'.$file;
    $string_of_files = $string_of_files.' '.$hotcakes_install_dir.'/'.$file;

    if(-e $file_location){
        print "File exist\n";


    }else{

        print "WARNING $file_location does not exist!\n";
    }

}

#print "The arguments are $string_of_files\n";

if(-e './messages.po'){

    system('rm ./messages.po');
}

system("xgettext --language=PHP $string_of_files" );

