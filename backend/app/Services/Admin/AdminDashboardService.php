<?php

namespace App\Services\Admin;

use App\DTO\Admin\AdminDashboardStatsDTO;
use App\DTO\Admin\AdminAnnouncementFunnelDTO;
use App\DTO\Admin\AdminPendingModerationItemDTO;
use App\DTO\Admin\AdminTopCategoryDTO;
use App\DTO\Admin\AdminUserRetentionDTO;
use App\Repositories\Admin\AdminDashboardRepositoryInterface;

class AdminDashboardService implements AdminDashboardServiceInterface
{
    public function __construct(
        private readonly AdminDashboardRepositoryInterface $repository
    ) {}

    public function getAllUsers(): array
    {
        return $this->repository->getAllUsers()->values()->all();
    }

    public function getDashboardStats(): array
    {
        return [
            'total_announcements' => $this->repository->countTotalAnnouncements(),
            'active_announcements' => $this->repository->countActiveAnnouncements(),
            'pending_moderation' => $this->repository->countPendingModeration(),
            'new_users_today' => $this->repository->countNewUsersToday(),
            'user_trends' => $this->repository->getUserTrend(),
        ];
    }

    public function getAnnouncementFunnel(): array
    {
        $funnel = $this->repository->getAnnouncementFunnelCounts();
        $dto = new AdminAnnouncementFunnelDTO(
            posted: (int) ($funnel['posted'] ?? 0),
            active: (int) ($funnel['active'] ?? 0),
            contacted: (int) ($funnel['contacted'] ?? 0),
            closed: (int) ($funnel['closed'] ?? 0),
        );

        return $dto->toArray();
    }

    public function getTopCategories(): array
    {
        return collect($this->repository->getTopCategories())
            ->map(fn ($row) => new AdminTopCategoryDTO(
                category: (string) ($row['category'] ?? 'Unknown'),
                count: (int) ($row['count'] ?? 0),
            ))
            ->map(fn (AdminTopCategoryDTO $dto) => $dto->toArray())
            ->values()
            ->all();
    }

    public function getUserRetention(): array
    {
        $retention = $this->repository->getUserRetentionStatsForCurrentMonth();
        $dto = new AdminUserRetentionDTO(
            returning_users_percent: (int) ($retention['returning'] ?? 0),
            new_users_percent: (int) ($retention['new'] ?? 0),
        );

        return $dto->toArray();
    }

    public function getHourlyActivity(): array
    {
        // Placeholder for hourly activity logic
        return array_fill(0, 24, 0);
    }

    public function getPendingModerationAnnouncements(int $limit = 5): array
    {
        return $this->repository
            ->getPendingModerationAnnouncements($limit)
            ->map(fn ($product) => AdminPendingModerationItemDTO::fromProduct($product)->toArray())
            ->values()
            ->all();
    }
}
