<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersReportExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::query();

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['role'])) {
            $query->where('role', $this->filters['role']);
        }

        if (!empty($this->filters['agency_id'])) {
            $query->where('id', $this->filters['agency_id']);
        }

        return $query->select('user_id as id', 'name', 'email', 'role', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Role', 'Created At'];
    }
}
