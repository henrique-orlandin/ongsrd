<?php

namespace App\Controllers;

use App\Models\ContactMessageModel;
use PHPMailer\PHPMailer\PHPMailer;

class ContactController extends BaseController
{
    public function index(): string
    {
        $recaptchaSiteKey = trim((string) (env('RECAPTCHA_SITE_KEY') ?: getenv('RECAPTCHA_SITE_KEY') ?: ''));

        return view('site/pages/contato', [
            'seo' => [
                'title'       => 'ONG SRD | Contato',
                'description' => 'Entre em contato com a ONG SRD. Respondemos denúncias, dúvidas e mensagens de doação.',
            ],
            'recaptchaSiteKey' => $recaptchaSiteKey,
        ]);
    }

    public function submit()
    {
        // ── Rate limit: 3 submissions per IP per hour ──────────────────
        $cache    = \Config\Services::cache();
        $ipKey    = 'contact_rate_' . md5($this->request->getIPAddress());
        $attempts = (int) ($cache->get($ipKey) ?? 0);

        if ($attempts >= 3) {
            return redirect()->to(base_url('contato'))
                ->with('error', 'Você atingiu o limite de 3 mensagens por hora. Tente novamente mais tarde.');
        }

        // ── Honeypot ───────────────────────────────────────────────────
        if (trim((string) $this->request->getPost('website')) !== '') {
            return redirect()->to(base_url('contato'));
        }

        // ── Google reCAPTCHA ───────────────────────────────────────────
        $recaptchaToken = trim((string) $this->request->getPost('g-recaptcha-response'));
        if (! $this->verifyRecaptcha($recaptchaToken)) {
            return redirect()->back()->withInput()
            ->with('error', 'Falha na verificação anti-robô. Tente novamente.');
        }

        // ── Validation ─────────────────────────────────────────────────
        if (! $this->validate([
            'name'    => 'required|max_length[120]',
            'email'   => 'required|valid_email',
            'phone'   => 'permit_empty|max_length[30]',
            'message' => 'required|min_length[10]|max_length[2000]',
        ])) {
            return redirect()->back()->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name    = trim((string) $this->request->getPost('name'));
        $email   = trim((string) $this->request->getPost('email'));
        $phone   = trim((string) ($this->request->getPost('phone') ?? ''));
        $message = trim((string) $this->request->getPost('message'));

        // ── Persist to DB ───────────────────────────────────────────────
        (new ContactMessageModel())->insert([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'message' => $message,
            'is_new'  => 1,
        ]);

        // ── Increment rate limit ────────────────────────────────────────
        $cache->save($ipKey, $attempts + 1, 3600);

        // ── Send e-mail (only when SMTP is configured) ──────────────────
        $this->sendMail($name, $email, $phone, $message);

        return redirect()->to(base_url('contato'))
            ->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }

    private function sendMail(string $name, string $email, string $phone, string $message): void
    {
        $smtpHost = (string) (getenv('MAIL_HOST') ?: '');
        if ($smtpHost === '') {
            return; // SMTP not configured yet – message already saved in DB
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = (string) (getenv('MAIL_USER') ?: '');
            $mail->Password   = (string) (getenv('MAIL_PASS') ?: '');
            $mail->SMTPSecure = (string) (getenv('MAIL_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port       = (int)    (getenv('MAIL_PORT') ?: 587);
            $mail->CharSet    = PHPMailer::CHARSET_UTF8;

            $fromEmail = (string) (getenv('MAIL_FROM') ?: $mail->Username);
            $toEmail   = (string) (getenv('MAIL_TO')   ?: $fromEmail);

            $mail->setFrom($fromEmail, 'ONG SRD - Site');
            $mail->addAddress($toEmail);
            $mail->addReplyTo($email, $name);

            $mail->isHTML(false);
            $mail->Subject = 'Nova mensagem de contato - ONG SRD';
            $mail->Body    = "Nome: {$name}\nE-mail: {$email}\nTelefone: {$phone}\n\nMensagem:\n{$message}";

            $mail->send();
        } catch (\Exception $e) {
            log_message('error', '[ContactController] PHPMailer: ' . $e->getMessage());
        }
    }

    private function verifyRecaptcha(string $token): bool
    {
        $secretKey = trim((string) (env('RECAPTCHA_SECRET_KEY') ?: getenv('RECAPTCHA_SECRET_KEY') ?: ''));

        if ($secretKey === '' || $token === '') {
            return false;
        }

        try {
            $response = \Config\Services::curlrequest()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'form_params' => [
                        'secret'   => $secretKey,
                        'response' => $token,
                        'remoteip' => (string) $this->request->getIPAddress(),
                    ],
                    'timeout' => 8,
                ]
            );

            $body = json_decode($response->getBody(), true);
            return is_array($body) && ! empty($body['success']);
        } catch (\Throwable $e) {
            log_message('error', '[ContactController] reCAPTCHA: ' . $e->getMessage());
            return false;
        }
    }
}
