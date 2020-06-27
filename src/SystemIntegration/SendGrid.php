<?php
    namespace teamicon\apikit\SystemIntegration;

    use \PHPMailer\PHPMailer\{PHPMailer, SMTP};
    use \teamicon\apikit\Exceptions\InvalidArgumentException;

    require_once(__DIR__ . "/PhpMailer/PHPMailer.php");
    require_once(__DIR__ . "/PhpMailer/SMTP.php");
    require_once(__DIR__ . "/PhpMailer/Exception.php");

    class SendGrid {
        public static string $ErrorMessage;

        public static function Send(string $apikey, string $from, string $fromName, array $to, string $subject, string $body, bool $isDebug = false) : bool {
            if(!filter_var($from, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException("from", $from, "invalid format");
            if(trim($fromName) == "") $fromName = $from;
            if(trim($apikey) == "") throw new InvalidArgumentException("apikey", "n.d.", "is empty");
            $mail = new PHPMailer(true);
            if($isDebug) $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = "smtp.sendgrid.net";
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->Username = "apikey";
            $mail->Password = $apikey;
            $mail->Priority = 1;
            $mail->From = $from;
            $mail->FromName = $fromName;
            for($i = 0; $i < count($to); $i++) $mail->AddAddress($to[$i]);
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = "Abilitare il client alla lettura della mail in formato HTML. Please, your client require to allow the use of HTML.";
            $res = $mail->Send();
            self::$ErrorMessage = !$res ? $mail->ErrorInfo : "";
            return $res;
        }

        public static function LoadTemplate(string $url, array $fields = []) : string {
            if(trim($url) == "" || !file_exists($url)) throw new InvalidArgumentException("url", $url == "" ? "n.d." : $url, "file not exists or field is empty");
            $body = file_get_contents($url);
            foreach(array_keys($fields) as $key) $body = str_replace("[**" . strtoupper($key) . "**]", $fields[$key], $body);
            return $body;
        }
    }
?>