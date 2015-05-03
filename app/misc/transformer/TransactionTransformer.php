<?php namespace Feenance\Misc\Transformers;

use Feenance\services\Currency\Currency;
use Markfee\Responder\Transformer;
use Markfee\Responder\TransformerInterface;

class TransactionTransformer extends Transformer implements TransformerInterface
{
    public static function transform($record)
    {
        $currencyConverter = Currency::createMainConverter($record->currency_code);

        return [
            "id" => (int)$record->id,
            "date" => $record->date->toISO8601String(),
            "amount" => $currencyConverter->convert($record->amount),
            "account_id" => $record->account_id,
            "balance" => $record->balance ? $currencyConverter->convert($record->balance->balance) : null,
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

            "bank_balance" => $currencyConverter->convert($record->bank_balance),
            "bank_string_id" => $record->bank_string_id ? (int)$record->bank_string_id : null,
            "bank_string" => $record->bankString ? $record->bankString->name : null,
            "payee" => $record->payee_id ? PayeeTransformer::transform($record->payee) : null,
            "category" => $record->category_id ? CategoryTransformer::transform($record->category) : null,
            "batch_id" => $record->batch_id,
            "currency_code" => Currency::get_main_unit($record->currency_code),
        ];
    }

    public static function transformInput($record)
    {
        $currencyCode = isset($record["currency_code"]) ? $record["currency_code"] : null;
        $currencyConverter = Currency::createSubConverter($currencyCode);

        $record["transformed"] = true; // Stop transforming input twice;
        if (isset($record["amount"])) {
            $record["amount"] =  $currencyConverter->convert($record["amount"]);
        }
        if (isset($record["date"])) {
            $record["date"] = substr($record["date"], 0, 10);
        }
        if (isset($record["balance"]) && is_numeric($record["balance"])) {
            $record["bank_balance"] = $record["balance"];
        }
        if (isset($record["bank_balance"]) && is_numeric($record["bank_balance"])) {
            $record["bank_balance"] =  $currencyConverter->convert($record["bank_balance"]);
        }
        $record["currency_code"] = Currency::get_sub_unit($currencyCode);
        return $record;
    }
}