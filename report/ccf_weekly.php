<?php


$servername = "localhost";
$username = "root";
$password = "";
$database = "neustmrdb";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html>
<title>Requisition and Issue Slip</title>
<link href="style.css" rel="stylesheet">

<body>

    <div class="body-div">

        <div class="header-div" style="font-family: 'Times New Roman'">
            <img src="../img/header-logo.png" alt="" width="50%">
            <br>
               <b> CENCUS OF CAMPUS <br>
                SUMACAB CAMPUS</b>
                <br>

                <input type="text" class="weekly_report">
              
           
            
        </div>

        <div style="width:100%; margin:auto;" class="mainbody">
            <table style="width:100%; ">
                <thead>

                    <tr>
                        <th style="width:13%">CASES</th>
                        <?PHP
                        // Display Department
                        $stmt = $conn->prepare("SELECT * FROM department WHERE 1");
                        $stmt->execute();

                        $result = $stmt->get_result();
                        $count = mysqli_num_rows($result);

                        $count += 1;

                        while ($row = $result->fetch_assoc()) { ?>
                            <th class="rotate_text wbold"><b><?php echo htmlspecialchars($row['department_code']) ?></b>
                            </th>
                            <?php
                        }
                        ?>
                        <th class="rotate_text" style="font-size: 10px;">Other <br> Campus</th>
                        <th class="rotate_text red">Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    
                    // Display CASES
                    $stmt = $conn->prepare("SELECT * FROM common_health_issues WHERE 1");
                    $stmt->execute();

                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) { ?>

                        <tr>
                            <td style="height: 2.5vh; text-align:left" class="wbold"><?php echo htmlspecialchars($row['issue_name']) ?></td>
                            <?php
                            for ($i = 1; $i <= $count; $i++) {

                                echo "<td></td>";
                            }

                            ?>

                            <td></td>
                        </tr>


                        <?php

                    }

                    ?>

                    <tr>
                        <td class="red wbold" style="text-align:right">TOTAL</td>
                        <?php for ($i = 0; $i <= $count; $i++) {

                            echo "<td></td>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <td class="red wbold" style="text-align:right">Total number of cases</td>
                        <td colspan="<?php echo $count +1; ?>"></td>
                    </tr>

                    </tbody>
            </table>



            <div style="width:100%; margin:auto;float:right;">
                <br>
                        <label style="float:right; margin-right:20%">Prepared By:</label>
                        <br>
                        <input type="text"  class="prepared">
            </div>
        </div>

        <footer>

        </footer>
    </div>  
    <br>
 <footer>

        NEUST-HSU-F003 <br>
        Rev.01 (07.01.23)
        </footer>
</body>


<script>
    function PrintElem(elem) {
        window.print();
    }

    function Popup(data) {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        //mywindow.document.write('<html><head><title>my div</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        //mywindow.document.write('</head><body class="skin-black" >');
        var printButton = document.getElementById("printpagebutton");
        //Set the print button visibility to 'hidden' 
        printButton.style.visibility = 'hidden';
        mywindow.document.write(data);
        //mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();

        printButton.style.visibility = 'visible';
        mywindow.close();

        return true;
    }
</script>

</html>