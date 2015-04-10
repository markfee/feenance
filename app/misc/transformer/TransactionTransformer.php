<?php namespace Feenance\Misc\Transformers;

use Markfee\Responder\Transformer;
use Markfee\Responder\TransformerInterface;

class TransactionTransformer extends Transformer implements TransformerInterface
{

    private function transformAmount($account, $amount, $transfer)
    {
        return $account ? ["account_id" => $account, "amount" => 0.01 * $amount, "transfer_id" => $transfer] : null;
    }

    public static function transform($record)
    {
        return [
            "id" => (int)$record->id,
            "date" => $record->date->toISO8601String(),
            "amount" => 0.01 * $record->amount,
            "account_id" => $record->account_id,
            "balance" => $record->balance ? 0.01 * $record->balance->balance : null,
            "reconciled" => $record->reconciled,
            "status" => $record->status ? $record->status->code : null,
            "payee_id" => $record->payee_id,
            "standing_order_id" => $record->standing_order_id,
            "category_id" => $record->category_id,
            "notes" => $record->notes ?: null,
            "source" => $record->source
                ? [
                    "transaction_id" => $record->source->source,
                    "account_id" => $record->source->source_account->account_id
                ]
                : null,
            "destination" => $record->destination
                ? [
                    "transaction_id" => $record->destination->destination,
                    "account_id" => $record->destination->destination_account->account_id
                ]
                : null,

            "bank_balance" => $record->bank_balance ? 0.01 * $record->bank_balance : null,
            "bank_string_id" => $record->bank_string_id ? (int)$record->bank_string_id : null,
            "bank_string" => $record->bankString ? $record->bankString->name : null,
            "payee" => $record->payee_id ? PayeeTransformer::transform($record->payee) : null,
            "category" => $record->category_id ? CategoryTransformer::transform($record->category) : null,
            "batch_id" => $record->batch_id,
        ];
    }

    public static function transformInput($record)
    {
//    dd($record["amount"]);
        if (isset($record["amount"])) $record["amount"] *= 100;
        if (isset($record["date"])) {
            $record["date"] = substr($record["date"], 0, 10);
        }
        return $record;
    }
}