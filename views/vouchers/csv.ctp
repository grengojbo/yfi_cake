<?php
    Configure::write('debug', 0);
    print("Realm,Username,Password,Profile,Days Valid,Expiry Date,Created,Status,First Used,Data Usage\n");

    foreach($csv_structure as $voucher){
        print(
        $voucher['realm'].','.
        $voucher['username'].','.
        $voucher['password'].','.
        $voucher['profile'].','.
        $voucher['days_valid'].','.
        $voucher['expiry_date'].','.
        $voucher['created'].','.
        $voucher['status'].','.
        $voucher['first_used'].','.
        $voucher['data_used'].
        "\n");
    }

?>
