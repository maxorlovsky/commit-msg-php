<?php
$commitMsgFile = __DIR__ . '/../../../.git/hooks/commit-msg';

// Check if commit-msg file exists, if not, it means that it's not a git repo, just stoping the execution
// If backup already exist, that means that git-commit-msg is already installed, aborting until backup is removed
if (!file_exists($commitMsgFile . '.sample') || file_exists($commitMsgFile . '.backup')) {
    exit;
}

// Creating a backup of commit-msg file if it exists
if (file_exists($commitMsgFile) && !is_link($commitMsgFile)) {
    echo 'commit-msg-php: git commit-msg hook detected, creating backup';
    echo "\r\n";
    copy($commitMsgFile, $commitMsgFile . '.backup');
}

// Deleting the original commit-msg
if (file_exists($commitMsgFile)) {
    unlink($commitMsgFile);
}

// Content of commit-msg hook
$commitMsgContent =
'#!/bin/bash
PHP=`which php 2> /dev/null`
$PHP ./vendor/maxorlovsky/commit-msg-php/index.php
RESULT=$?
[ $RESULT -ne 0 ] && exit 1
exit 0';

//
// It could be that we do not have rights to this folder which could cause the
// installation of this module to completely fail. We should just output the
// error instead destroying the whole npm install process.
//
try {
    $gitCommitFile = fopen($commitMsgFile, "wb");
    fwrite($gitCommitFile, $commitMsgContent);
    fclose($gitCommitFile);
} catch (Exception $e) {
    echo 'git-commit-msg: Failed to create the hook file in your .git/hooks folder because:';
    echo "\r\n";
    echo 'git-commit-msg: '. $e;
    echo "\r\n";
    echo 'git-commit-msg: The hook was not installed.';
    echo "\r\n";
    echo 'git-commit-msg:';
    echo "\r\n";
}

try {
    chmod($commitMsgFile, '0777');
} catch (Exception $e) {
    echo 'git-commit-msg:';
    echo "\r\n";
    echo 'git-commit-msg: chmod 0777 the commit-msg file in your .git/hooks folder because:';
    echo "\r\n";
    echo 'git-commit-msg: ' . $e;
    echo "\r\n";
    echo 'git-commit-msg:';
    echo "\r\n";
}