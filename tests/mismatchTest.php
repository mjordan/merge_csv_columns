<?php
require 'vendor/autoload.php';
use League\Csv\Reader;

class mismatchTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->asset_base_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $this->path_to_output_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "testoutput.sv";
    }

    public function testMismatches()
    {
        exec('php merge_csv_columns.php ' .
          '-p ' . $this->asset_base_dir .
          '/random_first_15.csv ' .
          '-s ' . $this->asset_base_dir .
          '/random_last_5_partial.csv ' .
          '-o ' . $this->path_to_output_file, $output
        );

        $this->assertRegExp('/Detected 10 primary records/', $output[53]);
    }

    public function testMerge()
    {
        exec('php merge_csv_columns.php ' .
          '-p ' . $this->asset_base_dir .
          '/random_first_15.csv ' .
          '-s ' . $this->asset_base_dir .
          '/random_last_5_partial.csv ' .
          '-o ' . $this->path_to_output_file
        );


        $reader = Reader::createFromPath($this->path_to_output_file, 'r');
        $data_20 = $reader->fetchColumn(20);
        $this->assertEquals('899693', $data_20[51]);

        $data_16 = $reader->fetchColumn(16);
        $this->assertEquals('', $data_16[49]);
        $data_17 = $reader->fetchColumn(17);
        $this->assertEquals('', $data_17[49]);
        $data_17 = $reader->fetchColumn(17);
        $this->assertEquals('', $data_17[49]);
        $data_18 = $reader->fetchColumn(18);
        $this->assertEquals('', $data_18[49]);
        $data_19 = $reader->fetchColumn(19);
        $this->assertEquals('', $data_19[49]);
        $this->assertEquals('', $data_20[49]);
    }

    protected function tearDown()
    {
        unlink($this->path_to_output_file);
    }

}
