#!/usr/bin/php
<?php

/**
 * Script to merge two CSV files.
 *
 * Usage php merge_csv_columns.php -p path/to/primary_file.csv -s path/to/secondary_file.csv -o path/to/output_file.csv
 *
 *  Example: php merge_csv_columns.php -p primary.csv -s secondary.csv -o ./myoutput.csv
 *
 * You can also specify a delimiter with '-d'. The default is ',', and if you want to use a tab, specify 't':
 *
 *  Example: php merge_csv_columns.php -p primary.tsv -s secondary.tsv -o ./myoutput.tsv -d t
 *
 *
 * where records in the secondary_file.csv will be merged into the corresponding records
 * in primary_file.csv. Both files must have a common field in their first column; this field
 * is used as the key to combine the data. This field must be the first column in both files.
 */

use League\Csv\Reader;
use League\Csv\Writer;
require 'vendor/autoload.php';

$cmd = new Commando\Command();

$cmd->option('p')
    ->aka('primary')
    ->require(true)
    ->describedAs('Ablsolute or relative path to the primary CSV file.')
    ->must(function ($primary_path) {
        if (file_exists($primary_path)) {
            return true;
        } else {
            return false;
        }
    });
$cmd->option('s')
    ->aka('secondary')
    ->require(true)
    ->describedAs('Ablsolute or relative path to the secondary CSV file.')
    ->must(function ($secondary_path) {
        if (file_exists($secondary_path)) {
            return true;
        } else {
            return false;
        }
    });
$cmd->option('o')
    ->aka('output')
    ->require(true)
    ->describedAs('Ablsolute or relative path to the output CSV file.');
$cmd->option('d')
    ->aka('delimiter')
    ->default(',')
    ->describedAs('Field delimiter. Defaults to a comma (,).');

$delimiter = ($cmd['delimiter'] == 't') ? "	" : $cmd['delimiter'];

var_dump($delimiter);

$primary_reader = Reader::createFromPath($cmd['primary'], 'r');
$primary_reader->setDelimiter($delimiter);
$secondary_reader = Reader::createFromPath($cmd['secondary'], 'r');
$secondary_reader->setDelimiter($delimiter);
$output_file = $cmd['output'];

// Prepare the combined output file.
$writer = Writer::createFromPath(new SplFileObject($output_file, 'a+'), 'w');
$writer->setDelimiter($delimiter);

/**
 * Read the secondary CSV file into memory and prepare it for merging
 * into the primary CSV file.
 */
$secondary_records_for_merging = array();
$secondary_headers = $secondary_reader->fetchOne();
$id_field = $secondary_headers[0];
// We need the size of the secondary CSV records to fill in
// data in the merged file for rows that are missing from
// secondary file.
$size_of_secondary_records = count($secondary_headers);
// Get rid of the secondary CSV data header record.
array_shift($secondary_headers);
$secondary_records = $secondary_reader->fetch();
// Remove the first field, which is the ID field.
foreach ($secondary_records as $secondary_record) {
  if ($secondary_record[0] != $id_field) {
    // Add these to an array with the $id_field as the key
    $secondary_records_for_merging[$secondary_record[0]] = array_slice($secondary_record, 1);
  }
}

/**
 * Read the primary CSV file into memory and prepare it and merge in
 * the secondary CSV data.
 */
$primary_headers = $primary_reader->fetchOne();
$primary_records = $primary_reader->fetch();
$writer->insertOne(array_merge($primary_headers, $secondary_headers));

$matching_records = 0;
$nonmatching_records = 0;

foreach ($primary_records as $primary_record) {
  if ($primary_record[0] == $id_field) {
    unset($primary_record);
  }
  else {
    print "Processing primary CSV record with ID " . $primary_record[0] . "...";
    // Look for a secondary CSV record that has shares its ID with the current
    // primary CSV record so we can merge the two records.
    if (array_key_exists($primary_record[0], $secondary_records_for_merging)) {
      // Merge the primary and secondary CSV records for adding to the output file.
      $merged_record = array_merge($primary_record, $secondary_records_for_merging[$primary_record[0]]);
      print "found matching secondary record.\n";
      $matching_records++;
    }
    // If there isn't a secondary CSV record, fill in the data with empty cells.
    else {
      $placeholder_secondary_record = array_fill(0, $size_of_secondary_records, '');
      $merged_record = array_merge($primary_record, $placeholder_secondary_record);
      print "did not find matching secondary record, adding placeholders.\n";
      $nonmatching_records++;
    }
    $writer->insertOne($merged_record);
  }
}

/**
 * Print some useful information.
 */
print "Merged $matching_records primary and secondary records.\nDetected $nonmatching_records primary records that did not have matching secondary records.\nFile containing merged records is at " . realpath($output_file) . ".\n";
