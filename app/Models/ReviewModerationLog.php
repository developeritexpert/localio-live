<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewModerationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'action',
        'from_status',
        'to_status',
        'actor_type',
        'actor_id',
        'layer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Action constants
    const ACTION_STATUS_CHANGED    = 'status_changed';
    const ACTION_LAYER1_CHECK      = 'ai_layer1_check';
    const ACTION_LAYER2_CHECK      = 'ai_layer2_check';
    const ACTION_LAYER3_RESULT     = 'ai_layer3_result';
    const ACTION_MODERATED         = 'moderated';
    const ACTION_APPEALED          = 'appealed';
    const ACTION_REPORTED          = 'reported';
    const ACTION_RE_MODERATED      = 're_moderated';

    // Actor type constants
    const ACTOR_SYSTEM     = 'system';
    const ACTOR_MODERATOR  = 'moderator';
    const ACTOR_USER       = 'user';

    // Layer constants
    const LAYER_1     = 'layer1';
    const LAYER_2     = 'layer2';
    const LAYER_3     = 'layer3';
    const LAYER_HUMAN = 'human';

    /**
     * The review this log entry belongs to.
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * The admin/user who performed this action (if any).
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Convenience: log a system action.
     */
    public static function logSystem(
        int $reviewId,
        string $action,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $layer = null,
        array $metadata = []
    ): self {
        return self::create([
            'review_id'   => $reviewId,
            'action'      => $action,
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'actor_type'  => self::ACTOR_SYSTEM,
            'actor_id'    => null,
            'layer'       => $layer,
            'metadata'    => $metadata ?: null,
        ]);
    }

    /**
     * Convenience: log a moderator action.
     */
    public static function logModerator(
        int $reviewId,
        string $action,
        int $moderatorId,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        array $metadata = []
    ): self {
        return self::create([
            'review_id'   => $reviewId,
            'action'      => $action,
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'actor_type'  => self::ACTOR_MODERATOR,
            'actor_id'    => $moderatorId,
            'layer'       => self::LAYER_HUMAN,
            'metadata'    => $metadata ?: null,
        ]);
    }
}
