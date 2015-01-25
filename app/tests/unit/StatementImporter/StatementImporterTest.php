<?php namespace Feenance\tests\unit\StatementImporter;
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 22/10/14
 * Time: 07:03
 */

use Feenance\tests\TestCase;
use Feenance\Services\StatementImporter;

class StatementImporterTest extends TestCase {

  public function test_exists() {
    $file = new \SplFileObject(base_path() . "/app/tests/unit/StatementImporter/test_firstdirect.csv", "r");
    $file = new \SplFileObject(base_path() . "/app/tests/unit/StatementImporter/test_tesco.csv", "r");
    $statementImport = new StatementImporter();
  }

  public function test_will_import_a_file_to_an_account() {
    $file = new \SplFileObject(base_path() . "/app/tests/unit/StatementImporter/test_tesco.csv", "r");
    $statementImport = new StatementImporter(1, $file);
//    $this->assertTrue($statementImport)

  }


};