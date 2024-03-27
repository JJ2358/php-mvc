require __DIR__ . '/vendor/autoload.php';

use App\Services\JobApiService;

$jobService = new JobApiService();
$jobs = $jobService->fetchJobs();
$jobService->saveJobsToDatabase($jobs);

echo "Jobs fetched and saved successfully.\n";
