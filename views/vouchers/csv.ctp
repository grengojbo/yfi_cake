<?php
    Configure::write('debug', 0);
    print("Realm,Username,Password,Profile,Days Valid,Expiry Date\n");

    foreach($csv_structure as $voucher){
        print($voucher['realm'].','.$voucher['username'].','.$voucher['password'].','.$voucher['profile'].','.$voucher['days_valid'].','.$voucher['expiry_date']."\n");
    }

?>