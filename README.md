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

## Usage

`php merge_csv_columns.php path/to/primary_file.csv path/to/secondary_file.csv path/to/output_file.csv`

For example,

`php merge_csv_columns.php primary.csv secondary.csv ./myoutput.csv`

## Things to note

* If there is no record in the secondary that matches a record in the primary file, the output will contain empty cells in the primary record.

## License

Public Domain.
