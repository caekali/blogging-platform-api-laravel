<?php
namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class GmailController extends Controller
{
    private $client;

    public function __construct()
    {
    
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json')); // Path to your OAuth credentials file
        $this->client->addScope(Gmail::GMAIL_SEND);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function sendEmail()
    {
        if ($this->client->isAccessTokenExpired()) {
            return redirect()->route('google.auth');
        }

        $gmailService = new Gmail($this->client);

      
        $message = new Message();
        $message->setRaw($this->createMessage());

        try {
            $gmailService->users_messages->send('me', $message);
            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return response()->json(['message' => 'Error sending email'], 500);
        }
    }

    private function createMessage()
    {
        $message = "From: your-email@gmail.com\r\n";
        $message .= "To: recipient@example.com\r\n";
        $message .= "Subject: Test Email\r\n";
        $message .= "\r\nThis is a test email sent via Gmail API.\r\n";

        return $this->base64url_encode($message);
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function handleOAuthRedirect(Request $request)
    {
        $this->client->authenticate($request->get('code'));
        session(['google_access_token' => $this->client->getAccessToken()]);

        return redirect()->route('email.send');
    }

    public function googleAuth()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }
}

