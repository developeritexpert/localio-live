<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\ModerationCase;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetectModerationCases extends Command
{
    protected $signature   = 'moderation:detect-cases {--days=7 : Number of days to inspect}';
    protected $description = 'Scan recent reviews for suspicious coordinated patterns (IP clusters, timing bursts, account creation spikes) and group them into Moderation Cases.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $since = now()->subDays($days);

        $this->info("Scanning reviews since {$since->toDateTimeString()} for coordinated manipulation patterns...");

        $businessesWithActivity = Review::where('created_at', '>=', $since)
            ->distinct()
            ->pluck('business_id');

        $detectedCasesCount = 0;

        foreach ($businessesWithActivity as $bizId) {
            $business = Business::with('translations')->find($bizId);
            if (!$business) continue;

            $bizName = $business->translations->first()?->name ?? "Business #{$bizId}";

            $recentReviews = Review::with(['user', 'translations'])
                ->where('business_id', $bizId)
                ->where('created_at', '>=', $since)
                ->get();

            if ($recentReviews->count() < 3) continue;

            // Pattern 1: IP Cluster (3+ reviews from the same IP)
            $ipGroups = $recentReviews->whereNotNull('ip_address')->groupBy('ip_address');
            foreach ($ipGroups as $ip => $reviewsOnIp) {
                $distinctUsers = $reviewsOnIp->pluck('user_id')->unique()->count();
                if ($reviewsOnIp->count() >= 3 && $distinctUsers >= 2) {
                    $caseNumber = 'CASE-' . strtoupper(substr(md5("ip-{$bizId}-{$ip}-" . now()->format('Y-W')), 0, 8));

                    $existingCase = ModerationCase::where('case_number', $caseNumber)->first();
                    if (!$existingCase) {
                        $case = ModerationCase::create([
                            'case_number' => $caseNumber,
                            'title'       => "IP Cluster Detected on {$bizName}",
                            'type'        => ModerationCase::TYPE_IP_CLUSTER,
                            'business_id' => $bizId,
                            'status'      => ModerationCase::STATUS_OPEN,
                            'summary'     => "Found {$reviewsOnIp->count()} reviews from {$distinctUsers} different accounts originating from the same IP ({$ip}).",
                            'signals'     => [
                                'ip_address'    => $ip,
                                'review_count'  => $reviewsOnIp->count(),
                                'account_count' => $distinctUsers,
                                'business_name' => $bizName,
                            ],
                        ]);

                        $case->reviews()->syncWithoutDetaching($reviewsOnIp->pluck('id')->toArray());
                        $detectedCasesCount++;
                        $this->line("  [!] Created IP Cluster Case: {$caseNumber} for {$bizName}");
                    }
                }
            }

            // Pattern 2: Timing Burst (5+ reviews in a 6-hour window)
            $sorted = $recentReviews->sortBy('created_at')->values();
            for ($i = 0; $i < $sorted->count(); $i++) {
                $windowStart = $sorted[$i]->created_at;
                $windowEnd   = $windowStart->copy()->addHours(6);

                $burstReviews = $sorted->filter(function ($r) use ($windowStart, $windowEnd) {
                    return $r->created_at >= $windowStart && $r->created_at <= $windowEnd;
                });

                if ($burstReviews->count() >= 5) {
                    $caseNumber = 'CASE-' . strtoupper(substr(md5("burst-{$bizId}-" . $windowStart->format('Y-m-d-H')), 0, 8));

                    $existingCase = ModerationCase::where('case_number', $caseNumber)->first();
                    if (!$existingCase) {
                        $distinctAccounts = $burstReviews->pluck('user_id')->unique()->count();
                        $distinctIps      = $burstReviews->whereNotNull('ip_address')->pluck('ip_address')->unique()->count();

                        $case = ModerationCase::create([
                            'case_number' => $caseNumber,
                            'title'       => "Unusual Review Velocity Spike on {$bizName}",
                            'type'        => ModerationCase::TYPE_TIMING_BURST,
                            'business_id' => $bizId,
                            'status'      => ModerationCase::STATUS_OPEN,
                            'summary'     => "Rapid surge of {$burstReviews->count()} reviews within 6 hours across {$distinctAccounts} accounts ({$distinctIps} IPs).",
                            'signals'     => [
                                'review_count'  => $burstReviews->count(),
                                'account_count' => $distinctAccounts,
                                'ip_count'      => $distinctIps,
                                'window_start'  => $windowStart->toDateTimeString(),
                                'window_end'    => $windowEnd->toDateTimeString(),
                            ],
                        ]);

                        $case->reviews()->syncWithoutDetaching($burstReviews->pluck('id')->toArray());
                        $detectedCasesCount++;
                        $this->line("  [!] Created Timing Burst Case: {$caseNumber} for {$bizName}");
                        break; // Move to next business
                    }
                }
            }
        }

        $this->info("Scan complete. {$detectedCasesCount} new moderation case(s) created.");
        Log::info("moderation:detect-cases: Created {$detectedCasesCount} cases.");

        return self::SUCCESS;
    }
}
