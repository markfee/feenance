<?php namespace Feenance\repositories\facades;

use Illuminate\Support\Facades\Facade;

class AccountRepository extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    dd("yayyayyayyay");
    return    'feenance.repositories.eloquent_account_repository';
  }

}