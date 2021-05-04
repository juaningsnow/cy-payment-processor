<?php

namespace BaseCode\Common\Utils;

use Illuminate\Http\Request;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Model;

class RepoExporter implements FromCollection, Responsable
{
    use Exportable;

    protected $model;
    protected $fileName;
    protected $fields;

    public function __construct(Model $model, array $fields, $fileName)
    {
        $this->model = $model;
        $this->fields = array_map(function ($field) {
            return Str::snake($field);
        }, $fields);
        $this->fileName = $fileName;
    }

    public function collection()
    {
        $items = $this->model->all()->map(function ($model) {
            $temp = Arr::only(Arr::dot($model->toArray()), $this->fields);
            // https://stackoverflow.com/questions/348410/sort-an-array-by-keys-based-on-another-array
            return array_replace(array_flip($this->fields), $temp);
        });
        $items->prepend(array_map(function ($field) {
            return $this->formatHeading($field);
        }, $this->fields));
        return $items;
    }

    protected function formatHeading($value)
    {
        return strtoupper(str_replace(['_', '.'], ' ', $value));
    }
}
