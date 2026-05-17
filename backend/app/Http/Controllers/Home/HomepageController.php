<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\GetHomepageDataRequest;
use App\Services\Home\HomepageService;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __construct(
        private readonly HomepageService $service
    ) {}

    public function __invoke(GetHomepageDataRequest $request): View
    {
        try {
            $homepageData = $this->service->getHomepageData($request);
            
            return view('home', [
                'data' => $homepageData
            ]);
        } catch (\Exception $e) {
            logger('Homepage API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, $e->getMessage());
        }
    }

    public function faq()
    {
        return view('faq');
    }

    public function partners()
    {
        return view('partners');
    }
}
