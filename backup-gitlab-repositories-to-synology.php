<?php

// This script will back up all your GitLab repositories to a specified location.
// git is required to clone repositories!
// install "git server" from package-center

// I recommend creating a seperate GitLab user for backups.
// You'll need to generate a personal access token for that user with API access (in GitLab).
// Next, generate a SSH keypair for the NAS user and attach it to the GitLab user.
// Finally, create a scheduled task in your NAS config to run this script: "php /some/location/script.php"

// if error "Permissions 0777 for '~/.ssh/id_rsa' are too open." appeared
// execute "chmod 600 ~/.ssh/id_rsa"

// Config -- start
$BACKUP_BASEDIR = "/volume1/backups/gitlab";
$GITLAB_TOKEN = "YOUR KEY";
$GITLAB_SERVER = "https://gitlab.com";
// Config -- end

// Step 1: Pull repository list from GitLab API
echo "Connecting to GitLab API to get repository list..." . PHP_EOL;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$GITLAB_SERVER}/api/v4/projects?per_page=1000&page=1&membership=true");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["PRIVATE-TOKEN: {$GITLAB_TOKEN }"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Step 2: Parse JSON
$parsed = json_decode($response);
$count = count($parsed);

echo "Downloaded list of repositories to back up: found {$count} items" . PHP_EOL;

// if the target directory doesn't exist, create folder
if (!file_exists($BACKUP_BASEDIR)) {
    @shell_exec("mkdir -p {$BACKUP_BASEDIR}");
}

$i = 0;

// Step 3: Iterate JSON items, cloning or fetching/pulling as needed
foreach ($parsed as $item) {
    $sshUrl = $item->ssh_url_to_repo;
    $targetPath = $BACKUP_BASEDIR . "/" . $item->path_with_namespace . "/.git";

    $percentage = round(($i / $count) * 100, 2);
    $i++;

    echo "[{$i} of {$count}, {$percentage}%]" . PHP_EOL;
    echo "Cloning {$sshUrl} to {$targetPath}..." . PHP_EOL;

    if (!file_exists($targetPath)) {
        // @shell_exec("mkdir -p {$targetPath}");
        echo shell_exec("cd {$BACKUP_BASEDIR} && git clone --mirror {$sshUrl} {$targetPath}") . PHP_EOL;
    } else {
        echo shell_exec("cd {$targetPath} && git fetch --all --prune");
    }
}

// That's all, folks!
echo "Completed git backup process";