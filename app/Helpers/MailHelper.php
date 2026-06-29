<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    
    private static function setupSMTP(PHPMailer $mail): void
    {
        $host       = config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $port       = (int) config('mail.mailers.smtp.port', 587);
        $encryption = config('mail.mailers.smtp.encryption', 'tls');
        $username   = config('mail.mailers.smtp.username');
        $password   = config('mail.mailers.smtp.password');

        $mail->isSMTP();
        $mail->Host    = $host;
        $mail->Port    = $port;
        $mail->Timeout = 15;

        if (!empty($username) && !empty($password)) {
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
        } else {
            $mail->SMTPAuth = false;
        }

        if (!empty($encryption) && strtolower((string) $encryption) !== 'null') {
            $mail->SMTPSecure  = strtolower((string) $encryption);
            $mail->SMTPAutoTLS = true;
        } else {
            $mail->SMTPSecure  = '';
            $mail->SMTPAutoTLS = false;
        }

        $mail->setFrom(
            config('mail.from.address', 'noreply@example.com'),
            config('mail.from.name', 'Tim Support')
        );
    }

    public static function sendResetPasswordEmail($email, $resetLink): array
    {
        $mail    = new PHPMailer(true);
        $appName = config('mail.from.name', 'Tim Support');

        try {
            self::setupSMTP($mail);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Kata Sandi - ' . $appName;
            $mail->Body    = self::buildResetPasswordBody($resetLink, $appName);
            $mail->AltBody = "Klik tautan berikut untuk mereset kata sandi Anda:\n\n"
                           . $resetLink . "\n\n"
                           . "Tautan ini hanya berlaku selama 60 menit.";

            $mail->send();

            return ['status' => 'success', 'message' => 'Tautan reset kata sandi telah dikirim.'];

        } catch (Exception $e) {
            $serverMsg = $mail->ErrorInfo ?: $e->getMessage();

            $log  = "\n===== RESET PASSWORD EMAIL =====\n";
            $log .= "To:              {$email}\n";
            $log .= "Link:            {$resetLink}\n";
            $log .= "Waktu:           " . date('Y-m-d H:i:s') . "\n";
            $log .= "SMTP_HOST:       " . config('mail.mailers.smtp.host') . "\n";
            $log .= "SMTP_PORT:       " . config('mail.mailers.smtp.port') . "\n";
            $log .= "SMTP_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
            $log .= "SMTP_USERNAME:   " . (empty(config('mail.mailers.smtp.username')) ? 'null' : '***') . "\n";
            $log .= "Error:           " . $serverMsg . "\n";
            $log .= "================================\n";

            file_put_contents(storage_path('logs/reset_password.log'), $log, FILE_APPEND);

            return [
                'status'  => 'error',
                'message' => 'Gagal mengirim email. Silakan coba lagi atau hubungi admin.',
                'debug'   => app()->environment('local') ? $serverMsg : null,
            ];
        }
    }

    public static function sendKodePenerimaanEmail(
        string $email,
        string $nama,
        string $kode,
        ?string $expiredAt,
        string $namaPeruntukan = ''
    ): array {
        $appName = config('mail.from.name', 'Tim Support');

        $altBody = "Halo {$nama},\n\n"
                . "Selamat! Anda telah diterima sebagai instruktur di {$appName}.\n\n"
                . "Kode Penerimaan Anda: {$kode}\n"
                . ($expiredAt ? "Berlaku hingga: {$expiredAt}\n" : "")
                . "\nGunakan kode ini saat melakukan registrasi akun instruktur di:\n"
                . url('/register') . "\n\n"
                . "Jangan bagikan kode ini kepada siapapun.";

        return self::sendEmail(
            $email,
            'Kode Penerimaan Instruktur - ' . $appName,
            self::buildKodePenerimaanBody($nama, $kode, $expiredAt, $appName),
            $altBody
        );
    }

    private static function buildKodePenerimaanBody(
        string $nama,
        string $kode,
        ?string $expiredAt,
        string $appName
    ): string {
        $registerUrl   = url('/register');
        $expiredNotice = $expiredAt
            ? '<p style="color:#92400e;font-size:13px;margin:0;">
                ⏳ Kode ini berlaku hingga <strong>' . htmlspecialchars($expiredAt) . '</strong>.
            </p>'
            : '<p style="color:#555;font-size:13px;margin:0;">
                Kode ini tidak memiliki batas waktu penggunaan.
            </p>';

        return '
        <html>
        <body style="margin:0;padding:0;font-family:Segoe UI,Arial,sans-serif;background:#f4f6f9;">
            <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
                <tr><td align="center">
                    <table width="560" cellpadding="0" cellspacing="0"
                        style="background:#fff;border-radius:12px;overflow:hidden;
                                box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                        <tr>
                            <td style="background:linear-gradient(135deg,,#e73c3c,#c92a2a);
                                    padding:36px 40px;text-align:center;">
                                <h1 style="color:#fff;margin:0 0 6px;font-size:22px;font-weight:700;">
                                    🎓 Selamat, Anda Diterima!
                                </h1>
                                <p style="color:rgba(255,255,255,0.85);margin:0;font-size:13px;">
                                    ' . htmlspecialchars($appName) . '
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:36px 40px;">
                                <p style="color:#333;font-size:16px;font-weight:600;margin:0 0 8px;">
                                    Halo, ' . htmlspecialchars($nama) . '!
                                </p>
                                <p style="color:#555;font-size:14px;line-height:1.7;margin:0 0 28px;">
                                    Anda telah diterima sebagai <strong>Instruktur</strong> di
                                    <strong>' . htmlspecialchars($appName) . '</strong>.
                                    Gunakan kode berikut untuk menyelesaikan registrasi akun Anda.
                                </p>

                                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                                    <tr><td align="center">
                                        <div style="display:inline-block;
                                                    background:#f0fdfa;
                                                    border:2px dashed #e73c3c;
                                                    border-radius:12px;
                                                    padding:20px 40px;
                                                    text-align:center;">
                                            <p style="color:#888;font-size:12px;margin:0 0 8px;
                                                    text-transform:uppercase;letter-spacing:1px;">
                                                Kode Penerimaan Anda
                                            </p>
                                            <p style="color:#e73c3c;
                                                    font-family:Courier New,monospace;
                                                    font-size:28px;
                                                    font-weight:700;
                                                    letter-spacing:4px;
                                                    margin:0;">
                                                ' . htmlspecialchars($kode) . '
                                            </p>
                                        </div>
                                    </td></tr>
                                </table>

                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#fffbeb;border-left:4px solid #f59e0b;
                                            border-radius:6px;margin-bottom:28px;">
                                    <tr><td style="padding:12px 16px;">
                                        ' . $expiredNotice . '
                                    </td></tr>
                                </table>

                                <p style="color:#333;font-size:14px;font-weight:600;margin:0 0 12px;">
                                    Langkah registrasi:
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                    <tr>
                                        <td style="width:28px;vertical-align:top;padding-top:2px;">
                                            <span style="background:#e73c3c;color:#fff;border-radius:50%;
                                                        width:20px;height:20px;display:inline-block;
                                                        text-align:center;font-size:11px;line-height:20px;
                                                        font-weight:700;">1</span>
                                        </td>
                                        <td style="color:#555;font-size:13px;line-height:1.6;padding-bottom:8px;">
                                            Buka halaman registrasi instruktur
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:28px;vertical-align:top;padding-top:2px;">
                                            <span style="background:#e73c3c;color:#fff;border-radius:50%;
                                                        width:20px;height:20px;display:inline-block;
                                                        text-align:center;font-size:11px;line-height:20px;
                                                        font-weight:700;">2</span>
                                        </td>
                                        <td style="color:#555;font-size:13px;line-height:1.6;padding-bottom:8px;">
                                            Isi data diri dan masukkan kode penerimaan di atas
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:28px;vertical-align:top;padding-top:2px;">
                                            <span style="background:#e73c3c;color:#fff;border-radius:50%;
                                                        width:20px;height:20px;display:inline-block;
                                                        text-align:center;font-size:11px;line-height:20px;
                                                        font-weight:700;">3</span>
                                        </td>
                                        <td style="color:#555;font-size:13px;line-height:1.6;padding-bottom:8px;">
                                            Upload surat / bukti penerimaan dari ' . htmlspecialchars($appName) . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:28px;vertical-align:top;padding-top:2px;">
                                            <span style="background:#e73c3c;color:#fff;border-radius:50%;
                                                        width:20px;height:20px;display:inline-block;
                                                        text-align:center;font-size:11px;line-height:20px;
                                                        font-weight:700;">4</span>
                                        </td>
                                        <td style="color:#555;font-size:13px;line-height:1.6;">
                                            Tunggu verifikasi dokumen oleh Admin
                                        </td>
                                    </tr>
                                </table>

                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr><td align="center" style="padding-bottom:28px;">
                                        <a href="' . $registerUrl . '"
                                        style="display:inline-block;padding:13px 36px;
                                                background:linear-gradient(135deg,#e73c3c,#c92a2a);
                                                color:#fff;text-decoration:none;border-radius:8px;
                                                font-size:15px;font-weight:600;">
                                            Daftar Sekarang →
                                        </a>
                                    </td></tr>
                                </table>

                                <hr style="border:none;border-top:1px solid #eee;margin:0 0 20px;">
                                <p style="color:#aaa;font-size:12px;margin:0;">
                                    🔒 Jangan bagikan kode ini kepada siapapun. Kode hanya dapat digunakan satu kali.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#f0fdfa;padding:20px 40px;
                                    text-align:center;border-top:1px solid #ccfbf1;">
                                <p style="color:#aaa;font-size:12px;margin:0;">
                                    &copy; ' . date('Y') . ' ' . htmlspecialchars($appName) . '.
                                    Dikirim otomatis, jangan balas email ini.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td></tr>
            </table>
        </body>
        </html>';
    }

    public static function sendAkunCreatedEmail(string $email, string $nama, string $username, string $password, string $role): array
    {
        $appName = config('mail.from.name', 'Tim Support');

        $altBody = "Halo {$nama},\n\n"
                 . "Admin telah membuat akun untuk Anda.\n\n"
                 . "Email    : {$email}\n"
                 . "Username : {$username}\n"
                 . "Password : {$password}\n"
                 . "Role     : " . ucfirst($role) . "\n\n"
                 . "Segera ubah password Anda setelah login pertama.\n\n"
                 . "Login di: " . url('/login');

        return self::sendEmail(
            $email,
            'Akun Anda Telah Dibuat - ' . $appName,
            self::buildAkunCreatedBody($nama, $email, $username, $password, $role, $appName),
            $altBody
        );
    }

    public static function sendEmail($to, $subject, $body, $altBody = ''): array
    {
        
        $mail = new PHPMailer(true);

        try {
            self::setupSMTP($mail);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();

            return ['status' => 'success', 'message' => 'Email berhasil dikirim'];

        } catch (Exception $e) {
            $serverMsg  = $mail->ErrorInfo ?: $e->getMessage();
            $logMessage = "To: {$to}\nSubject: {$subject}\nError: {$serverMsg}\n\n";
            file_put_contents(storage_path('logs/email.log'), $logMessage, FILE_APPEND);

            return ['status' => 'error', 'message' => 'Email gagal dikirim'];
        }
    }

    private static function buildResetPasswordBody(string $resetLink, string $appName): string
    {
        return '
        <html>
        <body style="margin:0;padding:0;font-family:Segoe UI,Arial,sans-serif;background:#f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
                <tr><td align="center">
                    <table width="600" cellpadding="0" cellspacing="0"
                           style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                        <tr>
                            <td style="background:linear-gradient(135deg,#e73c3c,#c92a2a);padding:32px 40px;text-align:center;">
                                <h1 style="color:#fff;margin:0;font-size:22px;font-weight:700;">'
                                    . htmlspecialchars($appName) . '</h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:40px;">
                                <h2 style="color:#333;font-size:20px;margin:0 0 16px;">Reset Kata Sandi</h2>
                                <p style="color:#555;line-height:1.7;margin:0 0 24px;">
                                    Kami menerima permintaan untuk mereset kata sandi akun Anda.
                                    Klik tombol di bawah untuk membuat kata sandi baru.
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr><td align="center" style="padding:10px 0 30px;">
                                        <a href="' . htmlspecialchars($resetLink) . '"
                                           style="display:inline-block;padding:14px 36px;
                                                  background:linear-gradient(135deg,#e73c3c,#c92a2a);
                                                  color:#fff;text-decoration:none;border-radius:8px;
                                                  font-size:15px;font-weight:600;">
                                            Reset Kata Sandi
                                        </a>
                                    </td></tr>
                                </table>
                                <p style="color:#888;font-size:13px;margin:0 0 16px;">
                                    Tautan ini hanya berlaku selama <strong>60 menit</strong>.
                                </p>
                                <p style="color:#888;font-size:12px;margin:0 0 8px;">
                                    Jika tombol tidak berfungsi, salin tautan berikut:
                                </p>
                                <p style="word-break:break-all;font-size:12px;margin:0 0 24px;">
                                    <a href="' . htmlspecialchars($resetLink) . '" style="color:#e73c3c;">'
                                        . htmlspecialchars($resetLink) . '</a>
                                </p>
                                <hr style="border:none;border-top:1px solid #eee;margin:0 0 20px;">
                                <p style="color:#aaa;font-size:12px;margin:0;">
                                    Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f9f9f9;padding:20px 40px;text-align:center;border-top:1px solid #eee;">
                                <p style="color:#bbb;font-size:12px;margin:0;">
                                    &copy; ' . date('Y') . ' ' . htmlspecialchars($appName) . '. Semua hak dilindungi.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>';
    }

    private static function buildAkunCreatedBody(string $nama, string $email, string $username, string $password, string $role, string $appName): string
    {
        $roleColors = [
            'admin'      => ['bg' => '#fff0ee', 'text' => '#e84e3a'],
            'instruktur' => ['bg' => '#e8f4fd', 'text' => '#3498db'],
            'peserta'    => ['bg' => '#eafaf1', 'text' => '#2ecc71'],
        ];
        $color    = $roleColors[$role] ?? ['bg' => '#f5f5f5', 'text' => '#888'];
        $loginUrl = url('/login');

        return '
        <html>
        <body style="margin:0;padding:0;font-family:Segoe UI,Arial,sans-serif;background:#f4f6f9;">
            <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
                <tr><td align="center">
                    <table width="560" cellpadding="0" cellspacing="0"
                           style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                        <tr>
                            <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:36px 40px;text-align:center;">
                                <h1 style="color:#fff;margin:0 0 6px;font-size:22px;font-weight:700;">
                                    🎉 Akun Anda Telah Dibuat
                                </h1>
                                <p style="color:rgba(255,255,255,0.8);margin:0;font-size:13px;">
                                    Selamat datang! Berikut detail login Anda.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:36px 40px;">
                                <p style="color:#333;font-size:16px;font-weight:600;margin:0 0 10px;">
                                    Halo, ' . htmlspecialchars($nama) . '!
                                </p>
                                <p style="color:#555;font-size:14px;line-height:1.7;margin:0 0 24px;">
                                    Admin telah membuat akun untuk Anda.
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0"
                                       style="background:#f8f7ff;border:1px solid #e0deff;border-radius:10px;margin-bottom:24px;">
                                    <tr><td style="padding:8px 20px;border-bottom:1px solid #ede9ff;">
                                        <table width="100%"><tr>
                                            <td style="color:#888;font-size:13px;">Nama</td>
                                            <td style="color:#333;font-size:13px;font-weight:600;text-align:right;">'
                                                . htmlspecialchars($nama) . '</td>
                                        </tr></table>
                                    </td></tr>
                                    <tr><td style="padding:8px 20px;border-bottom:1px solid #ede9ff;">
                                        <table width="100%"><tr>
                                            <td style="color:#888;font-size:13px;">Email</td>
                                            <td style="color:#333;font-size:13px;font-weight:600;text-align:right;">'
                                                . htmlspecialchars($email) . '</td>
                                        </tr></table>
                                    </td></tr>
                                    <tr><td style="padding:8px 20px;border-bottom:1px solid #ede9ff;">
                                        <table width="100%"><tr>
                                            <td style="color:#888;font-size:13px;">Username</td>
                                            <td style="color:#333;font-size:13px;font-weight:600;text-align:right;">'
                                                . htmlspecialchars($username) . '</td>
                                        </tr></table>
                                    </td></tr>
                                    <tr><td style="padding:8px 20px;border-bottom:1px solid #ede9ff;">
                                        <table width="100%"><tr>
                                            <td style="color:#888;font-size:13px;">Password</td>
                                            <td style="text-align:right;">
                                                <span style="background:#4f46e5;color:#fff;
                                                             font-family:Courier New,monospace;
                                                             padding:3px 12px;border-radius:6px;
                                                             font-size:14px;letter-spacing:1px;">'
                                                    . htmlspecialchars($password) . '</span>
                                            </td>
                                        </tr></table>
                                    </td></tr>
                                    <tr><td style="padding:8px 20px;">
                                        <table width="100%"><tr>
                                            <td style="color:#888;font-size:13px;">Role</td>
                                            <td style="text-align:right;">
                                                <span style="background:' . $color['bg'] . ';color:' . $color['text'] . ';
                                                             padding:3px 12px;border-radius:20px;
                                                             font-size:12px;font-weight:600;">'
                                                    . ucfirst(htmlspecialchars($role)) . '</span>
                                            </td>
                                        </tr></table>
                                    </td></tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0"
                                       style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:6px;margin-bottom:24px;">
                                    <tr><td style="padding:12px 16px;color:#92400e;font-size:13px;line-height:1.6;">
                                        ⚠️ <strong>Penting:</strong> Segera ubah password Anda setelah pertama kali login.
                                    </td></tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr><td align="center" style="padding-bottom:24px;">
                                        <a href="' . $loginUrl . '"
                                           style="display:inline-block;padding:13px 36px;
                                                  background:linear-gradient(135deg,#4f46e5,#7c3aed);
                                                  color:#fff;text-decoration:none;border-radius:8px;
                                                  font-size:15px;font-weight:600;">
                                            Masuk ke Aplikasi →
                                        </a>
                                    </td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f8f7ff;padding:20px 40px;text-align:center;border-top:1px solid #ede9ff;">
                                <p style="color:#bbb;font-size:12px;margin:0;">
                                    &copy; ' . date('Y') . ' ' . htmlspecialchars($appName) . '.
                                    Dikirim otomatis, jangan balas email ini.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>';
    }
}