<?php
namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use App\Support\JclProfileContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $profile = JclProfileContent::company();
        $hasJobListings = Schema::hasTable('job_listings');
        $hasCompanies = Schema::hasTable('companies');
        $hasCandidates = Schema::hasTable('candidates');
        $hasCategories = Schema::hasTable('categories');

        $data = [
            'job_count'       => $hasJobListings ? JobListing::where('status', 'active')->where('is_approved', true)->count() : 0,
            'company_count'   => $hasCompanies ? Company::where('status', 'active')->count() : 0,
            'candidate_count' => $hasCandidates ? Candidate::count() : 0,
            'featured_jobs'   => $hasJobListings ? JobListing::where('status', 'active')
                                    ->where('is_approved', true)
                                    ->where('is_featured', 1)
                                    ->orderByDesc('id')
                                    ->limit(6)
                                    ->get() : collect(),
            'recent_jobs'     => $hasJobListings ? JobListing::where('status', 'active')
                                    ->where('is_approved', true)
                                    ->orderByDesc('id')
                                    ->limit(8)
                                    ->get() : collect(),
            'categories'      => ($hasCategories && $hasJobListings) ? Category::where('is_active', true)
                                    ->withCount(['jobListings' => fn($q) => $q->where('status', 'active')->where('is_approved', true)])
                                    ->orderByDesc('job_listings_count')
                                    ->limit(8)
                                    ->get() : collect(),
            'jclProfile' => $profile,
            'jclImages' => JclProfileContent::images(),
            'seo_meta' => [
                'title'       => 'Jose Consulting Limited (JCL) — Workforce Transformation for Maritime & Energy',
                'description' => $profile['summary'],
                'full_url'    => url('/'),
                'is_homepage' => true,
            ],
        ];

        return view('home', $data);
    }

    public function checkConnectDatabase(Request $request){
        $connection = $request->input('database_connection');
        config([
            'database' => [
                'default' => $connection."_check",
                'connections' => [
                    $connection."_check" => [
                        'driver' => $connection,
                        'host' => $request->input('database_hostname'),
                        'port' => $request->input('database_port'),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                    ],
                ],
            ],
        ]);
        try {
            DB::connection()->getPdo();
            $check = DB::table('information_schema.tables')->where("table_schema","performance_schema")->get();
            if(empty($check) and $check->count() == 0){
                return $this->sendSuccess(false , __("Access denied for user!. Please check your configuration."));
            }
            if(DB::connection()->getDatabaseName()){
                return $this->sendSuccess(false , __("Yes! Successfully connected to the DB: ".DB::connection()->getDatabaseName()));
            }else{
                return $this->sendSuccess(false , __("Could not find the database. Please check your configuration."));
            }
        } catch (\Exception $e) {
            return $this->sendError( $e->getMessage() );
        }
    }
}
