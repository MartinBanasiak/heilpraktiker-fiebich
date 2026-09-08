<?
error_reporting(0);
//header("Content-type: application/pdf; charset=utf-8");
//Inhalt des digitalen Gutscheins holen
$query = "SELECT shop_digital_coupon.* ,shop_coupon_line.coupon_code
		  FROM shop_digital_coupon
		  LEFT JOIN shop_coupon_line ON shop_coupon_line.id = shop_digital_coupon.shop_coupon_line_id
		  WHERE shop_digital_coupon.id =".$dc_id." 
		  LIMIT 1";
$result = mysqli_query($GLOBALS['mysql_con'],$query);
if (@mysqli_num_rows($result)  == 1)
{
    $a4_width_mm = 210;
    $a4_height_mm = 297;

    $dc = mysqli_fetch_assoc($result);
    define('FPDF_FONTPATH','font/');
    $realpath_fpdf = realpath("../../plugins/fpdf/fpdf.php");
    require($realpath_fpdf);

    $realpath_img = realpath("../../userdata/dcshop/dc_background/");

    $logo = "logo.jpg";
    if (file_exists($realpath_img . DIRECTORY_SEPARATOR .strtolower($GLOBALS["shop"]["code"]) . "_logo.jpg")) {
        $logo = strtolower($GLOBALS["shop"]["code"]) . "_logo.jpg";
    }

    $logofile = $files = glob($realpath_img . DIRECTORY_SEPARATOR . $logo, GLOB_BRACE);
    if(strlen($logofile)>0){
        //$logofile=$logofiles[0];
        $logo_dimensions = img_size_mm($logofile,72);
        $logo_width_mm=$logo_dimensions[0];
        $logo_height_mm=$logo_dimensions[1];
        if($logo_width_mm > 0 && $logo_height_mm > 0){
            $logo_y_pos_mm = 10;
            $factor=0.8;
            $logo_x_pos_mm = (($a4_width_mm / 2) - ($factor*($logo_width_mm / 2)));
        }
    }



    $pdf=new FPDF();
    $pdf->Open();
    $pdf->AddPage(P,A4);
    $pdf->SetAutoPageBreak(true,5);

    if($logo_x_pos_mm>0 && $logo_y_pos_mm>0){
        $logo_x_pos_mm = 0;
        $pdf->Image($logofile,$logo_x_pos_mm,$logo_y_pos_mm,$logo_width_mm,0);
    } else {
        $pdf->Image("../../userdata/dcshop/dc_background/" . $logo,80,5,50,0);
    }
    $pdf->Image("../../".$GLOBALS["shop_setup"]["dc_image_config"][2]["path"].'/'.$GLOBALS["shop_language"]["digital_coupon_background_".$dc["background_image"].""],7,35,195,103); // Hintergrundbild festlegen

    $pdf->SetTextColor(100, 100, 100);
    $pdf->SetDrawColor(100, 100, 100);
    $pdf->SetFillColor(3, 82, 151);

    $pdf->SetXY(7,148);
    $pdf->SetFont('Arial','',24);
    $pdf->MultiCell(0,0,utf8_decode($GLOBALS["tc"]["your_coupon"]),0,'L');

    $pdf->SetXY(117,148);
    $pdf->SetFont('Arial','',24);
    $pdf->MultiCell(0,0,utf8_decode($GLOBALS["tc"]["coupon_value"]),0,'L');

    $pdf->SetXY(140,148);
    $pdf->SetFont('Arial','B',24);
    $pdf->MultiCell(0,0,utf8_decode(number_format($dc["amount"],2,',','')." Euro"),0,'L');

    $pdf->SetXY(117,160);
    $pdf->SetFont('Arial','B',14);
    $pdf->MultiCell(0,0,utf8_decode($GLOBALS["tc"]["coupon_code"].$dc["coupon_code"]),0,'L');

    $pdf->SetXY(117,170);
    $pdf->SetFont('Arial','',14);
    $pdf->MultiCell(0,0,utf8_decode($GLOBALS["tc"]["coupon_valid_to"]. date('d.m.Y',strtotime("today +2 years"))),0,'L');

    $pdf->SetFont('Arial','',14);
    $pdf->SetXY(7,160);
    $pdf->MultiCell(70,1,utf8_decode($GLOBALS["tc"]["from_name"].": ".$dc["from_name"]),0,'L');

    $pdf->SetXY(7,170);
    $pdf->MultiCell(70,0,utf8_decode($GLOBALS["tc"]["to_name"].":  ".$dc["to_name"]),0,'L',0);

    $pdf->SetXY(7,180);
    $pdf->MultiCell(0,0.1,"",0,'L',1);

    $pdf->SetFont('Arial','',14);
    $pdf->SetXY(7,185);
    $dc["message"] = str_replace("<br/>","\n",$dc["message"]);
    $pdf->MultiCell(0,5,utf8_decode($dc["message"]),0,'L');

    $pdf->SetXY(7,250);
    $pdf->MultiCell(0,0.1,"",0,'L',1);

    $pdf->SetXY(7,255);
    $pdf->SetFont('Arial','',9);

    $pdf->MultiCell(0,5,iconv('UTF-8', 'windows-1252',html_entity_decode($GLOBALS["tc"]["dc_hint"])),0,'L');

    $filename = $GLOBALS["tc"]["coupon"]."-".$dc["coupon_code"].".pdf";
    $pdf->Output("../../userdata/private/dc_pdf/".$filename,F);
    $filepath = "/userdata/private/dc_pdf/".$filename;
}

?>