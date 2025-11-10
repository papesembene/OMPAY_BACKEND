<?php

namespace App\Console\Commands;

use App\Services\OtpService;
use Illuminate\Console\Command;

class SendOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:send {phone} {--show-otp : Afficher l\'OTP généré}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer un OTP à un numéro de téléphone (pour simulation en production)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $showOtp = $this->option('show-otp');

        $this->info("Envoi d'un OTP vers {$phone}...");

        try {
            // Générer l'OTP
            $otpService = app(OtpService::class);
            $otpService->requestOtp($phone);

            $this->info('✅ OTP envoyé avec succès !');

            if ($showOtp) {
                // Récupérer le dernier OTP généré pour ce numéro
                $lastOtp = \App\Models\OtpRequest::where('phone', $phone)
                    ->where('used', false)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lastOtp) {
                    $this->warn("🔑 OTP généré : {$lastOtp->otp}");
                    $this->warn("⏰ Expire le : {$lastOtp->expires_at->format('Y-m-d H:i:s')}");
                }
            }

            $this->info('📝 Vérifiez les logs Laravel pour plus de détails.');

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi de l\'OTP : ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}