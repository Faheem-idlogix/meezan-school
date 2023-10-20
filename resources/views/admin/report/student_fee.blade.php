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
            /* text-align: center; */
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
            font-size: 70%;

        }
        .challan .note {
            page-break-after: always; /* Page break after the footer */
        }
        .total{
            font-size: 80%;

        }
        /* CSS style for the horizontal line */
        hr {
            border: none; /* Remove the default border */
            border-top: 2px solid #555; /* Set the desired line thickness and color */
            margin: 50px 0; /* Add some space above and below the line */
        }
        .container {
                display: block;
                width: 100%;
            }
            /* Apply styles to the table */
            table.header {
            border: none; /* Remove the border */
            width: 95%;
            margin: 0 auto; /* Center the table horizontally */

            }

            /* Apply styles to the table cells (th elements) */
            table.header td {
                border: none;
            padding: 5px; /* Add padding for spacing */
            }

            /* Apply styles to the logo and info columns */
            .logo {
            width: 50px;
            text-align: center; /* Center align the content within the cell */
            }

            .info {
            text-align: left; /* Align the content to the left within the cell */
            }

            /* Apply styles to the school name */
            .school-name {
            font-size: 18px; /* Adjust the font size as needed */
            margin-left: 100px; /* Remove default margin */
            }

            /* Apply styles to the address */
            .address {
            font-size: 12px; /* Adjust the font size as needed */
            margin-left: 30px; /* Remove default margin */
            margin-top: 10px;

            }

            
            



    </style>
</head>
<body>
    <!-- School Copy -->
    @foreach ($data as $item)
    
        
    <div class="challan">
      
        <table class="header">
            <tr class="col-md-6">
              <td><img src="{{ public_path('img/logo/school_logo.jpg') }}" style="width: 50px; height: 50px;"> </td>
              <td><h3 class="school-name">Meezan School System</h3>
                <p class="address">Address: Chak no 149/9.L, Sahiwal . Contact No : 03457423031</p>
    
            </td>
            </tr>
          </table>
          <div class="row col-md-12">

         <h4 class="heading">Fee Challan - School Copy</h4>
          </div>
        {{-- </div> --}}
        <!-- Fee Information -->
        <table class="fee-info-item">
        <tr>
            <td class="fee-info-item">Voucher No: {{$item->voucher_no}}</td>
            <td class="fee-info-item">Month: {{$item->fee_month}}</td>
            <td class="fee-info-item">Issue Date: {{$item->issue_date}}</td>
            <td class="fee-info-item">Due Date: {{$item->submit_date}}</td>
        </tr>
        </table>
        <table>
          
            <tr>
                <td><strong>Student Name:</strong> {{$item->student->student_name}}</td>
                <td><strong>Test Series Charges:</strong> {{$item->test_series_charges}}</td>
                <td><strong>Monthly Fee :</strong> {{$item->academic_fee}}</td>


            </tr>
            <tr>
                <td><strong>Father Name:</strong> {{$item->student->father_name}}</td>
                <td><strong>Exam Charges:</strong> {{$item->exam_charges}}</td>
                <td><strong>Stationery Charges:</strong> {{$item->stationery_charges}}</td>

            </tr>
            <tr>
                <td><strong>Contact No:</strong> {{$item->student->contact_no ?? ''}}</td>
                <td><strong>Arrears:</strong> {{$item->arrears}}</td>
                <td><strong>Fine :</strong> {{$item->fine}}</td>
            </tr>
        </table>
        <!-- Add fine details if applicable -->
    
        <div class="footer row">
            <p class="total"><span style="text-align: right; display: block;"><b>Total : {{$item->total_fee}}</b></span></p>
                         
        </div>
      <!-- Line break -->
      <hr>


    <!-- Student Copy 1 -->

        <table class="header">
            <tr class="col-md-6">
            <td><img src="{{ public_path('img/logo/school_logo.jpg') }}" style="width: 50px; height: 50px;"> </td>
            <td><h3 class="school-name">Meezan School System</h3>
                <p class="address">Address: Chak no 149/9.L, Sahiwal . Contact No : 03457423031</p>

            </td>
            </tr>
        </table>
      <div class="row col-md-12">

     <h4 class="heading">Fee Challan - School Copy</h4>
      </div>
        <table class="fee-info-item">
            <tr>
                <td class="fee-info-item">Voucher No: {{$item->voucher_no}}</td>
                <td class="fee-info-item">Month: {{$item->fee_month}}</td>
                <td class="fee-info-item">Issue Date: {{$item->issue_date}}</td>
                <td class="fee-info-item">Due Date: {{$item->submit_date}}</td>
            </tr>
            </table>
        <table>
            <tr>
                <td><strong>Student Name:</strong> {{$item->student->student_name}}</td>
                <td><strong>Test Series Charges:</strong> {{$item->test_series_charges}}</td>
                <td><strong>Monthly Fee :</strong> {{$item->academic_fee}}</td>


            </tr>
            <tr>
                <td><strong>Father Name:</strong> {{$item->student->father_name}}</td>
                <td><strong>Exam Charges:</strong> {{$item->exam_charges}}</td>
                <td><strong>Stationery Charges:</strong> {{$item->stationery_charges}}</td>

            </tr>
            <tr>
                <td><strong>Contact No:</strong> {{$item->student->contact_no ?? ''}}</td>
                <td><strong>Arrears:</strong> {{$item->arrears}}</td>
                <td><strong>Fine :</strong> {{$item->fine}}</td>
            </tr>
        </table>
        <!-- Add fine details if applicable -->
        <div class="footer row">
            <p class="total"><span style="text-align: right; display: block;"><b>Total : {{$item->total_fee}}</b></span></p>
            <p class="note">Note: 20 PKR (if paid after due date) </p>
                         
        </div>
    </div>
        @endforeach


    <!-- Student Copy 2 -->

     <!-- School Copy -->
   
</body>
</html>
