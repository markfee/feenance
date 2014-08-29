<?php
use Illuminate\Http\Response;

class TestCase extends Illuminate\Foundation\Testing\TestCase {
  protected $expected_status = Response::HTTP_OK;
	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;
		$testEnvironment = 'testing';
		return require __DIR__.'/../../bootstrap/start.php';
	}

  protected function assertNoErrors($jsonResponse) {
    if ( count($jsonResponse->errors) ) {
      $str = print_r($jsonResponse->errors, true);
      $this->assertEquals(0,  count($jsonResponse->errors),    "Unexpected errors: \n{$str}");
    }
  }

  protected function assertHasErrors($jsonResponse) {
    $this->assertEquals(true,  count($jsonResponse->errors) > 0,    "Is there an error message?");
  }

  protected function assertNewRecordHasId($jsonResponse) {
    $str = "";
    if (empty($jsonResponse->data[0]->id)){
      $str = print_r($jsonResponse->data, true);
    }

    $this->assertInternalType('integer', $jsonResponse->data[0]->id, "Data is expected to contain the inserted items id:\n{$str}");
  }

  protected function assertValidJsonError($response, $expectedResponse) {
    $this->assertEquals($expectedResponse, $response->getStatusCode(), "Expected response {$expectedResponse} got " . $response->getStatusCode());

    $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    $jsonResponse = $response->getData();
    $this->assertHasErrors($jsonResponse);
    $this->assertEquals(0,    count($jsonResponse->messages),   "Are there no messages?");
    $this->assertEquals(0,    count($jsonResponse->data) > 0,   "Is the data item empty?");
    return $jsonResponse;
  }

  protected function assertValidJsonResponse($response, Array $expectedFields = null, Array $unexpectedFields = null) {
//    dd($response);
//    try {
      $jsonResponse = $response->getData();
      $this->assertNoErrors($jsonResponse);
      $this->assertEquals($this->expected_status, $response->getStatusCode(), "Expected response {$this->expected_status} got " . $response->getStatusCode());
      $this->assertEquals('application/json', $response->headers->get('Content-Type'));
      $this->assertEquals(0,  count($jsonResponse->messages),  "Are there no messages?");
      $this->assertEquals(true, count($jsonResponse->data) > 0,  "Is there at least one data item?");
      $this->assertEquals(1,  count($jsonResponse->paginator), "Is there pagination data?");
      $this->assertExpectedFields($jsonResponse->data[0], $expectedFields);
      $this->assertUnExpectedFields($jsonResponse->data[0], $unexpectedFields);
      return $jsonResponse;
//    } catch(Exception $ex) {
//      print_r($jsonResponse);
//      $this->assertEquals(true, false, $ex->getMessage());
//    }
  }

  protected function assertCallback($jsonResponse, $callback) {
    foreach($jsonResponse->data as $item) {
      $callback($item);
    }
  }

  protected function assertValidSingleRecordJsonResponse($response, Array $expectedFields = null, Array $unexpectedFields = null) {
    $jsonResponse = $response->getData();
    $this->assertEquals($this->expected_status, $response->getStatusCode(), "Expected response {$this->expected_status} got " . $response->getStatusCode());
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    $this->assertTrue(true, count($jsonResponse) == 1,  "Is there at least one data item?");
    $this->assertExpectedFields($jsonResponse, $expectedFields);
    $this->assertUnExpectedFields($jsonResponse, $unexpectedFields);
    return $jsonResponse;
  }

  protected function assertExpectedFields($record, $expectedFields) {
    if (!empty($expectedFields)) {
      foreach($expectedFields as $field) {
        $this->assertEquals(true, array_key_exists($field, $record),  "Does the data have the field: '{$field}'?");
      }
    }
  }
  protected function assertUnExpectedFields($record, $unexpectedFields) {
    if (!empty($unexpectedFields)) {
      foreach($unexpectedFields as $field) {
        $this->assertEquals(false, array_key_exists($field, $record),  "Does the data NOT have the field: '{$field}'?");
      }
    }
  }

  protected function assertValidInsertJsonResponse($response) {
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $jsonResponse = $response->getData();
    $this->assertNoErrors($jsonResponse);
    $this->assertEquals(1, count($jsonResponse->messages), "A single message is expected");
    $this->assertEquals(1, count($jsonResponse->data),     "Data is expected to be a singular array");
    $this->assertNewRecordHasId($jsonResponse);

    $this->assertResponseStatus(Response::HTTP_CREATED);
    return $jsonResponse;
  }

  protected function assertValidUpdateJsonResponse($response) {
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $jsonResponse = $response->getData();
    $this->assertNoErrors($jsonResponse);
    $this->assertEquals(1, count($jsonResponse->messages), "A single message is expected");
    $this->assertEquals(0, count($jsonResponse->data),     "No Data is expected for an update");
    $this->assertResponseStatus(Response::HTTP_OK);
    return $jsonResponse;
  }

}
