<?php

declare(strict_types=1);

/**
 * Verifica rapida anti-regressione:
 * - lint PHP su file plugin (escluso vendor)
 * - controlli mirati sui file che in passato hanno avuto corruzione "duplica-coda"
 */

$root = dirname(__DIR__);
$phpBinary = PHP_BINARY;

$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile()) {
        continue;
    }

    $path = $fileInfo->getPathname();
    $normalized = str_replace('\\', '/', $path);
    if (!str_ends_with($normalized, '.php')) {
        continue;
    }
    if (str_contains($normalized, '/vendor/')) {
        continue;
    }

    $files[] = $path;
}

sort($files);

$errors = [];

foreach ($files as $file) {
    $command = escapeshellarg($phpBinary) . ' -l ' . escapeshellarg($file) . ' 2>&1';
    $output = [];
    $exitCode = 0;
    exec($command, $output, $exitCode);
    if ($exitCode !== 0) {
        $errors[] = "Lint fallito: {$file}\n" . implode(PHP_EOL, $output);
    }
}

$criticalFiles = [
    $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'AdminMenu.php' => 'final class AdminMenu',
    $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'QrDownloadController.php' => 'final class QrDownloadController',
];

foreach ($criticalFiles as $file => $classSignature) {
    if (!is_readable($file)) {
        $errors[] = "File critico non leggibile: {$file}";
        continue;
    }

    $content = (string) file_get_contents($file);
    $classCount = substr_count($content, $classSignature);
    if ($classCount !== 1) {
        $errors[] = "Integrita sospetta in {$file}: trovate {$classCount} occorrenze di '{$classSignature}'.";
    }

    $trimmed = rtrim($content);
    if (!str_ends_with($trimmed, '}')) {
        $errors[] = "Integrita sospetta in {$file}: il file non termina con la chiusura della classe.";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "[verify-integrity] ERRORI RILEVATI\n");
    foreach ($errors as $error) {
        fwrite(STDERR, "- {$error}\n");
    }
    exit(1);
}

fwrite(STDOUT, "[verify-integrity] OK (" . count($files) . " file PHP controllati)\n");
exit(0);
