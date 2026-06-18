<?php
/**
 * Newsletter helpers for scholarship alerts.
 */

function newsletter_token(): string {
    return bin2hex(random_bytes(32));
}

function newsletter_normalize_interests(array $interests): string {
    $allowed = ['all', 'masters', 'phd', 'undergraduate', 'fully-funded', 'closing-soon'];
    $clean = array_values(array_intersect($allowed, array_map('strval', $interests)));
    return implode(',', $clean ?: ['all']);
}

function newsletter_subscribe(mysqli $link, string $email, array $interests = ['all'], string $source = 'website'): array {
    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Please enter a valid email address.'];
    }

    $token = newsletter_token();
    $interest_csv = newsletter_normalize_interests($interests);

    $sql = "INSERT INTO newsletter_subscribers (email, status, interests, unsubscribe_token, source, created_at, updated_at)
            VALUES (?, 'active', ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                status = 'active',
                interests = VALUES(interests),
                unsubscribe_token = IF(unsubscribe_token IS NULL OR unsubscribe_token = '', VALUES(unsubscribe_token), unsubscribe_token),
                source = VALUES(source),
                updated_at = NOW()";

    $stmt = mysqli_prepare($link, $sql);
    if (!$stmt) {
        error_log('Newsletter subscribe prepare failed: ' . mysqli_error($link));
        return ['ok' => false, 'message' => 'Subscription failed. Please try again.'];
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $email, $interest_csv, $token, $source);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        error_log('Newsletter subscribe failed: ' . mysqli_error($link));
        return ['ok' => false, 'message' => 'Subscription failed. Please try again.'];
    }

    return ['ok' => true, 'message' => 'You are subscribed. Watch your inbox for weekly scholarship alerts.'];
}

function newsletter_unsubscribe(mysqli $link, string $token): bool {
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return false;
    }

    $stmt = mysqli_prepare($link, "UPDATE newsletter_subscribers SET status = 'unsubscribed', updated_at = NOW() WHERE unsubscribe_token = ?");
    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    $changed = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $changed;
}
