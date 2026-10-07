<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletter.index', [
            'subscribers' => NewsletterSubscriber::latest()->paginate(50),
            'activeCount' => NewsletterSubscriber::subscribed()->count(),
        ]);
    }

    /** Export CSV (compatible Excel / Brevo / Mailchimp) des inscrits actifs. */
    public function export()
    {
        $rows = NewsletterSubscriber::subscribed()->orderBy('email')->get(['email', 'created_at']);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['email', 'inscrit_le'], ';');
            foreach ($rows as $row) {
                fputcsv($out, [$row->email, $row->created_at->format('d/m/Y')], ';');
            }
            fclose($out);
        }, 'newsletter-lmvb-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
