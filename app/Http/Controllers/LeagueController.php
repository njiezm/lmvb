<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\Club;
use App\Models\Document;
use App\Models\Partner;

class LeagueController extends Controller
{
    public function index()
    {
        return view('league.index', [
            'board' => BoardMember::active()->get(),
            'documents' => Document::active()->orderBy('category')->orderBy('title')->get()->groupBy('category'),
            'partners' => Partner::active()->get(),
            'clubsCount' => Club::active()->whereNotNull('ffvb_number')->count(),
        ]);
    }

    public function legal()
    {
        return view('league.legal');
    }
}
