<?php
    Configure::write('debug', 0);
   // echo $javascript->object($json_return);
?>

<html>
<head>
    <script type="text/javascript">
      window.name= "<?php echo($trans_id); ?>"

    </script>
</head>
<body>
    <?php
        if($error != false){

            echo($error);
        }
    ?>
</body>
</html>


