<?php namespace Feenance\Misc\Transformer;

use Markfee\Responder\Transformer;

interface TransformableInterface {

  /**
   * @return Transformer
   */
  function getTransformer();


}