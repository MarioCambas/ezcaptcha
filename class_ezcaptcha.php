<?php
session_start();
class captcha{
    var $url;
    var $captchaParams=array(
        'randFontSizes'=>true,
        'randCaptchaLeng'=>true,
        'randUppercase'=>true,
        'randPattern'=>true,
        'randColor'=>true,
        'randAngle'=>true);
    var $font=array('./VeraBd.ttf','./FFF_Tusj.ttf');
    private function Captcha(){
        $_SESSION["captcha_code"]='';
        $random_alpha = md5(rand());
        $leng=6;
        if($this->captchaParams['randCaptchaLeng']){ $leng=rand(6,8);}
        $fontSize=20;
        $captcha_code = substr($random_alpha, 0, $leng);
        $imagelenght=$leng*$fontSize+10;
        $imageheight=$fontSize+24;
        $baseLineText=$imageheight-10;
        $target_layer = imagecreatetruecolor($imagelenght,$imageheight);
        if($this->captchaParams['randColor']){$captcha_background = imagecolorallocate($target_layer, 255, rand(0,255), 119);
        } else {$captcha_background = imagecolorallocate($target_layer, 255, 168, 119);}
        imagefill($target_layer,0,0,$captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        $grey = imagecolorallocate($target_layer, 128, 128, 128);
        $functions[0]=function ($code){return $code;};
        $functions[1]=function ($code){return strtoupper($code);};
        if($this->captchaParams['randPattern']){
            for ($j=0;$j<10;$j++){
                $x1=rand(0,$imagelenght);
                $y1=rand(0,$imageheight);
                $x2=rand(0,$imagelenght);
                $y2=rand(0,$imageheight);
                $x3=rand(0,$imagelenght);
                $y3=rand(0,$imageheight);
                $points=array($x1,$y1,$x2,$y2,$x3,$y3);
                imagepolygon($target_layer,$points,3,$captcha_text_color);
            }
        }
        for($i=0;$i<strlen($captcha_code);$i++){
            $code=$captcha_code[$i];
            if($this->captchaParams['randUppercase']){
                $code=$functions[rand(0,1)]($captcha_code[$i]);
            }
            $_SESSION["captcha_code"].=$code;
            $angl=array(0,15,-15);
            $ang=0;
            if($this->captchaParams['randAngle']){$ang=$angl[rand(0,2)];}
            // Adding shadow...
            $sizeOftheFont=$fontSize;
            if($this->captchaParams['randFontSizes']){ $sizeOftheFont=rand(10,$fontSize+5);}
            $font=$this->font[rand(0,count($this->font)-1)];
            imagettftext($target_layer, $sizeOftheFont, $ang, 7+$i*$fontSize, $baseLineText+2, $grey, $font, $code);
            // Adding text...
            imagettftext($target_layer, $sizeOftheFont, $ang, 5+$i*$fontSize, $baseLineText, $captcha_text_color, $font, $code);
        }
        header("Content-type: image/jpeg");
        imagejpeg($target_layer);
    }

    private function setHtml(){
        return '
        <style>
            .btnRefresh{background-color:#8B8B8B;border:0;padding:7px 10px;color:#FFF;float:left;}
            .labelcaptcha{color:#FF0000;display:none;}
            .captcha{display: block; padding-right: 42.5px;border-radius: 0;display: inline-block;background: none;border: 1.5px solid #BBBBBB;width: 100%;outline: none;padding: 10px 15px 10px 15px;font-size: .9em;color: #212121;margin-bottom: 0px;}
        </style>
        <div class="form-group has-feedback">
            <label class="labelcaptcha" for="captcha"></label><input type="captcha" name="captcha" id="captcha" autocomplete="off" class="captcha" placeholder="Captcha">
            <img id="captcha_code" src="http://www.mchsoft.com/system/functions/ajax/class_captcha.php">
            <button type="button" name="btnrefreshCaptcha" class="btnRefresh" onclick="refreshCaptcha();">Refresh Captcha</button>
        </div>'.$this->setJS();
    }
    private function setJS(){
       return "
        <script>
            $(\"#captcha\").blur(function(){
                $(\"#captcha\").css('background-color','#FFFFFF');
                $(\".labelcaptcha\").css('display','none');
                $(\".labelcaptcha\").html('');
            });
            function validCaptcha() {
                valid=true;
        	    if(!$(\"#captcha\").val()) {
                    $(\"#captcha\").css('background-color','#FFBDBD');
                    $(\".labelcaptcha\").css('display','block');
                    $(\".labelcaptcha\").html('You most type the captcha');
        		    valid = false;
            	}
            	return valid;
            }
            function refreshCaptcha() {
                $(\"#captcha_code\").attr('src','".$this->url."');
            }
        </script>";
    }
    function __construct(){
        $this->url='http://'.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', '/var/chroot'.__FILE__);
        if($_SERVER['HTTPS']){
            $this->url='https://'.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', '/var/chroot'.__FILE__);
        }
        if (isset($_GET['getHtml'])){
            echo $this->setHtml();
        } else {
            $this->Captcha();
        }
    }
}
$captcha = new captcha();
?>
