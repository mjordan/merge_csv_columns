# Merge CSV Columns

Script to merge two CSV files. Records in the secondary CSV file will be merged into the corresponding records in the primary CSV file. Both files must have a common field in their first column; this field is used as the key to combine the data. This field must be the first column in both files.

For example, if your primary CSV file is:

```
ID,Title,Author,Publisher
1,How to write a book,"Jordan, Mark",Freeby Press
2,How to parse a CSV,"Jordanski, Marvin",Parse Press
3,How to cook an egg,"Eggansku, Vlad",Egg Publishers
4,How to come up with ideas,"Fizbar, Ida",Nonce Publications

```

and you want to combine the CSV data in this secondary file:

```
ID,Date,"Number of pages"
1,,234
2,2012,45
4,1999,,,,
```

you will get this output:

```
ID,Title,Author,Publisher,Date,"Number of pages"
1,"How to write a book","Jordan, Mark","Freeby Press",,234
2,"How to parse a CSV","Jordanski, Marvin","Parse Press",2012,45
3,"How to cook an egg","Eggansku, Vlad","Egg Publishers",,,
4,"How to come up with ideas","Fizbar, Ida","Nonce Publications",1999,,,,
```

The `ID` column links the data in the two files.

## Requirements

* PHP 5.6.0 or higher
* [composer](https://getcomposer.org/)

## Installation

1. `git https://github.com/mjordan/merge_csv_columns.git`
1. `cd merge_csv_columns`
1. `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

## Usage

`php merge_csv_columns.php path/to/primary_file.csv path/to/secondary_file.csv path/to/output_file.csv`

For example,

`php merge_csv_columns.php primary.csv secondary.csv ./myoutput.csv`

Running this command will result in the following:

```
Processing primary CSV record with ID 1...found matching secondary record.
Processing primary CSV record with ID 2...found matching secondary record.
Processing primary CSV record with ID 3...did not find matching secondary record, adding placeholders.
Processing primary CSV record with ID 4...found matching secondary record.
Merged 3 primary and secondary records.
Detected 1 primary records that did not have matching secondary records.
File containing merged records is at /home/mark/hacking/merge_csv_columns/myoutput.csv.
```

## Things to note

* If there is no record in the secondary that matches a record in the primary file, the output will contain empty cells in the primary record (as illustrated in row 3 of the sample output CSV shown above).

## License

Public Domain.
