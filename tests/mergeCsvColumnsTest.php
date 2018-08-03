<?php
require 'vendor/autoload.php';
use League\Csv\Reader;

class mergeCsvColumnsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->asset_base_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $this->path_to_output_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "testoutput.csv";
    }

    public function testWriteRead()
    {
        exec('php merge_csv_columns.php ' .
          $this->asset_base_dir .
          '/primary.csv ' .
          $this->asset_base_dir .
          '/secondary.csv ' .
          $this->path_to_output_file
        );

        $reader = Reader::createFromPath($this->path_to_output_file, 'r');
        $data = $reader->fetchColumn(4);
        $this->assertEquals('1999', $data[4]);
    }

    protected function tearDown()
    {
        // unlink($this->path_to_output_file);
    }

}
