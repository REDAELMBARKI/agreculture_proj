<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    // USERS REPORT
    public function usersReport()
    {
        $users = DB::table('users')->get();
        return view('admin.reports.users-csv', compact('users'));
    }

    // DONATIONS REPORT
    public function donationsReport()
    {
        $donations = DB::table('Donation')->get();
        foreach ($donations as $d) {
            $d->itemsCount = DB::table('Donation_Item')
                ->where('donation_ID', $d->donation_ID)
                ->count();
        }
        return view('admin.reports.donations-csv', compact('donations'));
    }

    // CHARITIES REPORT
    public function charitiesReport()
    {
        $charities = DB::table('Charity')->get();
        foreach ($charities as $c) {
            $donationsReceived = DB::table('Donation')
                ->where('charity_ID', $c->charity_ID)
                ->get();
            $c->totalDonations = $donationsReceived->count();
            $c->totalItems = DB::table('Donation_Item')
                ->whereIn('donation_ID', $donationsReceived->pluck('donation_ID'))
                ->count();
        }
        return view('admin.reports.charities-csv', compact('charities'));
    }

    // SUSTAINABILITY REPORT
    public function sustainabilityReport()
    {
        $totalItems = DB::table('Donation_Item')->count();
        $totalCO2 = $totalItems * 1.5;
        return view('admin.reports.sustainability-csv', compact('totalCO2'));
    }
}
