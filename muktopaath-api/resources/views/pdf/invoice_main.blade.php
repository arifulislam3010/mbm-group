<!DOCTYPE html>
<html>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
  <table width="100%">
    <tr>
      <td width="70%"><b>TTIS ID Card Generate</b><br>
  Batch No- Muktopaath/Subject-meri/Year-2020/Mode-CEDP</td>
      <td width="30%"><b>Print Date:</b> <?=date("d-F-Y");?></td>
    </tr>
  </table>
<div class="row">
@foreach($data['data'] as $key => $value)
    <div class="column">  

      <div class="card">
        <table width="100%">
          <tr>
            <td width="10%"><div>
      <img class="img-left" src="https://seeklogo.com/images/N/national-university-of-bangladesh-logo-105B0CD1FD-seeklogo.com.png" height="30" width="30">
    </div></td>
            <td width="80%" align="center">
              <div class="center-content"><b>{{$data['institution']['institution_name']}}</b>
          <br><b>Dhaka, Bangladesh</b><br>
<!--          <b style="font-size: 12px; white-space: nowrap; float: left;">College Education Development Project (CEDP)</b><br> -->
          <div id="who">PARTICIPANT</div>
            </td>
            <td width="10%"><div>
        <img  class="img-right" src="https://www.cedp.gov.bd/wp-content/uploads/2017/07/logo-1.png" height="30" width="30">
    </div></td>
          </tr>
        </table>
         <div class="personal">
          <table width="100%">
            <tr>
              <td width="70%" style="white-space: nowrap;"><label ><b>Subject: </b>{{$data['batch']}}</label></td>
              <td width="30%"  style="white-space: nowrap;"><label ><b>Batch: </b>{{$data['batch']}}</label></td>
            </tr>
          </table>
          </div>
          <div class="personal">
            <table width="100%">
              <tr>
                <td style="white-space: nowrap;">Name: {{$value->name}}</td>
              </tr>
              <tr>
                <td width="80%">Contact:
                  @if(isset($value->email))
                  {{$value->email}}
                  @else
                  {{$value->email_or_phone}}
                  @endif
                </td>
                <td width="20%" style="white-space: nowrap;">Other</td>
              </tr>
              <tr>
                <td colspan="2">College Name: kaise ho tum</td>
              </tr>
            </table>
            <br>
            
          </div>
        <div class="qr">
          <?php 
           $qrcode=SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate('A basic example of QR code!');
           $code = (string)$qrcode;
           echo substr($code,38);
           ?>

        </div>
          <div class="footer">
            <table width="100%" class="foot">
              <tr>
                <td width="60%;"><h6>Valid 03/03/2020-03/03/2023</h6></td>
                <td width="40%" style="float: right;"><h6 >Director (Training)</h6></td>
              </tr>
            </table>
          </div>
        
    </div>
    </div>

    @endforeach
    </div>

</div>
</body>
<style type="text/css">
  @page {
  margin-left: 15px;
  margin-right: 15px;

}

h5{
  position: absolute;
  margin-bottom: 20px;
}
.footer h6{
  margin-left: 4px;
  font-size: 12px;
  margin-bottom: 0;
  font-weight: 400;
}

h6{
  font-weight: 500;

}
.personal{
  margin-top: 10px;
  margin-left: 10px;
  font-size: 12px;
}
.img-left{
  margin-left: 5px;
  float: left;
}

.img-right{
  margin-right: 10px;
  float: right;
}
#who{
  border: 1px solid black;
  padding:  2px;
  width:  70%;
  margin: auto;
}

.center-content{
  margin-top: 10px;
  text-align: center;

}
.personal > table tr > td{
    font-size: 10px;
}
.qr{
  width:  50%;
  text-align: center;
  margin: 0 auto;
  padding: 2px;
}

  .card{
    border: double;
    width: 95%;
  }
.column {width:50%; float: left; margin-top: 8px;}
.col12 {width:100%;float:left;}
</style>
</html>