<?php

namespace App\Services;

use Exception;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailConnectionTester
{
    /**
     * Test SMTP connection.
     */
    public function testSmtp(string $host, int $port, string $encryption, string $username, string $password): array
    {
        try {
            $isTls = in_array(strtolower($encryption), ['tls', 'ssl']);
            $transport = new EsmtpTransport($host, $port, $isTls);
            $transport->setUsername($username);
            $transport->setPassword($password);

            $transport->start();
            return ['success' => true, 'message' => 'SMTP connection successful.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'SMTP connection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Test IMAP connection.
     */
    public function testImap(string $host, int $port, string $encryption, string $username, string $password): array
    {
        if (!extension_loaded('imap')) {
            return ['success' => false, 'message' => 'PHP IMAP extension is not installed or enabled.'];
        }

        $encryptionFlag = strtolower($encryption) === 'ssl' ? '/ssl/novalidate-cert' : (strtolower($encryption) === 'tls' ? '/tls/novalidate-cert' : '/novalidate-cert');
        $serverString = "{" . $host . ":" . $port . "/imap" . $encryptionFlag . "}";

        try {
            $inbox = @imap_open($serverString, $username, $password, OP_HALFOPEN);
            if ($inbox) {
                imap_close($inbox);
                return ['success' => true, 'message' => 'IMAP connection successful.'];
            }
            return ['success' => false, 'message' => 'IMAP connection failed: ' . imap_last_error()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'IMAP connection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Test POP3 connection.
     */
    public function testPop(string $host, int $port, string $encryption, string $username, string $password): array
    {
        $protocol = strtolower($encryption) === 'ssl' ? 'ssl://' : (strtolower($encryption) === 'tls' ? 'tls://' : '');
        
        try {
            $fp = @fsockopen($protocol . $host, $port, $errno, $errstr, 10);
            if (!$fp) {
                return ['success' => false, 'message' => "POP3 connection failed: $errstr ($errno)"];
            }

            $response = fgets($fp, 512);
            if (strpos($response, '+OK') !== 0) {
                fclose($fp);
                return ['success' => false, 'message' => 'POP3 server did not respond correctly.'];
            }

            fputs($fp, "USER $username\r\n");
            $response = fgets($fp, 512);
            if (strpos($response, '+OK') !== 0) {
                fclose($fp);
                return ['success' => false, 'message' => 'POP3 authentication failed (USER).'];
            }

            fputs($fp, "PASS $password\r\n");
            $response = fgets($fp, 512);
            if (strpos($response, '+OK') !== 0) {
                fclose($fp);
                return ['success' => false, 'message' => 'POP3 authentication failed (PASS).'];
            }

            fputs($fp, "QUIT\r\n");
            fclose($fp);

            return ['success' => true, 'message' => 'POP3 connection successful.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'POP3 connection failed: ' . $e->getMessage()];
        }
    }
}
