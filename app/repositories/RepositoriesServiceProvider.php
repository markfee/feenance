<?php namespace Feenance\repositories;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider{

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->app->bind('feenance.repositories.eloquent_account_repository', function()
    {
      return new EloquentAccountRepository;
    });
  }
}