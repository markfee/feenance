<?php namespace Feenance\controllers\Api;

use \Event;
use Illuminate\Support\Facades\Input;
use Feenance\Misc\Transformers\TransformableTrait;


class BaseController extends \Controller {

  protected $paginateCount = 20;
  use TransformableTrait;

  public function __construct()  {
    $this->beforeFilter(function()
    {
      Event::fire('clockwork.controller.start');
    });

    $this->afterFilter(function()
    {
      Event::fire('clockwork.controller.end');
    });
    $perPage = \Input::get("perPage");
    if ($perPage && is_numeric($perPage)) {
      $this->paginateCount = $perPage;
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

}
