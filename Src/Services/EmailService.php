<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 11:16
 */

namespace famoser\phpFrame\Services;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\WAdminCrm\Libraries\PhpMailerHook;
use PHPMailer;

class EmailService extends ServiceBase
{
    private $hook;

    public function __construct()
    {
        parent::__construct();
        include_once RuntimeService::getInstance()->getFrameworkLibraryDirectory() . DIRECTORY_SEPARATOR . "PHPMailer/PHPMailerAutoload.php";
    }

    public function sendEmailFromServer($subject, $message, $emails, $names = '', $attachments = null, $ccEmails = null, $ccNames = null)
    {
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->SetLanguage(LocaleService::getInstance()->getActiveLang()->getName());
        $mail->isSMTP();
        $mail->Host = $this->getConfig("ServerHost");
        $mail->SMTPAuth = true;
        $mail->Username = $this->getConfig("ServerUsername");
        $mail->Password = $this->getConfig("ServerPassword");
        $mail->SMTPSecure = $this->getConfig("ServerSecure");
        $mail->Port = $this->getConfig("ServerPort");

        $mail->From = $this->getConfig("SenderEmail");
        $mail->FromName = $this->getConfig("SenderName");

        if (!is_array($emails)) {
            $mail->addAddress($emails, $names);
        } else {
            for ($i = 0; $i < count($emails); $i++) {
                if (is_array($names) && count($names) > $i)
                    $mail->addAddress($emails[$i], $names[$i]);
                else
                    $mail->addAddress($emails[$i]);
            }
        }

        if ($ccEmails != null) {
            if (!is_array($ccEmails)) {
                $mail->addAddress($ccEmails, $names);
            } else {
                for ($i = 0; $i < count($ccEmails); $i++) {
                    if (is_array($names) && count($names) > $i)
                        $mail->addAddress($ccEmails[$i], $names[$i]);
                    else
                        $mail->addAddress($ccEmails[$i]);
                }
            }
        }

        $mail->addReplyTo($this->getConfig("RespondToEmail"), $this->getConfig("RespondToName"));

        if ($attachments != null)
            for ($i = 0; $i < count($attachments); $i++) {
                $mail->AddAttachment($attachments[$i]);
            }

        $mail->isHTML(true);

        $mail->Subject = $subject;

        $mail->Body = nl2br($message);
        $mail->AltBody = $message;

        if ($mail->send()) {
            return true;
        }

        LogHelper::getInstance()->logError($mail->ErrorInfo);
        return false;
    }
}