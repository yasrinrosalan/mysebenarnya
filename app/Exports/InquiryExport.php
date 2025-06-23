<?php

namespace App\Exports;

use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InquiryExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Inquiry::with('category');

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('from') && $this->request->filled('to')) {
            $query->whereBetween('submitted_at', [$this->request->from, $this->request->to]);
        }

        if ($this->request->filled('category_id')) {
            $query->where('category_id', $this->request->category_id);
        }

        $inquiries = $query->get();
        return view('admin.inquiries.export_excel', compact('inquiries'));
    }
}
