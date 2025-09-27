<?php
$cronJobs = shell_exec("crontab -l");
if ($cronJobs) {
    $jobs = explode("\n", trim($cronJobs));
    foreach ($jobs as $job) {
        if (!empty($job)) {
            echo $job . "\n";
        }
    }
} else {
    echo "Nincs aktív cron job.\n";
}
