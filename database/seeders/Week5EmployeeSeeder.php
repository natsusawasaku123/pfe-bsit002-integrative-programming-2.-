<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class Week5EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::firstOrCreate(['name' => 'IT']);
        $hr = Department::firstOrCreate(['name' => 'HR']);

        $employees = [
            ['Juan', 'Dela Cruz', $it->id, 'Programmer'],
            ['Maria', 'Santos', $hr->id, 'HR Assistant'],
            ['Pedro', 'Reyes', $it->id, 'Technician'],
            ['Ana', 'Lopez', $hr->id, 'Recruiter'],
            ['Carlos', 'Garcia', $it->id, 'Web Developer'],
            ['Angela', 'Reyes', $hr->id, 'HR Officer'],
            ['Jose', 'Mendoza', $it->id, 'System Analyst'],
            ['Juan', 'Ramos', $hr->id, 'Training Assistant'],
            ['Liza', 'Cruz', $it->id, 'QA Tester'],
            ['Marco', 'Torres', $hr->id, 'Payroll Assistant'],
            ['Nina', 'Diaz', $it->id, 'Support Specialist'],
            ['Paolo', 'Flores', $hr->id, 'HR Coordinator'],
        ];

        foreach ($employees as $index => $employee) {
            Employee::updateOrCreate(
                [
                    'email' =>
                        'week5.employee' . ($index + 1) . '@example.com',
                ],
                [
                    'first_name' => $employee[0],
                    'last_name' => $employee[1],
                    'department_id' => $employee[2],
                    'position' => $employee[3],
                ]
            );
        }

        $this->command->info('12 Week 5 sample employees are ready.');
        $this->command->info('IT department ID: ' . $it->id);
        $this->command->info('HR department ID: ' . $hr->id);
    }
}