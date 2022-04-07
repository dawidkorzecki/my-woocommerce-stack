<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

/**
 * Trait with a sed like command
 * @see https://pl.wikipedia.org/wiki/Sed_(program)
 *
 * @package WPDesk\Composer\GitPlugin\Command
 */
trait SedTrait
{
    /**
     * SED.
     *
     * @param string $file_pattern .
     * @param string $pattern .
     * @param string $replace .
     *
     * @return string[] array of changed files
     */
    private function wpdeskSed($file_pattern, $pattern, $replace)
    {
        $changed_files = [];
        foreach (\glob($file_pattern) as $filename) {
            $input = \file_get_contents($filename);
            $output = \preg_replace($pattern, $replace, $input);
            if ($output !== $input) {
                $changed_files[] = $filename;
                \file_put_contents($filename, $output);
            }
        }
        return $changed_files;
    }
}
