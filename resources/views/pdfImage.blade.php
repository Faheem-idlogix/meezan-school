<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Challan</title>
    <style>
        /* Add your CSS styles for formatting the fee challan here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .challan {
            /* width: 210mm;  */
            margin: 0 auto;
            background-color: #ffffff;
            /* Add some padding and margins to make the challan look more attractive */
            padding: 20px 40px;
            /* margin-top: 20px; */
        }
        .challan h4 {
            text-align: center;
            /* //margin-bottom: 10px; */
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #000;
            font-size: 80%;
            
        }
        .data{
             /* border: 1px solid #000; */
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        .finee {
            background-color: #ffcccc;
            border: 1px solid #cc0000;
            padding: 5px;
            color: #cc0000;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
        }
        .copy {
            text-align: center;
            margin-top: 2px;
        }
        h3{
            text-align: center;
            

        }
        .fee-info {
            display: flex;
            justify-content: space-between;
            /* margin-top: 10px; */
            font-weight: bold;

        }
        .fee-info-item {
            border: 0px solid #cc0000;
            font-weight: bold;
            font-size: 80%;

        }
        .note{
            font-size: 60%;
        }
        .address{
            text-align: center;
            font-size: 70%;

        }
        .challan .note {
            page-break-after: always; /* Page break after the footer */
        }
        .total{
            font-size: 80%;

        }
    </style>
</head>
<body>
    <!-- School Copy -->
    <div class="challan">
      
                    <div class="row col-md-12">
                        <img src="{{ public_path('img/logo/school_logo.jpg') }}" style="width: 50px; height: 50px; margin-right: 20px;">
                        <h3 class="school-name">Meezan School System</h3>
                        <p class="address">Address: nai wala bangla. Contact No : 0301999000</p>
                        <h4 class="heading">Fee Challan - School Copy</h4>
                    </div>
        {{-- </div> --}}
        <!-- Fee Information -->
        <table class="fee-info-item">
        <tr>
            <td class="fee-info-item">Voucher No: 12345</td>
            <td class="fee-info-item">Month: September 2023</td>
            <td class="fee-info-item">Issue Date: September 1, 2023</td>
            <td class="fee-info-item">Due Date: September 15, 2023</td>
        </tr>
        </table>
        <table>
          
            <tr>
                <td><strong>Student Name:</strong> John Doe</td>
                <td><strong>Test Series Charges:</strong> John Doe</td>
                <td><strong>Monthly Fee :</strong> John Doe</td>


            </tr>
            <tr>
                <td><strong>Father Name:</strong> 12345</td>
                <td><strong>Exam Charges:</strong> 12345</td>
                <td><strong>Discount:</strong> 12345</td>

            </tr>
            <tr>
                <td><strong>Contact No:</strong> 12345</td>
                <td><strong>Arrears:</strong> 12345</td>
                <td><strong>Fine :</strong> 12345</td>
            </tr>
        </table>
        <!-- Add fine details if applicable -->
    
        <div class="footer row">
            <p class="total"><span style="text-align: right; display: block;"><b>Total : 600</b></span></p>
                         
        </div>
    </div>  
  

    <!-- Student Copy 1 -->
    <div class="challan">
        <div class="row col-md-12">
            <img src="{{ public_path('img/logo/school_logo.jpg') }}" style="width: 50px; height: 50px; margin-right: 20px;">
            <h3 class="school-name">Meezan School System</h3>
            <p class="address">Address: nai wala bangla. Contact No : 0301999000</p>
            <h4 class="heading">Fee Challan - Student Copy</h4>
        </div>
        <table class="fee-info-item">
            <tr>
                <td class="fee-info-item">Voucher No: 12345</td>
                <td class="fee-info-item">Month: September 2023</td>
                <td class="fee-info-item">Issue Date: September 1, 2023</td>
                <td class="fee-info-item">Due Date: September 15, 2023</td>
            </tr>
            </table>
        <table>
            <tr>
                <td><strong>Student Name:</strong> John Doe</td>
                <td><strong>Test Series Charges:</strong> John Doe</td>
                <td><strong>Monthly Fee :</strong> John Doe</td>


            </tr>
            <tr>
                <td><strong>Father Name:</strong> 12345</td>
                <td><strong>Exam Charges:</strong> 12345</td>
                <td><strong>Discount:</strong> 12345</td>

            </tr>
            <tr>
                <td><strong>Contact No:</strong> 12345</td>
                <td><strong>Arrears:</strong> 12345</td>
                <td><strong>Fine :</strong> 12345</td>
            </tr>
        </table>
        <!-- Add fine details if applicable -->
        <div class="footer row">
            <p class="total"><span style="text-align: right; display: block;"><b>Total : 600</b></span></p>
            <p class="note">Note: 20 PKR (if paid after due date) </p>
                         
        </div>
        </div>

    <!-- Student Copy 2 -->

     <!-- School Copy -->
     <div class="challan">
      
        <div class="row col-md-12">
            <img src="{{ public_path('img/logo/school_logo.jpg') }}" style="width: 50px; height: 50px; margin-right: 20px;">
            <h3 class="school-name">Meezan School System</h3>
            <p class="address">Address: nai wala bangla. Contact No : 0301999000</p>
            <h4 class="heading">Fee Challan - School Copy</h4>
        </div>
{{-- </div> --}}
<!-- Fee Information -->
<table class="fee-info-item">
<tr>
<td class="fee-info-item">Voucher No: 12345</td>
<td class="fee-info-item">Month: September 2023</td>
<td class="fee-info-item">Issue Date: September 1, 2023</td>
<td class="fee-info-item">Due Date: September 15, 2023</td>
</tr>
</table>
<table>

<tr>
    <td><strong>Student Name:</strong> John Doe</td>
    <td><strong>Test Series Charges:</strong> John Doe</td>
    <td><strong>Monthly Fee :</strong> John Doe</td>


</tr>
<tr>
    <td><strong>Father Name:</strong> 12345</td>
    <td><strong>Exam Charges:</strong> 12345</td>
    <td><strong>Discount:</strong> 12345</td>

</tr>
<tr>
    <td><strong>Contact No:</strong> 12345</td>
    <td><strong>Arrears:</strong> 12345</td>
    <td><strong>Fine :</strong> 12345</td>
</tr>
</table>
<!-- Add fine details if applicable -->

<div class="footer row">
<p class="total"><span style="text-align: right; display: block;"><b>Total : 600</b></span></p>
             
</div>
</div>  


<!-- Student Copy 1 -->
        <div class="challan">
        <div class="row col-md-12">
        <img src="{{ public_path('img/logo/school_logo.jpg') }}" style="width: 50px; height: 50px; margin-right: 20px;">
        <h3 class="school-name">Meezan School System</h3>
        <p class="address">Address: nai wala bangla. Contact No : 0301999000</p>
        <h4 class="heading">Fee Challan - Student Copy</h4>
        </div>
        <table class="fee-info-item">
        <tr>
            <td class="fee-info-item">Voucher No: 12345</td>
            <td class="fee-info-item">Month: September 2023</td>
            <td class="fee-info-item">Issue Date: September 1, 2023</td>
            <td class="fee-info-item">Due Date: September 15, 2023</td>
        </tr>
        </table>
        <table>
        <tr>
            <td><strong>Student Name:</strong> John Doe</td>
            <td><strong>Test Series Charges:</strong> John Doe</td>
            <td><strong>Monthly Fee :</strong> John Doe</td>


        </tr>
        <tr>
            <td><strong>Father Name:</strong> 12345</td>
            <td><strong>Exam Charges:</strong> 12345</td>
            <td><strong>Discount:</strong> 12345</td>

        </tr>
        <tr>
            <td><strong>Contact No:</strong> 12345</td>
            <td><strong>Arrears:</strong> 12345</td>
            <td><strong>Fine :</strong> 12345</td>
        </tr>
        </table>
        <!-- Add fine details if applicable -->
        <div class="footer row">
        <p class="total"><span style="text-align: right; display: block;"><b>Total : 600</b></span></p>
        <p class="note">Note: 20 PKR (if paid after due date) </p>
                    
        </div>
        </div>
</body>
</html>
