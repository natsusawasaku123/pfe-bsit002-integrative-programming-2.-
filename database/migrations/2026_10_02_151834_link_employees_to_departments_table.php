<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the new column before transferring existing data.
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable();
        });

        // Convert existing department names into department records.
        $names = DB::table('employees')
            ->distinct()
            ->pluck('department');

        foreach ($names as $name) {
            $departmentId = DB::table('departments')
                ->where('name', $name)
                ->value('id');

            if ($departmentId === null) {
                $departmentId = DB::table('departments')->insertGetId([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('employees')
                ->where('department', $name)
                ->update(['department_id' => $departmentId]);
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')
                ->nullable(false)
                ->change();

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->cascadeOnDelete();

            $table->dropColumn('department');
        });
    }

    public function down(): void
    {
        // Restore department names if this migration is rolled back.
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable();
        });

        $employees = DB::table('employees')
            ->join(
                'departments',
                'employees.department_id',
                '=',
                'departments.id'
            )
            ->select('employees.id', 'departments.name')
            ->get();

        foreach ($employees as $employee) {
            DB::table('employees')
                ->where('id', $employee->id)
                ->update(['department' => $employee->name]);
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->string('department')->nullable(false)->change();
        });
    }
};