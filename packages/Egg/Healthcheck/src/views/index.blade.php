<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=TIS-620">
        <title>:: Health Check ::</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </head>
    <body>
    <div class="container">
    <h3>:: Health Check :: <span style='color:red'>Share Service API</span></h3><hr/>
    <div class="row">
        <div class="span5">
            <table class="table  table-condensed">
              <thead>
                  <tr style='background-color:red;'>
                      <th style='color:white;'>No.</th> 
                      <th style='color:white;'>Type</th>
                      <th style='color:white;'>Service</th>
                      <th style='color:white;'>URL</th>
                      <th style='color:white;'>Time</th>  
                      <th style='color:white;'>Status</th>
                      <th style='color:white;'>Message</th>                                         
                  </tr>
              </thead>   
              <tbody>
                <?php
                  $count = 0 ;  
                  foreach ($data as $key => $value): 
                  $count ++ ;
                ?>
                <tr>
                    <td><?php echo $count; ?></td>
                    <td><?php echo $value['type']; ?></td>
                    <td><?php echo $value['checkService']; ?></td>
                    <td><?php echo $value['url']; ?></td>
                    <td><?php echo $value['setTime']; ?></td>
                    <td>
                        <?php
                            if ($value['setStatus'] === TRUE) {
                                echo 'OK';
                            } else {
                                echo 'Error';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if ($value['setStatus'] === TRUE) {
                                echo "<span class='label label-success'>OK</span>";
                            } else {
                                echo  "<span class='label label-danger'>".$value['setMessage']."</span>";
                            }
                        ?>
                    </span>
                    </td>                                       
                </tr>
               <?php endforeach; ?>
    </body>
</html>