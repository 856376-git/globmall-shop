<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermissionAuditLog extends Model
{
    protected $table = "role_permission_audit_logs";

    protected $fillable = [
        "actor_id",
        "target_user_id",
        "action",
        "subject_type",
        "subject_id",
        "subject_name",
        "ip_address",
        "user_agent",
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, "actor_id");
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, "target_user_id");
    }

    public static function log(
        int     $actorId,
        int     $targetUserId,
        string  $action,
        string  $subjectType,
        ?int    $subjectId,
        ?string $subjectName,
        ?string $ipAddress,
        ?string $userAgent,
    ): self {
        return self::create([
            "actor_id"       => $actorId,
            "target_user_id" => $targetUserId,
            "action"         => $action,
            "subject_type"   => $subjectType,
            "subject_id"     => $subjectId,
            "subject_name"   => $subjectName,
            "ip_address"     => $ipAddress,
            "user_agent"     => $userAgent,
        ]);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            "role_assigned"      => "Assigned role",
            "role_removed"       => "Removed role",
            "permission_granted" => "Granted permission",
            "permission_revoked" => "Revoked permission",
            default              => $this->action,
        };
    }
}
