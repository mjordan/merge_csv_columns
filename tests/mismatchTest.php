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

    protected function tearDown()
    {
        unlink($this->path_to_output_file);
    }

}
