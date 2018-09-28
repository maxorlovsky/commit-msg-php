<?php
$commitMsgFile = __DIR__ . '/.git/hooks/commit-msg';

// Stop if file does not exist
if (!file_exists($commitMsgFile)) {
    exit;
}

// Reverting backup file for commit-msg
if (!file_exists($commitMsgFile . '.backup')) {
    unlink($commitMsgFile);
} else {
    copy($commitMsgFile . '.backup', $commitMsgFile);
    chmod($commitMsgFile, '0755');
    unlink($commitMsgFile . '.backup');
}