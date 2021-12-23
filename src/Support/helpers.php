<?php

namespace Huid\PhpcDep\Support;

use Huid\PhpcDep\Support\Requests;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * get all package by input name
 *
 * @param  string $name
 * @return array
 */
function get_packages(string $name) {

    $data = cache_remember($name, function ($keyword) {
        $requests = new Requests();
        return $requests->get("https://packagist.org/packages/{$keyword}.json");
    });

    $ret = [];
    foreach($data['package']['versions'] as $version => $item) {
        if (empty($item['require']['php'])) {
            $phpVersion = '-';
        } else {
            $phpVersion = $item['require']['php'];
        }

        $ret[$phpVersion][] = $version;
    }

    return $ret;
};


/**
 * cache the fetch data
 *
 * @param  string   $key
 * @param  \Closure $closure
 * @return array
 */
function cache_remember(string $key, \Closure $closure) {
    $filepath = get_cache_file_path($key);

    if (is_file($filepath)) {
        return unserialize(file_get_contents($filepath));
    }

    $dirPath = dirname($filepath);
    if (!is_dir(dirname($filepath))) {
        mkdir($dirPath);
    }

    $data = $closure($key);
    file_put_contents($filepath, serialize($data));

    return $data;
};


/**
 * get cache file last update time
 *
 * @param  string $key
 * @return string
 */
function get_cache_file_update_time(string $key) {
    $filepath = get_cache_file_path($key);

    if (!is_file($filepath)) {
        return 'NAN';
    }

    return date('Y-m-d H:i:s', filemtime($filepath));
};

/**
 * get cache file path
 *
 * @param  string $key
 * @return string
 */
function get_cache_file_path(string $key) {
    $md5name = md5($key);
    return "./cache/{$md5name}";
};

/**
 * wrap output render style
 *
 * @param OutputInterface $output
 * @return void
 */
function wrap_gray_formatter(OutputInterface $output) {
    $outputStyle = new OutputFormatterStyle('gray', '', ['bold', 'blink']);
    $output->getFormatter()->setStyle('gray', $outputStyle);
};

/**
 * write down get command readme.
 *
 * @param OutputInterface $output
 * @param string $name
 * @return void
 */
function write_get_command_readme(OutputInterface $output, $name) {

    wrap_gray_formatter($output);
    $updatedTime = get_cache_file_update_time($name);

    $msg = <<<EOF
<gray>Read local cache list(last update: {$updatedTime})</gray>
<gray>You can run `ppdep:update` to get a newer release list.</gray>
EOF;

    $output->writeln($msg);

};

/**
 * render packages in console
 *
 * @param  string          $name
 * @param  InputInterface  $input
 * @param  OutputInterface $output
 * @return void
 */
function render_packages(string $name, InputInterface $input, OutputInterface $output) {
    $packages = get_packages($name);
    $io = new SymfonyStyle($input, $output);

    $listing = [];
    foreach ($packages as $phpVersion => $packageVersions) {
        $io->section("PHP VERSION: {$phpVersion}");
        $chunks = \array_chunk($packageVersions, 7);
        $listing = [];
        foreach ($chunks as $chunk) {
            $listing[] = \implode(', ', $chunk);
        }
        $io->listing($listing);
    }
}