<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Collection;

interface AdminDashboardRepositoryInterface
{
    public function getAllUsers(): Collection;

    public function countAllProducts(): int;

    public function countAllUsers(): int;

    public function countActiveProducts(): int;

    public function getRecentUserRegistrationDates(int $limit = 10): array;

    public function getAllInventoryItems(): Collection;

    public function countTotalAnnouncements(): int;

    public function countActiveAnnouncements(): int;

    public function countPendingModeration(): int;

    public function countNewUsersToday(): int;

    public function getUserTrend(): array;

    public function getAnnouncementFunnelCounts(): array;

    public function getTopCategories(): array;

    public function getUserRetentionStatsForCurrentMonth(): array;

    public function getPendingModerationAnnouncements(int $limit = 5): Collection;
}
