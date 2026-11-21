<?php

namespace App\Support;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Cell\EmptyCell;
use OpenSpout\Common\Entity\Cell\NumericCell;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExport
{
    /**
     * Text is always written as plain text so values starting with "=" are never evaluated as formulas.
     *
     * @param  list<string>  $headings
     * @param  iterable<array<int, string|int|float|null>>  $rows
     */
    public static function download(string $fileName, array $headings, iterable $rows): StreamedResponse
    {
        $fileName = $fileName.'-'.now()->timezone(config('app.timezone'))->format('Y-m-d-His').'.xlsx';

        return response()->streamDownload(function () use ($headings, $rows): void {
            $writer = new Writer;
            $writer->openToFile('php://output');

            $writer->addRow(new Row(
                array_map(fn (string $heading): Cell => new StringCell($heading, null), $headings),
                (new Style)->setFontBold(),
            ));

            foreach ($rows as $row) {
                $writer->addRow(new Row(array_map(static::makeCell(...), array_values($row))));
            }

            $writer->close();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected static function makeCell(string|int|float|null $value): Cell
    {
        return match (true) {
            $value === null, $value === '' => new EmptyCell(null, null),
            is_int($value), is_float($value) => new NumericCell($value, null),
            default => new StringCell($value, null),
        };
    }
}
