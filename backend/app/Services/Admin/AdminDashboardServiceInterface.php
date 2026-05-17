<?php

namespace App\Services\Admin;

interface AdminDashboardServiceInterface
{
    public function getAllUsers(): array;

    public function getDashboardStats(): array;

    public function getAnnouncementFunnel(): array;

    public function getTopCategories(): array;

    public function getUserRetention(): array;

    public function getHourlyActivity(): array;

    public function getPendingModerationAnnouncements(int $limit = 5): array;
}
