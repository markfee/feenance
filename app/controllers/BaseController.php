<?php
namespace api;
use Misc\Transformers\Transformer;
class BaseController extends \Controller {

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
  protected function transformCollection($record) {
    $transform = $this->getTransformer();
    return $transform ? $transform->transformCollection($record) : $record;
  }

}
