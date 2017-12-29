<html>
   <head>
      <title>Connect to MariaDB Server</title>
   </head>

   <body>
      <?php
         $dbhost = '127.0.0.1';
         $dbuser = 'home_temp';
         $dbpass = 'password';
         // $conn = mysql_connect($dbhost, $dbuser, $dbpass);
         $db = new mysqli($dbhost,$dbuser,$dbpass,'mysql', 3307);
      
         if(! $db ) {
            die('Could not connect: ' . mysql_error());
         }
         
         echo 'Connected successfully';
         // mysql_close($conn);
      ?>
   </body>
</html>