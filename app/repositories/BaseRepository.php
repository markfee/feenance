<?php namespace Feenance\repositories;

use Markfee\Responder\TransformerInterface;
use Feenance\Misc\Transformers\TransformableTrait;

abstract class BaseRepository implements RepositoryInterface{

  use TransformableTrait;

  function __construct(TransformerInterface $transformer) {
    $this->transformer = $transformer;
  }

}