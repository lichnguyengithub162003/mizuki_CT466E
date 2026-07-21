<?php

namespace App\Repositories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @extends BaseRepository<Promotion>
 */
class PromotionRepository extends BaseRepository
{
    public function __construct(Promotion $model)
    {
        parent::__construct($model);
    }

    public function findByCodeForUser(string $code, int $userId): ?Promotion
    {
        return $this->withEligibilityData($userId)
            ->where('code', $code)
            ->first();
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getCandidatesForUser(int $userId): Collection
    {
        return $this->withEligibilityData($userId)
            ->whereNotNull('code')
            ->where('applies_to', 'order')
            ->orderBy('ends_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Builder<Promotion>
     */
    private function withEligibilityData(int $userId): Builder
    {
        return $this->query()
            ->with([
                'branches' => fn (Builder|BelongsToMany $query): Builder|BelongsToMany => $query
                    ->select('branches.id'),
            ])
            ->withCount('usages')
            ->withCount([
                'usages as user_usage_count' => fn (Builder $query): Builder => $query
                    ->where('user_id', $userId),
            ]);
    }
}
