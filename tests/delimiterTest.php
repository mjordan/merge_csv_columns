<?php
require 'vendor/autoload.php';
use League\Csv\Reader;

class delimiterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->asset_base_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $this->path_to_output_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "testoutput.sv";
    }

    public function testBasicMerge()
    {
        exec('php merge_csv_columns.php ' .
          '-p ' . $this->asset_base_dir .
          '/primary.csv ' .
          '-s ' . $this->asset_base_dir .
          '/secondary.csv ' .
          '-o ' . $this->path_to_output_file
        );

        $reader = Reader::createFromPath($this->path_to_output_file, 'r');
        $data = $reader->fetchColumn(4);
        $this->assertEquals('1999', $data[4]);
    }

    public function testMergeWithTab()
    {
        exec('php merge_csv_columns.php ' .
          '-p ' . $this->asset_base_dir .
          '/primary.tsv ' .
          '-s ' . $this->asset_base_dir .
          '/secondary.tsv ' .
          '-o ' . $this->path_to_output_file .
          ' -d t'
        );

        $reader = Reader::createFromPath($this->path_to_output_file, 'r');
        $reader->setDelimiter('	');
        $data = $reader->fetchColumn(4);
        $this->assertEquals('2017', $data[4]);
    }

    public function testMergeWithPipe()
    {
        exec('php merge_csv_columns.php ' .
          '-p ' . $this->asset_base_dir .
          '/primary.psv ' .
          '-s ' . $this->asset_base_dir .
          '/secondary.psv ' .
          '-o ' . $this->path_to_output_file .
          ' -d \|'
        );

        $reader = Reader::createFromPath($this->path_to_output_file, 'r');
        $reader->setDelimiter('|');
        $data = $reader->fetchColumn(4);
        $this->assertEquals('2018', $data[4]);
    }

    protected function tearDown()
    {
        unlink($this->path_to_output_file);
    }

}
