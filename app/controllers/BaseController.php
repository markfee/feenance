<?php
namespace Feenance\Api;
use Feenance\Misc\Transformers\Transformer;
use \Event;
use Illuminate\Support\Facades\Input;

class BaseController extends \Controller {

  protected $paginateCount = 20;

  public function __construct()  {
    $this->beforeFilter(function()
    {
      Event::fire('clockwork.controller.start');
    });

    $this->afterFilter(function()
    {
      Event::fire('clockwork.controller.end');
    });

    if (\Input::get("perPage")) {
      $this->paginateCount = \Input::get("perPage");
    }
  }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

  /**
   * @var Transformer
   */
  protected $transformer = null;

  /**
   * @return Transformer
   */
  protected function getTransformer() {
    return null;
  }

  /**
   * @param $record
   * @return mixed
   */
  protected function transform($record) {
    $transform = $this->getTransformer();
    return $transform ? $transform->transform($record) : $record;
  }

  /**
   * @param $record
   * @return mixed
   */
  protected function transformInput($record) {
    $transform = $this->getTransformer();
    return $transform ? $transform->transformInput($record) : $record;
  }

  /**
   * @param $record
   * @return mixed
   */
  protected function transformCollection($record) {
    $transform = $this->getTransformer();
    return $transform ? $transform->transformCollection($record) : $record;
  }

}
