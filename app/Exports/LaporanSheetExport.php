<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanSheetExport implements FromView, ShouldAutoSize, WithTitle
{
    private $viewName;
    private $data;
    private $title;

    public function __construct(string $viewName, array $data, string $title)
    {
        $this->viewName = $viewName;
        $this->data = $data;
        $this->title = $title;
    }

    public function view(): View
    {
        return view($this->viewName, $this->data);
    }

    public function title(): string
    {
        return $this->title;
    }
}