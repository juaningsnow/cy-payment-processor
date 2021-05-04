<?php

namespace BaseCode\Common\Utils;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Exports implements FromView
{
    private $view;
    private $data;

    public function __construct($data, $view)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        $data = $this->data;
        return view($this->view, compact('data'));
    }
}
