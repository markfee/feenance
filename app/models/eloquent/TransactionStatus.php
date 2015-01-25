<?php namespace Feenance\models\eloquent;


class TransactionStatus extends \Eloquent {
  const UNRECONCILED  = 1;
  const RECONCILED    = 2;
  const EXPECTED_STANDING_ORDER = 3;
  const PLANNED_DEFINITE = 4;
  const PLANNED_POSSIBLE = 5;
}