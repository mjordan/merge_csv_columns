#!/usr/bin/php
<?php

/**
 * Script to merge two CSV files.
 *
 * Usage php merge_csv_columns.php path/to/primary_file.csv path/to/secondary_file.csv path/to/output_file.csv
 *
 *  Example: php merge_csv_columns.php primary.csv secondary.csv ./myoutput.csv
 *
 * where records in the secondary_file.csv will be merged into the corresponding records
 * in primary_file.csv. Both files must have a common field in their first column; this field
 * is used as the key to combine the data. This field must be the first column in both files.
 */

use League\Csv\Reader;
use League\Csv\Writer;
require 'vendor/autoload.php';

$primary_reader = Reader::createFromPath(trim($argv[1]), 'r');
$secondary_reader = Reader::createFromPath(trim($argv[2]), 'r');
$output_file = trim($argv[3]);

// Prepare the combined output file.
$writer = Writer::createFromPath(new SplFileObject($output_file, 'a+'), 'w');

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
