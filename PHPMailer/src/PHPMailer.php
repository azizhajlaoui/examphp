<?php
namespace PHPMailer\PHPMailer;

class PHPMailer {
    public $Host;
    public $Port;
    public $SMTPAuth;
    public $Username;
    public $Password;
    public $SMTPSecure;
    public $ErrorInfo;
    public $Subject;
    public $Body;
    public $AltBody;
    private $to = [];
    private $from;
    private $fromName;
    private $isHTML = false;
    private $isSMTP = false;
    private $exceptions;
    private $socket;

    public function __construct($exceptions = null) {
        $this->exceptions = $exceptions;
    }

    public function isSMTP() {
        $this->isSMTP = true;
    }

    public function setFrom($email, $name = '') {
        $this->from = $email;
        $this->fromName = $name;
    }

    public function addAddress($email, $name = '') {
        $this->to[] = ['email' => $email, 'name' => $name];
    }

    public function isHTML($isHtml = true) {
        $this->isHTML = $isHtml;
    }

    private function connect() {
        $this->socket = fsockopen($this->Host, $this->Port, $errno, $errstr, 30);
        if (!$this->socket) {
            throw new Exception("Failed to connect to SMTP server: $errstr ($errno)");
        }
        $this->getResponse();
    }

    private function getResponse() {
        $response = '';
        while ($str = fgets($this->socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == ' ') break;
        }
        return $response;
    }

    private function sendCommand($command) {
        fwrite($this->socket, $command . "\r\n");
        return $this->getResponse();
    }

    private function startTLS() {
        $response = $this->sendCommand("STARTTLS");
        if (substr($response, 0, 3) != '220') {
            throw new Exception("STARTTLS failed: $response");
        }
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    }

    private function authenticate() {
        $this->sendCommand("EHLO " . $this->Host);
        if ($this->SMTPSecure === 'tls') {
            $this->startTLS();
            $this->sendCommand("EHLO " . $this->Host);
        }
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->Username));
        $this->sendCommand(base64_encode($this->Password));
    }

    public function send() {
        if (!$this->isSMTP) {
            throw new Exception('SMTP is not configured');
        }

        try {
            $this->connect();
            $this->authenticate();

            // Set sender
            $this->sendCommand("MAIL FROM:<{$this->from}>");

            // Set recipients
            foreach ($this->to as $recipient) {
                $this->sendCommand("RCPT TO:<{$recipient['email']}>");
            }

            // Send email data
            $this->sendCommand("DATA");
            
            $headers = "MIME-Version: 1.0\r\n";
            if ($this->isHTML) {
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            }
            $headers .= "From: {$this->fromName} <{$this->from}>\r\n";
            $headers .= "Subject: {$this->Subject}\r\n\r\n";
            
            $this->sendCommand($headers . $this->Body);
            $this->sendCommand(".");

            $this->sendCommand("QUIT");
            fclose($this->socket);
            return true;
        } catch (Exception $e) {
            if ($this->socket) {
                fclose($this->socket);
            }
            $this->ErrorInfo = $e->getMessage();
            throw $e;
        }
    }
}
?> 