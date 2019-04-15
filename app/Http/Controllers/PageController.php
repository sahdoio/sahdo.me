<?php

namespace App\Http\Controllers;

use App\Repository\DashboardRepository;

class PageController extends Controller
{

    /*
    ########################
    # Generic Methods
    ########################
    */

    /**
     *
     */
    public function home()
    {
        return view('pages.home', [
            'page' => 'home',
            'page_title' => 'Home'
        ]);
    }

    /**
     *
     */
    public function notfound()
    {
        return view('pages.errors.404');
    }

    /*
    ########################
    # Business Methods
    ########################
    */

    /**
     *
     */
    public function analytics_dashboard($store_id)
    {
        $data = DashboardRepository::data();

        $total_revenue = float_to_money($data['total_revenue'], '');
        $total_orders = float_formatted($data['total_orders']);
        $total_customers = float_formatted($data['total_customers']);
        $total_products = float_formatted($data['total_product']);

        $last_months_labels = DashboardRepository::lastMonthsLabels(6);
        $last_months_data = $data['revenue_month'];
        $last_months_data_recommended = $data['revenue_month_recommended'];

        return view('pages.store.analytics.dashboard', compact(
            'total_revenue',
            'total_orders',
            'total_customers',
            'total_products',
            'last_months_labels',
            'last_months_data',
            'last_months_data_recommended'
        ),
            [
                'store_id' => $store_id,
                'page' => 'analytics_dashboard',
                'page_title' => 'Dashboard'
            ]);
    }

    /**
     *
     */
    public function analytics_orders($store_id)
    {
        return view('pages.store.analytics.orders', [
            'store_id' => $store_id,
            'page' => 'analytics_orders',
            'page_title' => 'Orders'
        ]);
    }

    /**
     *
     */
    public function analytics_search($store_id)
    {
        return view('pages.store.analytics.search', [
            'store_id' => $store_id,
            'page' => 'analytics_search',
            'page_title' => 'Search Insights'
        ]);
    }

    /**
     *
     */
    public function analytics_recs($store_id)
    {
        return view('pages.store.analytics.recs', [
            'store_id' => $store_id,
            'page' => 'analytics_recs',
            'page_title' => 'Recs. Insights'
        ]);
    }

    /**
     *
     */
    public function analytics_overlays($store_id)
    {
        return view('pages.store.analytics.overlays', [
            'store_id' => $store_id,
            'page' => 'analytics_overlays',
            'page_title' => 'Overlays Insights'
        ]);
    }

    /*
    ########################
    # Products Methods
    ########################
    */

    /**
     *
     */
    public function products_search($store_id)
    {
        return view('pages.store.products.search', [
            'store_id' => $store_id,
            'page' => 'products_search',
            'page_title' => 'Search'
        ]);
    }

    /**
     *
     */
    public function products_recs($store_id)
    {
        return view('pages.store.products.recs', [
            'store_id' => $store_id,
            'page' => 'products_recs',
            'page_title' => 'Recommendations'
        ]);
    }

    /**
     *
     */
    public function products_overlays($store_id)
    {
        return view('pages.store.products.overlays', [
            'store_id' => $store_id,
            'page' => 'products_overlays',
            'page_title' => 'Overlays'
        ]);
    }

    /*
    ########################
    # Setup Methods
    ########################
    */

    /**
     *
     */
    public function setup_designs($store_id)
    {
        return view('pages.store.setup.designs', [
            'store_id' => $store_id,
            'page' => 'setup_designs',
            'page_title' => 'Designs'
        ]);
    }

    /**
     *
     */
    public function setup_datafeed($store_id)
    {
        return view('pages.store.setup.datafeed', [
            'store_id' => $store_id,
            'page' => 'setup_datafeed',
            'page_title' => 'Data Feed'
        ]);
    }

    /**
     *
     */
    public function setup_inspect($store_id)
    {
        return view('pages.store.setup.inspect', [
            'store_id' => $store_id,
            'page' => 'setup_inspect',
            'page_title' => 'Inspect'
        ]);
    }
}
