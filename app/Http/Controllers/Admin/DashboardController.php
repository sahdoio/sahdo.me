<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\SiteView;
use Session;

class DashboardController extends Controller
{
    /**
     *
     */
    public function dashboard()
    {
        $siteviews = [];

        $siteviews_labels = [];
        $siteviews_data = [];
        $visitors_labels = [];
        $visitors_data = [];
        for ($i = (count($siteviews) - 1); $i > 0; $i--) {
            $siteview = $siteviews[$i];

            $date = new \DateTime($siteview['date']);
            $date = $date->format('d/m/Y');

            $siteviews_labels[] = $date;
            $siteviews_data[] = $siteview['pages'];
            $visitors_labels[] = $date;
            $visitors_data[] = $siteview['views'];
        }

        $messages = [];

        $map_date = [];
        for ($i = (count($messages) - 1); $i >= 0; $i--) {
            $message = $messages[$i];

            $date = new \DateTime($message['date']);
            $date = $date->format('d/m/Y');

            if (isset($map_date[$date]))
                $map_date[$date] += 1;
            else
                $map_date[$date] = 1;
        }

        $messages_labels = [];
        $messages_data = [];
        foreach ($map_date as $date => $qty) {
            $messages_labels[] = $date;
            $messages_data[] = $qty;
        }

        $visits = 5;
        $pageviews = 10;
        $messages = 6;

        $siteviews_labels = json_encode($siteviews_labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        $siteviews_data = json_encode($siteviews_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $visitors_labels = json_encode($visitors_labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $visitors_data = json_encode($visitors_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $messages_labels = json_encode($messages_labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $messages_data = json_encode($messages_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $data = [
            'page' => 'dashboard',
            'page_title' => 'Dashboard',
            'siteviews_labels' => $siteviews_labels,
            'siteviews_data' => $siteviews_data,
            'visitors_labels' => $visitors_labels,
            'visitors_data' => $visitors_data,
            'messages_labels' => $messages_labels,
            'messages_data' => $messages_data,
            'pageviews' => $pageviews,
            'visits' => $visits,
            'messages' => $messages,
        ];

        return view('admin.dashboard', $data);
    }
}
