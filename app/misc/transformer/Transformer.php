<?php namespace Misc\Transformers;

abstract class Transformer {

  public function transformCollection(array $records, $with = []) {
    $arr = [];
    foreach($records as $record) {
      $arr[] = empty($with)
        ? $this->transform($record)
        : array_merge($this->transformWith($record), empty($with) ? $with : $this->withTransform($record, $with));
    }
    return $arr;
  }

  public abstract function transform($record);
  public function transformInput($record) {    return $record;  } // to be overridden
  public function transformWith($record, $with=[]) {
    return array_merge($this->transform($record), empty($with) ? $with : $this->withTransform($record, $with));
  }

  /**
   * @param $name
   * @return Transformer
   */
  protected function getTransformer($name) { return null;}

  private function withTransform($record, $with = []) {
    $arr = [];
    if (!is_array($with)) { return $arr; }
    foreach($with as $join) {
      if ($transformer = $this->getTransformer($join)) {
        $arr[$join] = $transformer->transformCollection($record->$join->all(), $with);
      }
    }
    return $arr;
  }
}